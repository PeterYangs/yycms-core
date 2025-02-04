<?php

namespace Ycore\Http\Controllers\Pc;

use Ycore\Dao\ChannelRandom;
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

        $path = request()->path();

        $htmlPath = str_replace("/", "_____", $path);

        //静态读取
        if (\Storage::disk('static')->exists('pc/__channel/' . $htmlPath)) {
            if (time() - \Storage::disk('static')->lastModified('pc/__channel/' . $htmlPath) < 60 * 5) {
                return \Storage::disk('static')->get('pc/__channel/' . $htmlPath);
            }

        }

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


        $channel = Hook::applyFilter('channel', $category->toArray(), $category->parent ? $category->parent->toArray() : ['id' => 0], $currentRoute, 'pc', request()->input(), request()->route());

        if ($channel === null || !($channel instanceof \Ycore\Dao\Channel)) {

            $channel = \Ycore\Dao\Channel::channel(10, 1);

        }

        if ($channel instanceof \Ycore\Dao\Channel) {

            $query->orderBy($channel->getOrderField(), $channel->getOrderDirection());
        }

        if ($channel instanceof ChannelRandom) {

            $query->inRandomOrder();
        }


        $data = $query->seoPaginate($channel->getSize(), ['*'], $channel->getPage(),
            $channel->getPath());

        $viewHtml = "";
        if (file_exists($viewFile)) {
            $viewHtml = view($view, ['category' => $category, 'data' => $data])->render();
        } else {
            $viewHtml = view('channel', ['category' => $category, 'data' => $data])->render();
        }

        $lock = \Cache::lock('pc_' . $htmlPath, 10);
        try {
            if ($lock->get()) {
                //静态写入
                \Storage::disk('static')->put('pc/__channel/' . $htmlPath, $viewHtml);
                $lock->release();
            }
        } finally {
            $lock->release();
        }

        return $viewHtml;
    }

}
