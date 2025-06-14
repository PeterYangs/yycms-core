<?php

namespace Ycore\Schedule;

use Illuminate\Console\Scheduling\Schedule;

class Kernel
{


    public function schedule(Schedule $schedule)
    {

        //go脚本自动更新
        $schedule->command("GetGoScript")->hourly();


        //采集文章发布到正式文章
//        $schedule->command('AutoPush')->everyMinute();


        //生成全站链接
        $schedule->command("MakeAllLink")->dailyAt("02:13");

        //生成网站地图
        $schedule->command('MakeXml')->dailyAt("03:00");

        //数据库备份
//        $schedule->command('MysqlBackup')->dailyAt("04:00");

        //日志清理
        $schedule->command('CleanUserAccess')->dailyAt("01:05");
        $schedule->command('CleanErrorAccess')->dailyAt("01:15");


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
            $schedule->command('StaticTool')->dailyAt("01:25");
        }

        //生成死链
        $schedule->command('CreateDeathLink')->dailyAt("03:25");



    }


}
