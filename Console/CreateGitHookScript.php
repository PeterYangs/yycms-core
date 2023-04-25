<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CreateGitHookScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateGitHookScript';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成git钩子脚本';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        file_put_contents(base_path('.git/hooks/post-checkout'),"#!/bin/sh

".env('PHP_DEVELOP_CLI')." artisan GitCheckoutHook");



        return 0;
    }
}
