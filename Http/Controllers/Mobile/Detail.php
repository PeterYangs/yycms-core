<?php

namespace Ycore\Http\Controllers\Mobile;

use Ycore\Models\Category;

class Detail extends Base
{


    function detail()
    {

        $id = request()->route('id', 0);

        $cid = (int)request()->route('cid');


        $item = ArticleDetailModel()->where('id', $id)
            ->with('article_tag')
            ->where('category_id', $cid)
            ->firstOrFail();

        $item->append('download_url');



        $category = Category::where('id', $cid)->with('category_route')->firstOrFail();

        $listRoute = $category->category_route->where('type', 1)->where('tag', 'list')->where('is_main', 1)->value('route');

        $viewFile = $this->getViewPath() . "/channel-" . $listRoute . ".blade.php";

        $view = "/detail-" . $listRoute;

        if (!file_exists($viewFile)) {

            $route = $category->parent->category_route->where('type', 1)->where('tag', 'list')->where('is_main', 1)->value('route');

            $viewFile = $this->getViewPath() . "/detail-" . $route . ".blade.php";

            $view = "/detail-" . $route;

        }

        if (file_exists($viewFile)) {


            return view($view, ['category' => $category, 'data' => $item]);
        }

        return view('detail', ['category' => $category, 'data' => $item]);


    }

}
