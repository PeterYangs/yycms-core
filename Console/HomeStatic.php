<?php

namespace Ycore\Console;

use Illuminate\Console\Command;

class HomeStatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HomeStatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '主页静态化';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $domain = getOption('domain', "");


        if ($domain) {


            try {
                $b = \Http::withHeaders([])->get($domain . '?admin_key=' . env('ADMIN_KEY'))->body();

                \Storage::disk('static')->put('pc/index.html', $b);

            } catch (\Throwable $exception) {

            }


        }


        $m_domain = getOption('m_domain', '');


        if ($m_domain) {


            try {
                $b = \Http::withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'])->get($m_domain . '?admin_key=' . env('ADMIN_KEY'))->body();

                \Storage::disk('static')->put('mobile/index.html', $b);

            } catch (\Throwable $exception) {

            }


        }


        return 0;
    }
}
