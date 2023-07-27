<?php

namespace Ycore\Http\Controllers\Mobile;

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
        $cid = request()->route('cid');

        $category = Category::where('id', $cid)->with('category_route')->firstOrFail();

        $route = $category->category_route->where('type', 1)->where('tag', 'list')->where('is_main', 1)->value('route');


        $currentRoute = $route;

        $viewFile = $this->getViewPath() . "/channel-" . $route . ".blade.php";

        $view = "/channel-" . $route;

        if (!file_exists($viewFile) && $category->pid !== 0) {

            $route = $category->parent->category_route->where('type', 1)->where('tag', 'list')->where('is_main', 1)->value('route');

            $viewFile = $this->getViewPath() . "/channel-" . $route . ".blade.php";

            $view = "/channel-" . $route;

        }

        $cid = getCategoryIds($category->id);

        $query = ArticleListModel()->whereIn('category_id', $cid);


        $channel = Hook::applyFilter('channel', $category->toArray(), $category->parent ? $category->parent->toArray() : ['id' => 0], $currentRoute, 'mobile', request()->input(), request()->route());

        if ($channel === null || !($channel instanceof \Ycore\Dao\Channel)) {

            $channel = \Ycore\Dao\Channel::channel(10, 1);

        }

        $query->orderBy($channel->getOrderField(), $channel->getOrderDirection());

        $data = $query->seoPaginate($channel->getSize(), ['*'], $channel->getPage(),
            $channel->getPath());


        if (file_exists($viewFile)) {


            return view($view, ['category' => $category, 'data' => $data]);
        }

        return view('channel', ['category' => $category, 'data' => $data]);
    }

}
