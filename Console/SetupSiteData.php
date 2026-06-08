<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use JsonException;
use Throwable;
use Ycore\Models\Category;
use Ycore\Models\CategoryRoute;
use Ycore\Models\Page;

class SetupSiteData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetupSiteData {file=yycms-site-data.json} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '上线前设置网站数据';

    private array $siteKeys = [
        'site_name',
        'icp',
        'domain',
        'm_domain',
        'seo_title',
        'seo_keyword',
        'seo_desc',
    ];

    private array $categoryKeys = [
        'seo_title',
        'seo_keywords',
        'seo_description',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = (string)$this->argument('file');
        $path = base_path($file);
        $dryRun = (bool)$this->option('dry-run');

        if (!file_exists($path)) {
            $this->error("配置文件不存在：{$path}");
            return 1;
        }

        try {
            $data = $this->readJsonFile($path);
            $this->validateRoot($data);
            $this->validateCategoryData($data['categories'] ?? []);
            $this->validatePageData($data['pages'] ?? []);

            $summary = $this->applyData($data, $dryRun);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return 1;
        }

        if ($dryRun) {
            $this->warn('当前为 dry-run 模式，未写入数据库，未刷新分类路由。');
        }

        $this->info("网站设置：{$summary['site']} 项");
        $this->info("分类更新：{$summary['categories']} 个，跳过下架分类：{$summary['skipped_categories']} 个");
        $this->info("路由更新：{$summary['routes']} 条");
        $this->info("单页面更新：{$summary['pages']} 个");

        return 0;
    }

    /**
     * @throws JsonException
     */
    private function readJsonFile(string $path): array
    {
        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException("配置文件读取失败：{$path}");
        }

        $json = $this->stripJsonComments($content);
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new \RuntimeException('配置文件根节点必须是 JSON object。');
        }

        return $data;
    }

    private function stripJsonComments(string $content): string
    {
        $result = '';
        $length = strlen($content);
        $inString = false;
        $escape = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $content[$i];
            $next = $i + 1 < $length ? $content[$i + 1] : '';

            if ($inString) {
                $result .= $char;

                if ($escape) {
                    $escape = false;
                    continue;
                }

                if ($char === '\\') {
                    $escape = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($char === '"') {
                $inString = true;
                $result .= $char;
                continue;
            }

            if ($char === '/' && $next === '/') {
                $i += 2;

                while ($i < $length && $content[$i] !== "\n") {
                    $i++;
                }

                if ($i < $length) {
                    $result .= "\n";
                }

                continue;
            }

            if ($char === '/' && $next === '*') {
                $i += 2;

                while ($i < $length) {
                    if ($content[$i] === "\n") {
                        $result .= "\n";
                    }

                    if ($content[$i] === '*' && $i + 1 < $length && $content[$i + 1] === '/') {
                        $i++;
                        break;
                    }

                    $i++;
                }

                continue;
            }

            $result .= $char;
        }

        return $result;
    }

    private function validateRoot(array $data): void
    {
        foreach (['site', 'pages'] as $key) {
            if (isset($data[$key]) && !is_array($data[$key])) {
                throw new \RuntimeException("{$key} 必须是 JSON object。");
            }
        }

        if (isset($data['categories']) && !is_array($data['categories'])) {
            throw new \RuntimeException('categories 必须是 JSON array。');
        }
    }

    private function validateCategoryData(array $categories): void
    {
        foreach ($categories as $index => $item) {
            $label = "categories[{$index}]";

            if (!is_array($item)) {
                throw new \RuntimeException("{$label} 必须是 JSON object。");
            }

            if (!isset($item['id']) || !is_numeric($item['id'])) {
                throw new \RuntimeException("{$label}.id 必须是数字。");
            }

            if (!isset($item['routes'])) {
                continue;
            }

            if (!is_array($item['routes'])) {
                throw new \RuntimeException("{$label}.routes 必须是 JSON array。");
            }

            foreach ($item['routes'] as $routeIndex => $routeItem) {
                $routeLabel = "{$label}.routes[{$routeIndex}]";

                if (!is_array($routeItem)) {
                    throw new \RuntimeException("{$routeLabel} 必须是 JSON object。");
                }

                if (!isset($routeItem['route_id']) || !is_numeric($routeItem['route_id'])) {
                    throw new \RuntimeException("{$routeLabel}.route_id 必须是数字。");
                }

                if (!array_key_exists('route', $routeItem) || trim((string)$routeItem['route']) === '') {
                    throw new \RuntimeException("{$routeLabel}.route 不能为空。");
                }

                if (str_starts_with((string)$routeItem['route'], '/')) {
                    throw new \RuntimeException("{$routeLabel}.route 不应以 / 开头。");
                }
            }
        }
    }

    private function validatePageData(array $pages): void
    {
        foreach (['about', 'contact'] as $key) {
            if (array_key_exists($key, $pages) && !is_string($pages[$key])) {
                throw new \RuntimeException("pages.{$key} 必须是字符串。");
            }
        }
    }

    private function applyData(array $data, bool $dryRun): array
    {
        $summary = [
            'site' => 0,
            'categories' => 0,
            'skipped_categories' => 0,
            'routes' => 0,
            'pages' => 0,
        ];

        $routeChanged = false;

        DB::transaction(function () use ($data, $dryRun, &$summary, &$routeChanged) {
            $summary['site'] = $this->applySiteData($data['site'] ?? [], $dryRun);

            $categorySummary = $this->applyCategoryData($data['categories'] ?? [], $dryRun);
            $summary['categories'] = $categorySummary['categories'];
            $summary['skipped_categories'] = $categorySummary['skipped_categories'];
            $summary['routes'] = $categorySummary['routes'];
            $routeChanged = $categorySummary['routes'] > 0;

            $summary['pages'] = $this->applyPageData($data['pages'] ?? [], $dryRun);
        });

        if ($routeChanged && !$dryRun) {
            Artisan::call('CreateRoute');
            $this->info('已刷新分类路由：CreateRoute');
        }

        return $summary;
    }

    private function applySiteData(array $site, bool $dryRun): int
    {
        $count = 0;

        foreach ($this->siteKeys as $key) {
            if (!array_key_exists($key, $site)) {
                continue;
            }

            $value = $site[$key];
            $this->line($this->formatAction($dryRun, "设置网站配置 {$key} = {$value}"));

            if (!$dryRun) {
                setOption($key, $value, true);
            }

            $count++;
        }

        return $count;
    }

    private function applyCategoryData(array $categories, bool $dryRun): array
    {
        $summary = [
            'categories' => 0,
            'skipped_categories' => 0,
            'routes' => 0,
        ];

        foreach ($categories as $item) {
            $categoryId = (int)$item['id'];
            $category = Category::withoutGlobalScope('statusScope')->where('id', $categoryId)->first();

            if (!$category) {
                throw new \RuntimeException("分类不存在：{$categoryId}");
            }

            if ((int)$category->status === 0) {
                $this->warn("跳过已下架分类：{$categoryId} {$category->name}");
                $summary['skipped_categories']++;
                continue;
            }

            $updates = [];

            foreach ($this->categoryKeys as $key) {
                if (array_key_exists($key, $item)) {
                    $updates[$key] = $item[$key];
                }
            }

            if ($updates) {
                $this->line($this->formatAction($dryRun, "更新分类 {$categoryId} {$category->name} SEO"));

                if (!$dryRun) {
                    $category->fill($updates);
                    $category->save();
                }

                $summary['categories']++;
            }

            foreach ($item['routes'] ?? [] as $routeItem) {
                $summary['routes'] += $this->applyCategoryRoute($categoryId, $routeItem, $dryRun);
            }
        }

        return $summary;
    }

    private function applyCategoryRoute(int $categoryId, array $routeItem, bool $dryRun): int
    {
        $routeId = (int)$routeItem['route_id'];
        $route = CategoryRoute::where('id', $routeId)->first();

        if (!$route) {
            throw new \RuntimeException("分类路由不存在：{$routeId}");
        }

        if ((int)$route->category_id !== $categoryId) {
            throw new \RuntimeException("分类路由 {$routeId} 不属于分类 {$categoryId}。");
        }

        $newRoute = trim((string)$routeItem['route']);
        $this->line($this->formatAction($dryRun, "更新分类 {$categoryId} 路由 {$routeId}: {$route->route} => {$newRoute}"));

        if (!$dryRun) {
            $route->route = $newRoute;
            $route->save();
        }

        return 1;
    }

    private function applyPageData(array $pages, bool $dryRun): int
    {
        $map = [
            'about' => [
                'title' => '关于我们',
                'route' => 'about',
            ],
            'contact' => [
                'title' => '联系我们',
                'route' => 'call',
            ],
        ];
        $count = 0;

        foreach ($map as $key => $page) {
            if (!array_key_exists($key, $pages)) {
                continue;
            }

            $this->line($this->formatAction($dryRun, "更新单页面 {$page['title']} route={$page['route']}"));

            if (!$dryRun) {
                Page::updateOrCreate(
                    ['route' => $page['route']],
                    [
                        'title' => $page['title'],
                        'route' => $page['route'],
                        'content' => $pages[$key],
                    ]
                );
            }

            $count++;
        }

        return $count;
    }

    private function formatAction(bool $dryRun, string $message): string
    {
        return ($dryRun ? '[dry-run] ' : '') . $message;
    }
}
