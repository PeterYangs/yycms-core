<?php

namespace Ycore\Console;

use Ycore\Models\Admin;
use Faker\Generator;
use Illuminate\Console\Command;


class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成管理员';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $faker = app(Generator::class);


        $password = $faker->password;


        Admin::updateOrCreate(['username' => 'root'],
            [
                'id' => 1,
                'username' => 'root',
                'password' => $password,
                'email' => "root@yycms.com",
                'nick_name' => "root",
                'role_id' => 0,

            ]);


        echo "root账号密码为:  " . $password . PHP_EOL;

        return 0;
    }
}
