<?php

namespace Ycore\Http\Controllers\Mobile;

use Ycore\Models\Category;
use Ycore\Models\Page;

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

        //获取下载地址
        $item->append('download_url');

        $category = Category::where('id', $cid)->with('category_route')->firstOrFail();

        $template = $this->resolveCategoryTemplate($category, 'detail', 2);

        return view($template['view'], ['category' => $category, 'data' => $item]);


    }

    function page($route)
    {

        $page = Page::where('route', $route)->firstOrFail();

        $viewFile = $this->getViewPath() . "/about.blade.php";

        if (!file_exists($viewFile)) {

            abort(404);
        }

        return view('about', ['page' => $page]);
    }

}
