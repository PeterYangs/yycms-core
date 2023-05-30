<?php

namespace Ycore\Http\Controllers\Pc;

use Ycore\Models\Category;
use Ycore\Tool\Hook;

class Channel extends Base
{


    /**
     * 栏目控制器
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function channel()
    {

        $cid = request()->get('cid');

        $category = Category::where('id', $cid)->with('category_route')->firstOrFail();


        $route = "channel-" . $category->category_route->where('type', 1)->where('tag', 'list')->where('is_main', 1)->value('route');


        $viewFile = $this->getViewPath() . "/" . $route . ".blade.php";

        $view = $route;

        if (!file_exists($viewFile)) {

            $route = "channel-" . $category->parent->category_route->where('type', 1)->where('tag', 'list')->where('is_main', 1)->value('route');

            $viewFile = $this->getViewPath() . "/" . $route . ".blade.php";


            $view = $route;

        }


        $cid = getCategoryIds($category->id);


        $query = ArticleListModel()->whereIn('category_id', $cid);

        $query->orderBy('push_time', 'desc');

        $channel = Hook::applyFilter('channel', $category, $route, 'pc', request()->input(), request()->route());


        if ($channel === null || !($channel instanceof \Ycore\Dao\Channel)) {

            $channel = \Ycore\Dao\Channel::channel(10, 1);

        }


        $data = $query->seoPaginate($channel->getSize(), ['*'], $channel->getPage(),
            $channel->getPath());


        if (file_exists($viewFile)) {


            return view($view, ['category' => $category, 'data' => $data]);
        }

        return view('channel', ['category' => $category, 'data' => $data]);
    }

}
