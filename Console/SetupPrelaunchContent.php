<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Throwable;
use Ycore\Tool\PrelaunchContentSeeder;

class SetupPrelaunchContent extends Command
{
    protected $signature = 'SetupPrelaunchContent {--dry-run} {--seed=} {--append}';

    protected $description = '上线前填充手游排行榜、游戏合集和应用合集内容';

    public function handle(): int
    {
        $dryRun = (bool)$this->option('dry-run');
        $append = (bool)$this->option('append');
        $seed = trim((string)$this->option('seed')) ?: null;

        try {
            $result = (new PrelaunchContentSeeder())->run($dryRun, $append, $seed);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $this->line('seed：' . $result['seed']);
        $this->line('batch：' . $result['batch']);
        $this->line('mode：' . ($append ? 'append' : 'default'));

        if ($dryRun) {
            $this->warn('当前为 dry-run 模式，未写入数据库，未下载远程图片。');
        }

        if (!$result['items']) {
            $this->info('没有需要生成的内容。');

            return 0;
        }

        $this->table([
            '类型',
            'slot',
            '分类',
            '标题',
            '主图',
            '关联文章ID',
            $dryRun ? '状态' : '文章ID',
        ], array_map(function (array $item) use ($dryRun): array {
            return [
                $item['type'],
                $item['slot'],
                $item['category_name'] . '(' . $item['category_id'] . ')',
                $item['title'],
                $item['img'],
                implode(',', $item['article_ids']),
                $dryRun ? '待生成' : ($item['created_id'] ?? ''),
            ];
        }, $result['items']));

        $this->info('计划生成：' . $result['planned'] . ' 篇');

        if (!$dryRun) {
            $this->info('实际生成：' . $result['created'] . ' 篇');
        }

        return 0;
    }
}
