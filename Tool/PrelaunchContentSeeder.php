<?php

namespace Ycore\Tool;

use Illuminate\Support\Facades\Date;
use RuntimeException;
use Throwable;
use Ycore\Models\Article;
use Ycore\Models\Category;

class PrelaunchContentSeeder
{
    public const MARKER_PREFIX = 'yycms-prelaunch-content:v1';

    private const TARGETS = [
        'rank' => 4,
        'game_collect' => 10,
        'app_collect' => 10,
    ];

    private const RELATED_ARTICLE_LIMITS = [
        'rank' => 10,
        'game_collect' => 2,
        'app_collect' => 2,
    ];

    private const COLLECT_CATEGORY_MINIMUM_ARTICLES = 2;

    private PrelaunchContentCatalog $catalog;

    public function __construct(?PrelaunchContentCatalog $catalog = null)
    {
        $this->catalog = $catalog ?: new PrelaunchContentCatalog();
    }

    /**
     * @throws Throwable
     */
    public function run(bool $dryRun = false, bool $append = false, ?string $seed = null, ?string $batch = null): array
    {
        $plan = $this->buildPlan($append, $seed, $batch);
        $created = 0;

        if (!$dryRun) {
            foreach ($plan['items'] as $key => $item) {
                $article = $this->createArticle($item);
                $plan['items'][$key]['created_id'] = $article->id;
                $plan['items'][$key]['img'] = (string)$article->img;
                $created++;
            }
        }

        $plan['dry_run'] = $dryRun;
        $plan['created'] = $created;

        return $plan;
    }

    public function buildPlan(bool $append = false, ?string $seed = null, ?string $batch = null): array
    {
        $seed = $seed ?: $this->defaultSeed();
        $batch = $batch ?: date('YmdHis');

        $items = array_merge(
            $this->rankPlans($seed, $append, $batch),
            $this->collectPlans('game_collect', $seed, $append, $batch),
            $this->collectPlans('app_collect', $seed, $append, $batch)
        );

        return [
            'seed' => $seed,
            'batch' => $batch,
            'append' => $append,
            'planned' => count($items),
            'target' => array_sum(self::TARGETS),
            'items' => $items,
        ];
    }

    public static function targetSlots(string $type, int $target, array $existingSlots, bool $append): array
    {
        $slots = range(1, $target);

        if ($append) {
            return $slots;
        }

        $existingSlots = array_map('intval', $existingSlots);

        return array_values(array_filter($slots, fn (int $slot): bool => !in_array($slot, $existingSlots, true)));
    }

    public static function deterministicTake(array $items, int $limit, string $seed, string $salt): array
    {
        $decorated = [];

        foreach (array_values($items) as $index => $item) {
            $decorated[] = [
                'key' => hash('sha256', $seed . '|' . $salt . '|' . $index . '|' . self::itemStableKey($item)),
                'item' => $item,
            ];
        }

        usort($decorated, fn (array $a, array $b): int => strcmp($a['key'], $b['key']));

        return array_slice(array_column($decorated, 'item'), 0, $limit);
    }

    public static function relatedArticleLimit(string $type): int
    {
        return self::RELATED_ARTICLE_LIMITS[$type] ?? 10;
    }

    public static function collectCategoryMinimumArticleCount(): int
    {
        return self::COLLECT_CATEGORY_MINIMUM_ARTICLES;
    }

    public static function collectCategoryPlansForSlots(array $slots, array $eligibleCategories, int $target, string $seed, string $type): array
    {
        $selectedCategories = self::deterministicTake($eligibleCategories, $target, $seed, "{$type}:categories");
        $plans = [];

        foreach ($slots as $slot) {
            $slot = (int)$slot;
            $category = $selectedCategories[$slot - 1] ?? null;

            if ($category === null) {
                continue;
            }

            $plans[] = [
                'slot' => $slot,
                'category' => $category,
            ];
        }

        return $plans;
    }

    public static function makeUniqueTitle(string $title, int $categoryId, string $batch, callable $titleExists): string
    {
        $title = trim($title);

        if (!$titleExists($title, $categoryId)) {
            return $title;
        }

        $suffix = substr($batch, -6);

        for ($i = 1; $i <= 99; $i++) {
            $candidate = $title . ' ' . $suffix . ($i > 1 ? '-' . $i : '');

            if (!$titleExists($candidate, $categoryId)) {
                return $candidate;
            }
        }

        throw new RuntimeException("无法生成唯一标题：{$title}");
    }

    public static function marker(string $type, int $slot, bool $append, string $batch): string
    {
        $marker = '<!-- ' . self::MARKER_PREFIX . " {$type}:{$slot}";

        if ($append) {
            $marker .= " append:{$batch}";
        }

        return $marker . ' -->';
    }

    public static function selectPrimaryImage(
        array $articles,
        string $fallbackImage,
        string $seed,
        string $salt,
        ?callable $imageUsable = null,
        string $finalFallbackImage = '',
        string $uploadPrefix = 'uploads'
    ): string
    {
        $images = [];

        foreach ($articles as $article) {
            $img = self::normalizePrimaryImage((string)($article['img'] ?? ''), $uploadPrefix);

            if ($img !== '' && self::primaryImageIsUsable($img, $imageUsable)) {
                $images[] = $img;
            }
        }

        $images = array_values(array_unique($images));

        if ($images) {
            return self::deterministicTake($images, 1, $seed, $salt)[0];
        }

        foreach ([$fallbackImage, $finalFallbackImage] as $image) {
            $image = self::normalizePrimaryImage($image, $uploadPrefix);

            if ($image !== '' && self::primaryImageIsUsable($image, $imageUsable)) {
                return $image;
            }
        }

        return '';
    }

    public static function normalizePrimaryImage(string $image, string $uploadPrefix = 'uploads'): string
    {
        $image = trim($image);

        if ($image === '' || preg_match("/^(http|https):\/\//", $image)) {
            return $image;
        }

        $image = ltrim($image, '/');
        $uploadPrefix = trim($uploadPrefix, '/');

        if ($uploadPrefix !== '' && str_starts_with($image, $uploadPrefix . '/')) {
            return substr($image, strlen($uploadPrefix) + 1);
        }

        return $image;
    }

    private static function primaryImageIsUsable(string $image, ?callable $imageUsable): bool
    {
        if ($imageUsable === null) {
            return true;
        }

        return (bool)$imageUsable($image);
    }

    private static function itemStableKey(mixed $item): string
    {
        if (is_array($item)) {
            return (string)($item['id'] ?? json_encode($item, JSON_UNESCAPED_UNICODE));
        }

        if (is_object($item)) {
            return (string)($item->id ?? spl_object_hash($item));
        }

        return (string)$item;
    }

    private function rankPlans(string $seed, bool $append, string $batch): array
    {
        $rankCategory = $this->requiredCategory((int)config('category.rank'), '手游排行榜');
        $this->assertAssociationObjectExpand($rankCategory->id, $rankCategory->name);

        $slots = self::targetSlots(
            'rank',
            self::TARGETS['rank'],
            $this->existingSlots('rank'),
            $append
        );

        if (!$slots) {
            return [];
        }

        $articles = $this->articlesForRoot((int)config('category.game'), 60);

        if (count($articles) < self::relatedArticleLimit('rank')) {
            throw new RuntimeException('手游排行榜至少需要 ' . self::relatedArticleLimit('rank') . ' 篇已发布游戏文章。');
        }

        $plans = [];

        foreach ($slots as $slot) {
            $definition = $this->catalog()->rankDefinition($slot, $seed);
            $selectedArticles = self::deterministicTake($articles, self::relatedArticleLimit('rank'), $seed, "rank:{$slot}:articles");
            $title = self::makeUniqueTitle(
                $definition['title'],
                $rankCategory->id,
                $batch,
                fn (string $candidate, int $categoryId): bool => $this->titleExists($candidate, $categoryId)
            );

            $plans[] = $this->makeArticlePlan(
                'rank',
                $slot,
                $rankCategory->id,
                $rankCategory->name,
                $title,
                $definition['subject'],
                $selectedArticles,
                $seed,
                $append,
                $batch,
                null,
                (string)($rankCategory->img ?? '')
            );
        }

        return $plans;
    }

    private function collectPlans(string $type, string $seed, bool $append, string $batch): array
    {
        $targetCategoryId = $type === 'app_collect' ? (int)config('category.app_collect') : (int)config('category.collect');
        $parentCategoryId = $type === 'app_collect' ? (int)config('category.app') : (int)config('category.game');
        $defaultIds = $type === 'app_collect'
            ? $this->catalog()->defaultAppCategoryIds()
            : $this->catalog()->defaultGameCategoryIds();

        $targetCategory = $this->requiredCategory($targetCategoryId, $type === 'app_collect' ? '应用合集' : '游戏合集');
        $this->assertAssociationObjectExpand($targetCategory->id, $targetCategory->name);

        $slots = self::targetSlots(
            $type,
            self::TARGETS[$type],
            $this->existingSlots($type),
            $append
        );

        if (!$slots) {
            return [];
        }

        $eligibleCategories = $this->eligibleCollectCategories($parentCategoryId, $defaultIds);
        $slotCategories = self::collectCategoryPlansForSlots($slots, $eligibleCategories, self::TARGETS[$type], $seed, $type);
        $plans = [];

        foreach ($slotCategories as $slotCategory) {
            $slot = $slotCategory['slot'];
            $category = $slotCategory['category'];
            $articles = $this->articlesForCategory((int)$category['id'], 60);
            $selectedArticles = self::deterministicTake($articles, self::relatedArticleLimit($type), $seed, "{$type}:{$category['id']}:articles");
            $baseTitle = $this->catalog()->collectTitle($type, $category['name']);
            $title = self::makeUniqueTitle(
                $baseTitle,
                $targetCategory->id,
                $batch,
                fn (string $candidate, int $categoryId): bool => $this->titleExists($candidate, $categoryId)
            );

            $plans[] = $this->makeArticlePlan(
                $type,
                $slot,
                $targetCategory->id,
                $targetCategory->name,
                $title,
                $category['name'],
                $selectedArticles,
                $seed,
                $append,
                $batch,
                (int)$category['id'],
                (string)($category['img'] ?: $targetCategory->img)
            );
        }

        return $plans;
    }

    private function makeArticlePlan(
        string $type,
        int $slot,
        int $categoryId,
        string $categoryName,
        string $title,
        string $subject,
        array $articles,
        string $seed,
        bool $append,
        string $batch,
        ?int $sourceCategoryId,
        string $categoryImage
    ): array {
        $marker = self::marker($type, $slot, $append, $batch);
        $siteName = $this->siteName();
        $seo = $this->catalog()->seoFor($type, $title, $subject, $siteName, $seed, $slot);
        $catalogFallbackImage = $this->catalog()->imageFor($type, $sourceCategoryId, $seed, $slot);
        $img = self::selectPrimaryImage(
            $articles,
            $categoryImage,
            $seed,
            "{$type}:{$slot}:primary-image",
            fn (string $candidate): bool => $this->isUsablePrimaryImage($candidate),
            $catalogFallbackImage,
            $this->uploadPrefix()
        );

        if ($img === '') {
            throw new RuntimeException("{$title} 缺少可用主图。");
        }

        return [
            'type' => $type,
            'slot' => $slot,
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'source_category_id' => $sourceCategoryId,
            'title' => $title,
            'subject' => $subject,
            'img' => $img,
            'marker' => $marker,
            'content' => $this->catalog()->contentFor($type, $title, $subject, $articles, $marker, $siteName, $seed, $slot),
            'seo_title' => $seo['seo_title'],
            'seo_keyword' => $seo['seo_keyword'],
            'seo_desc' => $seo['seo_desc'],
            'articles' => $articles,
            'article_ids' => array_column($articles, 'id'),
        ];
    }

    /**
     * @throws Throwable
     */
    private function createArticle(array $item): Article
    {
        $now = Date::now()->format('Y-m-d H:i:s');
        $generator = new ArticleGenerator();

        return $generator->fill([
            'category_id' => $item['category_id'],
            'push_time' => $now,
            'issue_time' => $now,
            'content' => $item['content'],
            'img' => $item['img'],
            'title' => $item['title'],
            'seo_title' => $item['seo_title'],
            'seo_desc' => $item['seo_desc'],
            'seo_keyword' => $item['seo_keyword'],
            'status' => 1,
            'push_status' => 1,
            'hits' => 0,
        ], [
            'association_object' => $this->relationItems($item['articles']),
            'author' => 'YYCMS编辑部',
            'source' => $this->siteName(),
        ])->create(false, false, true);
    }

    private function relationItems(array $articles): array
    {
        return array_map(function (array $article): array {
            return [
                'id' => $article['id'],
                'title' => $article['title'],
                'img' => $article['img'],
            ];
        }, $articles);
    }

    private function eligibleCollectCategories(int $parentCategoryId, array $defaultIds): array
    {
        $categories = Category::withoutGlobalScope('statusScope')
            ->where('pid', $parentCategoryId)
            ->where('status', 1)
            ->whereIn('id', $defaultIds)
            ->orderBy('id')
            ->get();

        $items = [];

        foreach ($categories as $category) {
            $imagePool = $this->catalog()->imagePoolForCategory((int)$category->id);

            if (count($imagePool) < 20) {
                continue;
            }

            $articleCount = Article::where('category_id', $category->id)->count();

            if ($articleCount < self::collectCategoryMinimumArticleCount()) {
                continue;
            }

            $items[] = [
                'id' => (int)$category->id,
                'name' => $category->name,
                'img' => $category->img,
                'article_count' => $articleCount,
            ];
        }

        return $items;
    }

    private function articlesForRoot(int $rootCategoryId, int $limit): array
    {
        $categoryIds = Category::withoutGlobalScope('statusScope')
            ->where('status', 1)
            ->where(function ($query) use ($rootCategoryId) {
                $query->where('id', $rootCategoryId)->orWhere('pid', $rootCategoryId);
            })
            ->pluck('id')
            ->all();

        if (!$categoryIds) {
            return [];
        }

        return Article::select(['id', 'title', 'img', 'category_id'])
            ->whereIn('category_id', $categoryIds)
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (Article $article): array => $this->articleToArray($article))
            ->all();
    }

    private function articlesForCategory(int $categoryId, int $limit): array
    {
        return Article::select(['id', 'title', 'img', 'category_id'])
            ->where('category_id', $categoryId)
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (Article $article): array => $this->articleToArray($article))
            ->all();
    }

    private function articleToArray(Article $article): array
    {
        return [
            'id' => (int)$article->id,
            'title' => $article->title,
            'img' => $article->img,
            'category_id' => (int)$article->category_id,
        ];
    }

    private function existingSlots(string $type): array
    {
        $slots = [];
        $pattern = '/<!--\s*' . preg_quote(self::MARKER_PREFIX, '/') . '\s+' . preg_quote($type, '/') . ':(\d+)(?:\s+[^>]*)?-->/';

        Article::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('content', 'like', '%' . self::MARKER_PREFIX . " {$type}:%")
            ->select(['id', 'content'])
            ->chunkById(100, function ($items) use (&$slots, $pattern) {
                foreach ($items as $item) {
                    if (preg_match($pattern, $item->content, $matches)) {
                        $slots[] = (int)$matches[1];
                    }
                }
            });

        return array_values(array_unique($slots));
    }

    private function requiredCategory(int $categoryId, string $label): Category
    {
        $category = Category::withoutGlobalScope('statusScope')->where('id', $categoryId)->first();

        if (!$category) {
            throw new RuntimeException("{$label} 分类不存在：{$categoryId}");
        }

        if ((int)$category->status !== 1) {
            throw new RuntimeException("{$label} 分类未上架：{$categoryId} {$category->name}");
        }

        return $category;
    }

    private function assertAssociationObjectExpand(int $categoryId, string $categoryName): void
    {
        $expand = getExpandByCategoryId($categoryId);

        foreach ($expand as $item) {
            if (($item['name'] ?? '') === 'association_object' && (int)($item['type'] ?? 0) === 7) {
                return;
            }
        }

        throw new RuntimeException("分类 {$categoryId} {$categoryName} 缺少 association_object 关联拓展字段。");
    }

    private function titleExists(string $title, int $categoryId): bool
    {
        return Article::withoutGlobalScopes()
            ->where('title', $title)
            ->where('category_id', $categoryId)
            ->exists();
    }

    private function defaultSeed(): string
    {
        $parts = [
            (string)env('APP_URL', ''),
            (string)config('app.url', ''),
        ];

        try {
            $parts[] = (string)getOption('domain', '');
            $parts[] = (string)getOption('site_name', '');
        } catch (Throwable) {
        }

        $seed = implode('|', array_filter($parts));

        return $seed !== '' ? sha1($seed) : 'yycms-prelaunch-content';
    }

    private function siteName(): string
    {
        try {
            return (string)getOption('site_name', '本站');
        } catch (Throwable) {
            return '本站';
        }
    }

    private function isUsablePrimaryImage(string $image): bool
    {
        $image = self::normalizePrimaryImage($image, $this->uploadPrefix());

        if ($image === '') {
            return false;
        }

        if (preg_match("/^(http|https):\/\//", $image)) {
            $path = parse_url($image, PHP_URL_PATH) ?: '';
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            return in_array($extension, $this->allowedImageExtensions(), true);
        }

        if (str_contains($image, "\0")) {
            return false;
        }

        if (!$this->usesLocalUpload()) {
            return true;
        }

        $path = public_path($this->uploadPrefix() . '/' . ltrim($image, '/'));

        return is_file($path) && @getimagesize($path) !== false;
    }

    private function usesLocalUpload(): bool
    {
        return strtolower((string)env('UPLOAD_TYPE', 'local')) === 'local';
    }

    private function uploadPrefix(): string
    {
        $prefix = trim((string)config('yycms.upload_prefix', 'uploads'), '/');

        return $prefix !== '' ? $prefix : 'uploads';
    }

    private function allowedImageExtensions(): array
    {
        $extensions = array_map('trim', explode(',', (string)env('ALLOW_UPLOAD_TYPE', 'png,gif,jpg,jpeg')));
        $extensions = array_map('strtolower', $extensions);

        return array_values(array_filter($extensions));
    }

    private function catalog(): PrelaunchContentCatalog
    {
        return $this->catalog;
    }
}
