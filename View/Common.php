<?php

namespace Ycore\View;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Contracts\View\Engine;
use mysql_xdevapi\Exception;
use QL\QueryList;
use Ycore\Models\Article;
use Ycore\Models\Special;

class Common implements Engine
{

    /**
     * @var Engine
     */
    protected Engine $engine;


    /**
     * @param Engine $engine
     */
    public function __construct(Engine $engine)
    {
        $this->engine = $engine;

    }


    public function get($path, array $data = [])
    {
        // TODO: Implement get() method.

        //只能被设置一次
        static $isSet = false;

        $html = "";

        try {
            //获取原始html
            $html = $this->engine->get($path, $data);

        } catch (\Exception $exception) {


            throw $exception->getPrevious();


        }


        $htmlDoc = QueryList::html($html);


        if (!$isSet && $htmlDoc->find("body")->count() > 0) {

            //文章点击逻辑
            if (request()->route('_type') === "detail" && isset($data['data']) && $data['data'] instanceof Article) {


                $htmlDoc->find("body")->append("<script  src='/_hit?id={$data['data']->id}' type='text/javascript' charset='utf-8'></script>");

            }

            //备案页面
            if (request()->path() === "/" && getOption('is_beian')) {

                $htmlDoc->find("head")->prepend("<script  src='/_beian.js' type='text/javascript' charset='utf-8'></script>");
            }


            if (request()->host() === parse_url(getOption('m_domain'))['host']) {

                //手机端js跳pc
                $htmlDoc->find("body")->append(view('_to_pc')->render());


                //手机端详情js隐藏
                if (isset($data['data']) && $data['data'] instanceof Article) {


                    $special = Special::where('id', $data['data']->special_id)->first();

                    if ($special && $special->js_mobile_hide === 1) {

                        $htmlDoc->find("head")->append("<script  src='/_js_hide.js' type='text/javascript' charset='utf-8'></script>");

                    }

                }


            } else {

                //pc端跳手机
                $htmlDoc->find("body")->append(view('_to_mobile')->render());


                //pc端详情js隐藏
                if (isset($data['data']) && $data['data'] instanceof Article) {


                    $special = Special::where('id', $data['data']->special_id)->first();

                    if ($special && $special->js_pc_hide === 1) {

                        $htmlDoc->find("head")->append("<script  src='/_js_hide.js' type='text/javascript' charset='utf-8'></script>");
                    }

                }

            }


            //详情页时间因子
            if (isset($data['data']) && $data['data'] instanceof Article && request()->route('_type') === "detail") {


                $htmlDoc->find("body")->append(view('_time_json_detail', ['data' => $data['data']])->render());

            }

            //主页时间因子
            if (request()->path() === "/") {


                $htmlDoc->find("body")->append(view('_time_json_index', [])->render());

            }

            //加锁
            $isSet = true;
        }

        return $htmlDoc->getHtml();
    }
}
