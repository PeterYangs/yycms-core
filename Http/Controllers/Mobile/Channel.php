<?php

namespace Ycore\Http\Controllers\Mobile;

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
        // 判断是否启用读取静态缓存
        $staticCacheReadEnabled = env('STATIC_CACHE_READ_ENABLED', true); // 默认为 true
        $staticOpen = env('STATIC_OPEN', true); // 默认为 true
        $cid = request()->route('cid');
        $category = Category::where('id', $cid)->with('category_route')->firstOrFail();
        $path = request()->path();
        $htmlPath = str_replace("/", "_____", $path);

        // 如果启用静态缓存读取，且文件存在且未过期，则返回静态文件
        if ($staticOpen && $staticCacheReadEnabled && \Storage::disk('static')->exists('mobile/__channel/' . $htmlPath)) {
            if (time() - \Storage::disk('static')->lastModified('mobile/__channel/' . $htmlPath) < 60 * 10) {
                // 返回缓存的静态文件
                return \Storage::disk('static')->get('mobile/__channel/' . $htmlPath);
            }
        }

        // 如果关闭静态缓存读取，或者静态缓存文件不存在，则生成动态内容
        $lock = \Cache::lock('mobile_' . $htmlPath, 10); // 10秒超时

        // 如果获得锁，进行缓存更新
        try {
            if ($lock->get()) {
                // 生成动态内容并获取视图HTML
                $viewHtml = $this->generateViewHtml($category, $htmlPath);

                // 如果启用静态缓存，且静态文件存在，则更新静态缓存
                if ($staticCacheReadEnabled) {
                    \Storage::disk('static')->put('mobile/__channel/' . $htmlPath, $viewHtml);
                }

                return $viewHtml;
            } else {
                // 锁获取失败，直接执行动态内容生成
                return $this->generateViewHtml($category, $htmlPath);
            }
        } finally {
            // 释放锁
            if ($lock->get()) {
                $lock->release();
            }
        }
    }

    /**
     * 生成视图HTML
     * @param \Ycore\Models\Category $category
     * @param string $htmlPath
     * @return string
     */
    private function generateViewHtml($category, $htmlPath)
    {
        $route = $category->category_route->where('type', 2)->where('tag', 'list')->where('is_main', 1)->value('route');
        $currentRoute = $route;
        $viewFile = $this->getViewPath() . "/channel-" . $route . ".blade.php";
        $view = "/channel-" . $route;

        if (!file_exists($viewFile) && $category->pid !== 0) {
            $route = $category->parent->category_route->where('type', 2)->where('tag', 'list')->where('is_main', 1)->value('route');
            $viewFile = $this->getViewPath() . "/channel-" . $route . ".blade.php";
            $view = "/channel-" . $route;
        }

        $cid = getCategoryIds($category->id);
        $query = ArticleListModel()->whereIn('category_id', $cid);

        $channel = Hook::applyFilter('channel', $category->toArray(), $category->parent ? $category->parent->toArray() : ['id' => 0], $currentRoute, 'mobile', request()->input(), request()->route());

        if ($channel === null || !($channel instanceof \Ycore\Dao\Channel)) {
            $channel = \Ycore\Dao\Channel::channel(10, 1);
        }

        if ($channel instanceof \Ycore\Dao\Channel) {
            $query->orderBy($channel->getOrderField(), $channel->getOrderDirection());
        }

        if ($channel instanceof ChannelRandom) {
            $query->inRandomOrder();
        }

        $data = $query->seoPaginate($channel->getSize(), ['*'], $channel->getPage(), $channel->getPath());

        $viewHtml = "";
        if (file_exists($viewFile)) {
            $viewHtml = view($view, ['category' => $category, 'data' => $data])->render();
        } else {
            $viewHtml = view('channel', ['category' => $category, 'data' => $data])->render();
        }

        return $viewHtml;
    }

}
