<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SetArticleTimestamps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetArticleTimestamps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '上线前批量设置文章时间';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('memory_limit', '2G');
        // 配置最大间隔（分钟）
        $maxIntervalMinutes = 45;

        // 获取文章数据
        $articles = DB::table('article')->orderBy('id')->get();
        $total = $articles->count();

        if ($total === 0) {
            $this->info("没有文章数据");
            return 0;
        }

        // 使用最大间隔的一半来估算总时间跨度（更密集）
        $totalGapMinutes = ($total - 1) * ($maxIntervalMinutes * 1.4);
        $now = Carbon::now();
        $start = $now->copy()->subMinutes($totalGapMinutes);
        $current = $start->copy();

        $this->info("将从 {$start->toDateTimeString()} 到 {$now->toDateTimeString()} 分布 {$total} 篇文章");

        foreach ($articles as $index => $article) {
            if ($current->greaterThan($now)) {
                $this->warn("生成时间超出当前时间，剩余未处理：" . ($total - $index));
                break;
            }

            // 判断是否节假日（周六日）
            $isHoliday = in_array($current->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);

            // 当前时间作为发布时间（再随机秒）
            $postTime = $current->copy()->setSecond(rand(0, 59));

            // 写入数据库
            DB::table('article')->where('id', $article->id)->update([
                'created_at' => $postTime,
                'updated_at' => $postTime,
                'push_time' => $postTime,
                'issue_time' => $postTime,
            ]);

            $this->line("ID {$article->id} 设置为 {$postTime->toDateTimeString()} （" . ($isHoliday ? '节假日' : '工作日') . "）");

            // 推进到下一条时间
            $current = $this->addRandomTime($postTime, $isHoliday, $maxIntervalMinutes);
        }

        $this->info("分布完成，共处理 {$total} 篇文章");

        return 0;
    }

    /**
     * 时间推进器：在当前时间上加随机分钟和秒，节假日间隔更长，超出20:00跳到次日08:00
     */
    protected function addRandomTime(Carbon $current, bool $isHoliday, int $maxInterval)
    {
        $min = $isHoliday ? intval($maxInterval * 2) : 5;
        $max = $maxInterval;

        $next = $current->copy()
            ->addMinutes(rand($min, $max))
            ->addSeconds(rand(0, 59));

        if ($next->hour >= 20) {
            $next->addDay()->setTime(8, 0, rand(0, 59));
        }

        return $next;
    }

}
