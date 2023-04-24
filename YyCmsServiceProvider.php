<?php

namespace Ycore;

use Illuminate\Support\ServiceProvider;
use Ycore\Console\Test2;

class YyCmsServiceProvider extends ServiceProvider
{


    public function boot()
    {

    }


    public function register()
    {


        $this->bootCommands();

    }


    private function bootCommands()
    {


        if ($this->app->runningInConsole()) {

            $this->commands([
                Test2::class
            ]);

        }

    }


}
