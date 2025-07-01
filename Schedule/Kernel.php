<?php

namespace Ycore\Schedule;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Date;

class Kernel
{


    public function schedule(Schedule $schedule)
    {

        $date = Date::createFromTimeString(date("Y-m-d") . " " . $this->getNightTimeFromString(env('APP_NAME')));


        //go脚本自动更新
        $schedule->command("GetGoScript")->hourly();


        //采集文章发布到正式文章
//        $schedule->command('AutoPush')->everyMinute();


        //生成全站链接
        $schedule->command("MakeAllLink")->dailyAt($date->format('H:i'));

        //生成网站地图
        $schedule->command('MakeXml')->dailyAt($date->addMinutes(30)->format('H:i'));


        //日志清理
        $schedule->command('CleanUserAccess')->dailyAt($date->addMinutes(60)->format('H:i'));
        $schedule->command('CleanErrorAccess')->dailyAt($date->addMinutes(90)->format('H:i'));


        //自动采集
//        $schedule->command('Spider')->hourly();
//        $schedule->command('SpiderTable')->hourly();


        //每小时更新一次访问数据
        $schedule->command("GetAccess")->hourly()->between('5:00', '23:00');

        $schedule->command("SearchAccess")->everyFifteenMinutes()->between('5:00', '23:00');

        //每分钟静态化一次主页
        $schedule->command('HomeStatic')->everyMinute();

        //定时文章发布
//        $schedule->command('TimingArticlePush')->everyMinute();

        if (getOption('static_everyday', 0) === 1) {
            //详情页静态化
            $schedule->command('StaticTool')->dailyAt($date->addMinutes(120)->format('H:i'));
        }

        //生成死链
        $schedule->command('CreateDeathLink')->dailyAt($date->addMinutes(150)->format('H:i'));

        //数据库备份
        $schedule->command('MysqlBackup')->dailyAt($date->addMinutes(180)->format('H:i'));


    }


    function getNightTimeFromString($input)
    {
        // 将字符串转换为哈希值
        $hash = crc32($input);

        // 限制在 0~239（4小时 * 60分钟）
        $minutes = $hash % 240;

        // 计算小时和分钟
        $hour = intdiv($minutes, 60);
        $minute = $minutes % 60;

        // 返回格式化时间
        return sprintf("%02d:%02d", $hour, $minute);
    }


}
