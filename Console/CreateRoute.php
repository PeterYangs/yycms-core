<?php

namespace Ycore\Console;


use Illuminate\Console\Command;
use Ycore\Models\Category;


class CreateRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateRoute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成路由';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $list = Category::with('category_route')->has('category_route')->get();


        $pcRoute = "<?php \n";
        $mobileRoute = "<?php \n";

        foreach ($list as $vo) {


            foreach ($vo->category_route as $vv) {


                if ($vv->tag === "detail") {


                    if ($vv->type === 1) {


                        if ($vv->is_main === 1) {

                            \Cache::put('category:detail:pc_' . $vo->id, $vv->route);
                        }


                        $pcRoute .= "Route::get('/{$vv->route}', make(\Ycore\Http\Controllers\Pc\Detail::class , 'detail', []))->setDefaults(['cid' => {$vo->id},'is_auto'=>1,'_type'=>'detail'])";

                    }

                    if ($vv->type === 2) {

                        if ($vv->is_main === 1) {

                            \Cache::put('category:detail:mobile_' . $vo->id, $vv->route);
                        }

                        $mobileRoute .= "Route::get('/{$vv->route}', make(\Ycore\Http\Controllers\Mobile\Detail::class , 'detail', []))->setDefaults(['cid' => {$vo->id},'is_auto'=>1,'_type'=>'detail'])";

                    }


                }


                if ($vv->tag === "list") {


                    if ($vv->type === 1) {

                        if ($vv->is_main === 1) {

                            \Cache::put('category:list:pc_' . $vo->id, $vv->route);
                        }


                        $pcRoute .= "Route::get('/{$vv->route}', make(\Ycore\Http\Controllers\Pc\Channel::class , 'channel', []))->setDefaults(['cid' => {$vo->id},'is_auto'=>1,'_type'=>'channel'])";

                    }

                    if ($vv->type === 2) {

                        if ($vv->is_main === 1) {

                            \Cache::put('category:list:mobile_' . $vo->id, $vv->route);
                        }


                        $mobileRoute .= "Route::get('/{$vv->route}', make(\Ycore\Http\Controllers\Mobile\Channel::class , 'channel', []))->setDefaults(['cid' => {$vo->id},'is_auto'=>1,'_type'=>'channel'])";

                    }


                }


                if ($vv->type === 1) {


                    if ($vv->tag === "detail") {

                        $pcRoute .= "->middleware(\Ycore\Http\Middleware\home\StaticRender::class)";

                    }

                    if ($vv->alias) {

                        $pcRoute .= "->name('{$vv->alias}')";
                    }

                    if ($pcRoute) {

                        $pcRoute .= ";\n\n";
                    }


                }


                if ($vv->type === 2) {


                    if ($vv->tag === "detail") {

                        $mobileRoute .= "->middleware(\Ycore\Http\Middleware\home\StaticRender::class)";

                    }

                    if ($vv->alias) {

                        $mobileRoute .= "->name('{$vv->alias}')";
                    }

                    if ($mobileRoute) {

                        $mobileRoute .= ";\n\n";
                    }


                }


            }


        }


        file_put_contents(base_path('routes/channel/pc.php'), $pcRoute);
        file_put_contents(base_path('routes/channel/mobile.php'), $mobileRoute);


        return 0;
    }
}
