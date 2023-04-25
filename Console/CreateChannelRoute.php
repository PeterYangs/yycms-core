<?php

namespace Ycore\Console;


use Ycore\Models\Category;
use Illuminate\Console\Command;


class CreateChannelRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateChannelRoute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成栏目路由';


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


//            \Cache::put('category:');


            foreach ($vo->category_route as $vv) {


                if ($vv->tag === "detail") {


                    if ($vv->type === 1) {


                        \Cache::put('category:detail:pc_' . $vo->id, $vv->route);
                    }

                    if ($vv->type === 2) {

                        \Cache::put('category:detail:mobile_' . $vo->id, $vv->route);

                    }


                }


                if ($vv->tag === "list") {


                    if ($vv->type === 1) {


                        \Cache::put('category:list:pc_' . $vo->id, $vv->route);
                    }

                    if ($vv->type === 2) {

                        \Cache::put('category:list:mobile_' . $vo->id, $vv->route);

                    }


                }


                if ($vv->type === 1) {


                    $pcRoute .= "Route::get('/{$vv->route}', make(\App\Http\\" . str_replace(".php", "",
                            str_replace("/", "\\",
                                $vv->controller)) . "::class, '{$vv->action}', ['cid' => {$vo->id}]))";

                    if ($vv->alias) {

                        $pcRoute .= "->name('{$vv->alias}')";
                    }


                    $pcRoute .= ";\n\n";


                }


                if ($vv->type === 2) {


                    $mobileRoute .= "Route::get('/{$vv->route}', make(\App\Http\\" . str_replace(".php", "",
                            str_replace("/", "\\",
                                $vv->controller)) . "::class, '{$vv->action}', ['cid' => {$vo->id}]))";

                    if ($vv->alias) {

                        $mobileRoute .= "->name('{$vv->alias}')";
                    }


                    $mobileRoute .= ";\n\n";


                }


            }


        }


        file_put_contents(base_path('routes/channel/pc.php'), $pcRoute);
        file_put_contents(base_path('routes/channel/mobile.php'), $mobileRoute);


        return 0;
    }
}
