<?php

namespace Ycore\Http\Controllers\Pc;

use Ycore\Models\Category;

class Channel extends Base
{


    function channel()
    {

        $cid = request()->get('cid');

        $category = Category::where('id', $cid)->with('category_route')->firstOrFail();


        $route = $category->category_route->where('type', 1)->where('tag', 'list')->value('route');

        $viewFile = $this->getViewPath() . "/" . $route . ".blade.php";


        $cid = getCategoryIds($category->id);

//        dd($cid);

        $query = ArticleListModel()->whereIn('category_id', $cid);

        $query->orderBy('push_time', 'desc');


        if (file_exists($viewFile)) {


            return view($route, ['category' => $category]);
        }

        return 'channel';
    }

}
