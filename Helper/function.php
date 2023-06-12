<?php


use Illuminate\Support\Facades\File;
use Ycore\Events\ArticleUpdate;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\ArticleExpand;
use Ycore\Models\ArticleTag;
use Ycore\Models\Category;
use Ycore\Models\Collect;
use Ycore\Models\CollectTag;
use Ycore\Models\Options;
use Ycore\Tool\Hook;
use Ycore\Tool\Json;
use Ycore\Tool\Seo;
use QL\QueryList;

function paginate(\Illuminate\Database\Eloquent\Builder $list, int $page = 1, $row = 10)
{

    $page = abs($page);

    if (!$list) {
        return ['currentPage' => 1, 'data' => [], 'total' => 0];
    }

    $count = clone $list;

    $total = $count->count();

    $offset = ($page - 1) * $row;

    $data = $list->offset($offset)->limit($row)->get();

    return ['currentPage' => $page, 'data' => $data, 'total' => $total];

}


function groupByKey($array, $key, $defaultKey = '其他')
{

    $result = [];

    foreach ($array as $k => $v) {

        $item = $v[$key];

        if (!$item) {
            $item = $defaultKey;
        }

        $result[$item][] = $v;

    }

    return $result;
}

/**
 * 获取空的拓展表数据
 * Create by Peter Yang
 * 2023-02-22 13:49:24
 * @param int $category_id
 * @return mixed|string
 */
function getExpandByCategoryId(int $category_id)
{


    $cid = CategoryController::getExpandTableCategoryId($category_id);


    if (!$cid) {


        return [];
    }


    $data = ArticleExpand::with('list')->where('category_id', $cid)->first();


    if (!$data) {


        return [];
    }


    foreach ($data['list'] as $key => $value) {


        switch ($value['type']) {

            case 1:
            case 2:
            case 5:
            case 3:

                $data['list'][$key]['value'] = "";

                break;
            case 4:
            case 6:

                //复合模型
            case 7:

                $data['list'][$key]['value'] = [];
                $data['list'][$key]['show'] = false;
                break;

            //单一模型
            case 8:

                $data['list'][$key]['value'] = 0;

                $data['list'][$key]['show'] = false;
                break;


        }

    }


    return $data['list'];

}


/**
 * 将拓展表数据转key-value格式
 * Create by Peter Yang
 * 2023-02-22 13:55:47
 * @param $data
 * @return array
 * @throws JsonException
 */
function dealExpandToTable($data): array
{


    $expand_data = [];

    foreach ($data as $key => $value) {


        switch ($value['type']) {


            case 4:


                $value['value'] = array_filter($value['value'], function ($v) {

                    if ($v === "" || $v === null) {
                        return false;
                    }

                    return true;

                });

                $expand_data[$value['name']] = join(',', $value['value']);

                break;

            case 5:

                $expand_data[$value['name']] = strtotime($value['value']);

                break;

            case 7:
            case 6:

                $expand_data[$value['name']] = json_encode($value['value'], JSON_THROW_ON_ERROR);

                break;

            case 8:

                $expand_data[$value['name']] = (int)($value['value']);

                break;

            default:

                $expand_data[$value['name']] = $value['value'];


        }


    }


    return $expand_data;
}


/**
 * 根据输入的分类标识获取该分类和该分类子集id数组
 * Create by Peter Yang
 * 2023-02-22 11:37:34
 * @param string|array|int $categoryName
 * @return \Illuminate\Support\Collection
 */
function getCategoryIds(string|array|int $categoryName, $exceptSelf = false): \Illuminate\Support\Collection
{

    $category = null;

    if (is_array($categoryName)) {

        //全是id数组
        $isAllInt = true;

        foreach ($categoryName as $c) {

            if (!is_numeric($c)) {

                $isAllInt = false;

                break;
            }
        }


        $idArr = "category:son-" . implode("-", $categoryName);

        if ($isAllInt) {


            if ($exceptSelf) {


                $idArr .= "-except_self";

                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        return Category::whereIn('pid', $categoryName)->get()->pluck('id');

                    });


            } else {


                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        return Category::whereIn('pid', $categoryName)->get()->pluck('id')->merge($categoryName);

                    });

            }


        } else {


            if ($exceptSelf) {

                $idArr .= "-except_self";

                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        $c = Category::whereIn('name', $categoryName)->get();


                        return Category::whereIn('pid', $c->pluck('id')->all())->get()->pluck('id');

                    });


            } else {


                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        $c = Category::whereIn('name', $categoryName)->get();


                        return Category::whereIn('pid',
                            $c->pluck('id')->all())->get()->pluck('id')->merge($c->pluck('id'));

                    });

            }


        }


    } else {


        $idArr = "category:son-" . $categoryName;

        if (is_numeric($categoryName)) {


            if ($exceptSelf) {

                $idArr .= "-except_self";

                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        return Category::where('pid', $categoryName)->get()->pluck('id');

                    });


            } else {


                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        return Category::where('pid', $categoryName)->get()->pluck('id')->push($categoryName);

                    });

            }


        } else {

            if ($exceptSelf) {


                $idArr .= "-except_self";


                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        $c = Category::where('name', $categoryName)->get();


                        return Category::whereIn('pid', $c->pluck('id')->all())->get()->pluck('id');

                    });


            } else {


                $category = Cache::tags(['search_category'])->remember($idArr, now()->addDays(),
                    function () use ($categoryName) {


                        $c = Category::where('name', $categoryName)->get();


                        return Category::whereIn('pid',
                            $c->pluck('id')->all())->get()->pluck('id')->merge($c->pluck('id'));

                    });

            }


        }


    }


    return $category ?? collect([]);

}


/**
 * Create by Peter Yang
 * 2022-06-27 13:56:19
 * @param string|array $categoryName 分类名称
 * @param int $limit 长度
 * @param int $offset offset
 * @param bool $isParent 是否查询当前id下的子分类(已弃用)
 * @param array $querys 查询条件 [ ['status', '=', '1'], ['subscribed', '<>', '1'] ]
 * @param array $notIdArray 不包括的id
 * @param string $orderField 排序字段
 * @param string $orderDirection 排序方式，desc、asc
 * @param string $expandId 拓展id
 * @param array $expandQuerys 拓展表查询条件
 *
 * 调用例子：getArticleByCategoryName('角色扮演',11,0,[],[],'push_time','desc','1',[ ['author','=','是真的帅'] ])
 *
 * @throws Exception
 */
function getArticleByCategoryName(
    string|array|int $categoryName,
    int              $limit = 10,
    int              $offset = 0,
//    bool $isParent = false,
    array            $querys = [],
    array            $notIdArray = [],
    string           $orderField = 'push_time',
    string           $orderDirection = 'desc',
    string           $expandId = '',
    array            $expandQuerys = [],
    bool             $isRandom = false
): array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
{


    $category = getCategoryIds($categoryName);

    if ($category->count() <= 0) {


        return [];
    }

    $query = ArticleListModel()->offset($offset)->limit($limit);


    $query->whereIn('category_id', $category->all());


    if ($querys) {


        $query->where($querys);
    }


    if ($orderField === "") {

        $orderField = 'push_time';
    }

    if ($orderDirection === "") {

        $orderDirection = 'desc';
    }


    if ($isRandom) {


//        $query->whereRaw("id >= (
//		(SELECT MAX(id) FROM article) - (SELECT MIN(id) FROM article)
//	) * RAND() + (SELECT MIN(id) FROM article)");


        $maxId = Cache::remember("article_max_id", now()->addDays(7), function () {


            return Article::max('id');

        });

        $minId = Cache::remember("article_min_id", now()->addDays(7), function () {


            return Article::min('id');

        });

        $query->join(
            DB::raw("(SELECT ROUND(RAND() * ( {$maxId} - {$minId} )+ $minId ) AS xid) as t2"),
            'article.id', '>=', 't2.xid'
        );


//        $query->inRandomOrder();


    } else {

        $query->orderBy($orderField, $orderDirection);
    }


    if ($expandId && $expandQuerys) {


        $tableName = 'expand_table_' . $expandId;


        $query->whereExists(function (\Illuminate\Database\Query\Builder $q) use ($tableName, $expandQuerys) {


            $q->select(['id'])->from($tableName)->whereColumn($tableName . ".article_id",
                'article.id')->where($expandQuerys);

        });

    }


    //id排除
    if ($notIdArray) {

        $query->whereNotIn('id', $notIdArray);

    }


    return $query->get();


}


/**
 * 上方的随机版
 * Create by Peter Yang
 * 2022-06-29 17:30:56
 */
function getArticleByCategoryNameWithRandom(
    string|array $categoryName,
    int          $limit = 10,
    int          $offset = 0,
    array        $querys = [],
    array        $notIdArray = [],
    string       $orderField = 'push_time',
    string       $orderDirection = 'desc',
    string       $expandId = '',
    array        $expandQuerys = [],
): \Illuminate\Database\Eloquent\Collection|array|\Illuminate\Support\Collection
{

    return getArticleByCategoryName($categoryName, $limit, $offset, $querys, $notIdArray, $orderField,
        $orderDirection, $expandId, $expandQuerys, true);

}


/**
 * 获取对象或者数组的属性
 * @param $obj
 * @param $attr
 * @param string $default
 * @param array $returnType
 * @return mixed|string
 */
function getObjPlus($obj, $attr, $default = '')
{


    $attr = explode('.', $attr);


    foreach ($attr as $value) {

        $obj = getObj($obj, $value, $default);

    }


    if (is_array($obj) && count($obj) === 0) {


        return [];
    }


    if (!$obj) {


        return $default;
    }


//    switch ($returnType) {
//
//        case 'string':
//
//            if (!is_string($obj)) {
//                return '';
//            }
//
//            break;
//
//
//        case 'array':
//
//            if (!is_array($obj)) {
//                return [];
//            }
//
//            break;
//
//    }

    return $obj;

}

/**
 * 上一个方法的子方法
 * @param $obj
 * @param $attr
 * @param string $default
 * @return mixed|string
 */
function getObj($obj, $attr, $default = '')
{

    if (is_array($obj)) {


        if (array_key_exists($attr, $obj)) {
            return $obj[$attr];
        }

        return $default;


    } elseif (is_object($obj)) {

        if ($obj->$attr) {


            return $obj->$attr;
        }

        return $default;


    }

}


/**
 * 时间格式的格式转换
 * @param $dateString
 * @param $format
 * @return false|string
 */
function dateForString($dateString, $format)
{


    $time = strtotime($dateString);

    return date($format, $time);


}

/**
 * @param $push_time
 * @return string
 * Notes:将时间转换为几秒前、几分钟前、几小时前、几天前
 * User: Zy
 * Date: 2022/6/28 15:46
 */
function time_tran($push_time)
{
    $show_time = strtotime($push_time);
    $dur = time() - $show_time;
    if ($dur < 0) {
        return $push_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前发布';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) { // 3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $push_time;
                    }
                }
            }
        }
    }
}

/**
 * 图片前缀
 * Create by Peter Yang
 * 2022-06-27 14:15:39
 * @param string $url
 * @return string
 */
function getImagePrefix(string $url)
{


    //默认图片
    if (!$url) {


        return env("IMAGE_DOMAIN") . "/logo_default.png";
    }


    $img = Hook::applyFilter('the_image', $url, env("IMAGE_DOMAIN"));

    if ($img !== null) {


        return $img;
    }


    return env("IMAGE_DOMAIN") . "/uploads/" . $url;

}


/**
 * 获取详情页链接
 * Create by Peter Yang
 * 2022-06-27 14:37:52
 * @param $item
 * @return string
 */
function getDetailUrl($item): string
{


    if (!$item) {

        return '';
    }

    $prefix = request()->server('REQUEST_SCHEME') . '://' . request()->getHttpHost();


    $category = $item->category;


    if (!$category) {


        return '';
    }


    if ((parse_url(getOption("m_domain"))['host'] ?? "") === request()->getHost()) {

        $detail_name = Cache::get('category:detail:mobile_' . $category->id);

    } else {


        $detail_name = Cache::get('category:detail:pc_' . $category->id);


    }


    if (!$detail_name) {

        return '';

    }

    $id = $item->id;

    $detail_name = str_replace('{id}', $id, $detail_name);

    return $prefix . '/' . $detail_name;


}


/**
 * 获取详情页链接(cli版)
 * @param Article $item
 * @param string $mode
 * @return string
 */
function getDetailUrlForCli(Article $item, string $mode = 'pc'): string
{

    if (!$item) {

        return '';
    }


    $category = $item->category;


    if (!$category) {

        return '';
    }


    if ($mode === 'pc') {


        $prefix = getOption('domain', "");

        $detail_name = Cache::get('category:detail:pc_' . $category->id);

    } else {

        $prefix = getOption('m_domain', "");

        $detail_name = Cache::get('category:detail:mobile_' . $category->id);
    }


    if (!$detail_name) {

        return '';

    }

    $id = $item->id;

    $detail_name = str_replace('{id}', $id, $detail_name);

    return $prefix . '/' . $detail_name;


}


/**
 * 解析控制器
 * Create by Peter Yang
 * 2022-06-28 10:38:34
 * @param string $controller
 * @param string $action
 * @param array $input
 * @return Closure
 */
function make(string $controller, string $action, array $input = [])
{

    return function () use ($controller, $action, $input) {


        if (count($input) > 0) {


            foreach ($input as $key => $value) {


                request()->merge([$key => $value]);


            }

        }


        $c = App::make($controller);


        return App::call([$c, $action]);

    };

}


/**
 * 获取栏目链接(包含域名)
 * Create by Peter Yang
 * 2022-06-28 14:10:09
 * @param Category $category
 * @return string
 */
function getChannelUrl(Category $category): string
{


    $type = getHostPrefix();

    if ($type === "m") {

        $list_name = Cache::get('category:list:mobile_' . $category->id);

    } else {


        $list_name = Cache::get('category:list:pc_' . $category->id);
    }


    $prefix = request()->server('REQUEST_SCHEME') . '://' . request()->getHttpHost();


    return $prefix . "/" . $list_name . "/";

}


/**
 * 获取列表路由(不包含域名)
 * Create by Peter Yang
 * 2022-09-24 16:07:36
 * @param Category $category
 * @param string $mode
 * @return mixed
 */
function getCategoryListRoute(Category $category, string $mode = "pc")
{


    if ($mode === "m") {

        return Cache::get('category:list:mobile_' . $category->id);

    }

    return Cache::get('category:list:pc_' . $category->id);

}


/**
 * 获取当前域名
 * Create by Peter Yang
 * 2023-02-22 13:59:02
 * @return string
 */
function getHttpPrefix(): string
{


    return request()->server('REQUEST_SCHEME') . '://' . request()->getHttpHost();
}


/**
 * 获取自定义模块内容
 * Create by Peter Yang
 * 2022-06-30 10:57:42
 * @param string $code
 * @return array
 */
function getModeByCode(string $code): array
{


    $mode = \Ycore\Models\Mode::where('code', $code)->first();

    if (!$mode) {


        return [];
    }

    $list = [];

    foreach ($mode->list as $key => $value) {

        foreach ($value as $kk => $vv) {

            switch ($mode->field_list[$kk]['type']) {


                case 1:

                    $id = getObjPlus($vv, 'id');

                    $article = Article::where('id', $id)->with('category')->first();

                    if (!$article) {

                        $list[$key][$mode->field_list[$kk]['title']] = [];

                    } else {

                        $list[$key][$mode->field_list[$kk]['title']] = $article;
                    }

                    break;

                default:

                    $list[$key][$mode->field_list[$kk]['title']] = $vv;
            }


        }

    }


    return $list;

}


/**
 * 判断当前是否被选中
 * Create by Peter Yang
 * 2023-02-22 14:00:01
 * @param Category $category
 * @param $cid
 * @param $className
 * @return string
 */
function getSelectedByCategory(Category $category, $cid, $className = 'active'): string
{

    $c = Category::where('id', $cid)->first();

    if (!$c) {

        return '';
    }


    if ($category->id == $c->pid || $category->id == $c->id) {


        return $className;
    }


    return '';
}


/**
 * 获取内容缩略
 * Create by Peter Yang
 * 2022-07-01 10:58:24
 * @param Article $article
 * @param int $length
 * @return string
 */
function getContentShort(Article $article, int $length): string
{


    $html = QueryList::html($article->content);

    $text = $html->find("*")->text();

    $text = preg_replace('/\s+/u', '', $text);


    $text = str_replace(["\t", "\n", "\r"], "", $text);


    return mb_substr(trim($text), 0, $length);
}


/**
 * 获取文章内容
 * Create by Peter Yang
 * 2023-02-22 14:00:19
 * @param Article $article
 * @return array|string|string[]|null
 * @throws JsonException
 */
function getContent(Article $article)
{

    $content = $article->content;


    $html = QueryList::html($content);


    //将内容中的h1和h2标签替换成h3
    $html->find("h1")->map(function (\QL\Dom\Elements $elements) {


        $h = $elements->html();

        $elements->replaceWith("<h3>" . $h . "</h3>");


    });


    $html->find("h2")->map(function (\QL\Dom\Elements $elements) {


        $h = $elements->html();

        $elements->replaceWith("<h3>" . $h . "</h3>");


    });


    //给图片添加完整域名
    $html->find('img')->map(function (\QL\Dom\Elements $elements) use (&$imgIndex, $article) {


        $elements->removeAttr("title");
        $elements->removeAttr("alt");
        $elements->removeAttr("data-src");

        $url = $elements->attr('src');


        if (!preg_match("/^(http|https):\/\//", $url)) {


            if (preg_match("/^\/api(\/uploads\/.*?)$/", $url, $mmm)) {


                $url = $mmm[1] ?? "";


            }


            $elements->attr('src', env('IMAGE_DOMAIN') . $url);

        }


    });


    //移除外部a标签
    $html->find('a')->map(function (\QL\Dom\Elements $elements) {


        $elements->removeAttr("alt");
        $elements->removeAttr("title");


        $not_link = Hook::applyFilter('not_remove_link', $elements->attr('href'));


        if ($not_link === null || $not_link === false) {

            $html = $elements->html();


            $elements->replaceWith($html);

        }


    });


    $str = $html->getHtml();


    //设置标签链接
    $str = Seo::setTagLinkForContent($article, $str);


    $html2 = QueryList::html($str);


    $imgIndex = 1;


    //添加图片描述
    $html2->find("img")->map(function (\QL\Dom\Elements $elements) use (&$imgIndex, $article) {


        $elements->attr("alt", $article->title . "[图{$imgIndex}]");


        $imgIndex++;

    });


    $str = $html2->getHtml();


    //移除游侠下载链接
    $str = preg_replace("/https:\/\/app\.ali213\.net\/[0-9a-zA-Z\/]+\.html/", getDetailUrl($article), $str);


    if ($article->category->pid === config('category.news')) {

        $str .= "<p style='margin-top: 30px'>以上就是<a href='" . getOption('domain',
                "") . "'>" . getOption('site_name') . "</a>为你带来的\"<a href='" . getDetailUrl($article) . "' target='_blank'>" . $article->title . "</a>\",更多有趣好玩的热门资讯攻略，请持续关注<a href='" . getOption('domain',
                '') . "'>" . getOption('site_name') . "</a>!</p>";
    }


    return $str;
}


/**
 * Create by Peter Yang
 * 2023-02-22 14:02:22
 * @param int $id
 * @return Article|\Ycore\Models\Base|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
 */
function getArticleById(int $id)
{


    return ArticleDetailModel()->where('id', $id)->first();
}


function getArticleByIdList(array $ids)
{


    return ArticleDetailModel()->whereIn('id', $ids)->get();
}


/**
 * 获取文章主图
 * Create by Peter Yang
 * 2022-07-12 16:28:17
 * @return string
 */
function getImage($article): string
{

    if ($article instanceof Article) {


        return getImagePrefix(getObjPlus($article, 'img'));
    }


    return "";
}


/**
 * 获取文章标题
 * Create by Peter Yang
 * 2022-07-12 16:57:14
 * @return string
 */
function getTitle($article): string
{


    if ($article instanceof Article) {


        $fTitle = Hook::applyFilter('the_title', $article->title, $article);

        if ($fTitle !== null) {

            return $fTitle;
        }

        return $article->title;
    }


    return "";
}


/**
 * 获取分类名称
 * Create by Peter Yang
 * 2022-08-02 15:09:48
 * @param Article $article
 * @return mixed|null
 */
function getCategoryName(Article $article)
{


    return optional($article->category)->name;
}


/**
 * 是否有安卓下载
 * Create by Peter Yang
 * 2022-07-14 15:01:44
 * @param Article $article
 * @return bool
 */
function androidHas(Article $article): bool
{

    $special_ex = $article->special_ex;

    $android = trim($special_ex[config('static.android_download_link')]);

    if ($android) {


        return true;
    }

    return false;
}


/**
 * 是否有苹果下载
 * Create by Peter Yang
 * 2022-07-14 15:03:07
 * @param Article $article
 * @return bool
 */
function iosHas(Article $article): bool
{


    $special_ex = $article->special_ex;

    $ios = trim($special_ex[config('static.ios_download_link')]);

    if ($ios) {


        return true;
    }

    return false;
}


/**
 * 获取详情页的seo标题
 * Create by Peter Yang
 * 2022-07-15 16:43:53
 * @param Article $article
 * @return string
 */
function getSeoTitleForDetail(Article $article): string
{

    $seoTitle = $article->seo_title;

    if ($seoTitle) {


        return $seoTitle;
    }


    return Seo::getSeoTitle($article);

}


/**
 * 获取详情页seo关键字
 * Create by Peter Yang
 * 2022-07-15 16:52:05
 * @param Article $article
 * @return string
 */
function getSeoKeywordForDetail(Article $article): string
{

    $seoKeyword = $article->seo_keyword;

    if ($seoKeyword) {

        return $seoKeyword;
    }

    return $article->title;
}

/**
 * 获取详情页seo描述
 * Create by Peter Yang
 * 2022-07-15 16:55:20
 * @param Article $article
 * @return string
 */
function getSeoDescForDetail(Article $article): string
{

    $seoDesc = $article->seo_desc;

    if ($seoDesc) {


        return $seoDesc;
    }


    return getContentShort($article, 100);
}


/**
 * 获取栏目seo标题
 * Create by Peter Yang
 * 2022-07-15 17:00:04
 * @param Category $category
 * @return string
 */
function getSeoTitleForChannel(Category $category): string
{


    return $category->seo_title;
}


/**
 * 获取栏目seo关键字
 * Create by Peter Yang
 * 2022-07-15 17:03:06
 * @param Category $category
 * @return string
 */
function getSeoKeywordForChannel(Category $category): string
{


    return $category->seo_keywords;
}


/**
 * 获取栏目seo描述
 * Create by Peter Yang
 * 2022-07-15 17:03:09
 * @param Category $category
 * @return string
 */
function getSeoDescForChannel(Category $category): string
{


    return $category->seo_description;
}


/**
 * 获取文章关联的单一文章（obj单一关联）
 * Create by Peter Yang
 * 2022-08-02 11:37:00
 */
function getExGame(Article $article)
{


    $gameId = getObjPlus($article, 'ex.' . config('static.news_game_field'));

    if (!$gameId) {


        return false;
    }

    $item = getArticleById($gameId);

    if (!$item) {

        return false;
    }

    return $item;

}


/**
 * 获取文章关联的文章(association_object列表关联)
 * Create by Peter Yang
 * 2022-09-09 14:01:23
 * @param Article $article
 * @param int $limit
 * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
 */
function getExGameList(Article $article, int $limit = 0): \Illuminate\Database\Eloquent\Collection|array
{


    $mainId = $article->id;

    $query = ArticleListModel()->whereIn('id', function ($query) use ($mainId) {

        $query->select('slave')->from('article_association_object')->where('main', $mainId);

    })->orderBy('push_time', 'desc');

    if ($limit !== 0) {

        $query->limit($limit);
    }

    return $query->get();

}


/**
 * 获取文章关联的游戏(association_object列表关联)带分页
 * Create by Peter Yang
 * 2022-09-29 11:30:14
 * @param Article $article
 * @param string $pageUrl
 * @param int $page
 * @param int $size
 * @return Closure|mixed|object
 */
function getExGameListWithPage(Article $article, string $pageUrl = "", int $page = 1, int $size = 10)
{


    $mainId = $article->id;

    $query = ArticleListModel()->whereIn('id', function ($query) use ($mainId) {

        $query->select('slave')->from('article_association_object')->where('main', $mainId);

    });


    $offset = ($page - 1) * $size;

    $query->offset($offset)->limit($size);


    return $query->seoPaginate($size, ['*'], $page, $pageUrl);

}


/**
 * 获取文章相关文章(根据tag和单一obj查询)
 * Create by Peter Yang
 * 2022-08-05 20:02:12
 * @param Article $article
 * @param array|int|string $categoryName 限制特定分类id(如果是多个分类，取第一个，故拓展表必须是同一个)
 * @param int $limit
 * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
 */
function getRelated(Article $article, array|int|string $categoryName, int $limit = 5)
{


    $category = getCategoryIds($categoryName);


    $ex_table = CategoryController::getExpandTableName($category->first());


    //获取当前文章所有标签
    $tagList = $article->article_tag->pluck('tag_id')->all();


    //获取当前文章中关联的单一文章id
    $obj = $article->ex[config('static.news_game_field')] ?? false;


    return ArticleListModel()->where('id', "!=", $article->id)->whereIn('category_id',
        $category->all())->where(function (Illuminate\Database\Eloquent\Builder $query) use (
        $tagList,
        $article,
        $ex_table,
        $obj
    ) {

        //查询其他文章中是否有关联当前文章
        $query->whereRaw('id in (SELECT ' . $ex_table . '.article_id FROM ' . $ex_table . ' WHERE ' . config('static.news_game_field') . ' = ? )', [$article->id]);

        //查找同一个标签的文章
        if (count($tagList) > 0) {

            $query->orWhereRaw("id in (select article_tag.article_id from article_tag where article_tag.tag_id in (" . join(",", $tagList) . ") and article_tag.article_id != " . $article->id . ")");
        }


        //查询当前文章绑定的文章是否也被其他文章绑定
        if ($obj) {

            $query->orwhereRaw('EXISTS(select * from ' . $ex_table . ' where article.id = ' . $ex_table . '.article_id and ' . config('static.news_game_field') . ' = ?)',
                [$obj]);

        }

    })->limit($limit)->orderBy('push_time', 'desc')->get();


}


/**
 * 获取标签相关的文章（可以用于查询相关版本）
 * Create by Peter Yang
 * 2022-10-22 17:11:37
 * @param Article $article
 * @param int $categoryId
 * @param int $limit
 * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
 */
function getRelatedByTag(Article $article, int|string|array $categoryName, int $limit = 5)
{

    $tagList = $article->article_tag->pluck('tag_id')->all();

    if (count($tagList) <= 0) {


        return collect([]);
    }


    $query = ArticleListModel()->where('id', "!=", $article->id)
        ->whereRaw("id in (select article_tag.article_id from article_tag where article_tag.tag_id in (" . join(",", $tagList) . ") and article_tag.article_id != " . $article->id . ")")
        ->limit($limit);


    $category = getCategoryIds($categoryName);


    $query->whereIn('category_id', $category->all());


    return $query->orderBy('push_time', 'desc')->get();


}


/**
 * 获取详情模型
 * Create by Peter Yang
 * 2023-02-21 16:46:12
 * @return Article
 */
function ArticleDetailModel(): Illuminate\Database\Eloquent\Builder
{

    return Article::with('category');
}


/**
 * 获取列表、搜索的文章模型
 * Create by Peter Yang
 * 2022-08-20 11:27:15
 * @return Article
 */
function ArticleListModel(): Illuminate\Database\Eloquent\Builder
{

    //(special_id != 0 and exists (select 1 from `special` where `article`.`special_id` = `special`.`id` and `real_pc_hide` = 2))


    $spiderCondition = "";

    $isSpider = isSearchEngine(request()->header('user-agent', ""));


    if ($isSpider) {

        $spiderCondition = " 1 = 1 ";

    } else {

        $spiderCondition = " 1 = 2 ";
    }


    $a = getHostPrefix();


    if ($a === "m") {

        return Article::with('category')->whereRaw("(special_id = 0 or (special_id != 0 and exists (select 1 from `special` where `article`.`special_id` = `special`.`id` and ( (`list_mobile_hide` = 2  and `list_mobile_without_search_hide` = 2 ) or ( `list_mobile_without_search_hide` = 1  and  " . $spiderCondition . "  ) ) )) )");

    }


    return Article::with('category')->whereRaw("(special_id = 0 or (special_id != 0 and exists (select 1 from `special` where `article`.`special_id` = `special`.`id` and ( (`list_pc_hide` = 2  and `list_pc_without_search_hide` = 2 ) or ( `list_pc_without_search_hide` = 1  and  " . $spiderCondition . "  ) ) )) )");
}


/**
 * 判断是否是搜索引擎
 * Create by Peter Yang
 * 2023-01-06 11:50:15
 * @param string $userAgent
 * @return bool
 */
function isSearchEngine(string $userAgent): bool
{

    $str = [
        'Baiduspider',
        'Googlebot',
        '360Spider',
        'Bytespider',
        'bingbot',
        'Sogou',
        'Sosospider',
        'JikeSpider',
        'YoudaoBot',
        'MSNBot',
        "bot",
        'spider'
    ];


    foreach ($str as $s) {


        if (str_contains($userAgent, $s)) {


            return true;
        }


    }

    return false;
}


/**
 * 获取域名前缀,例：域名是m.yycms.com，返回的就是m
 * Create by Peter Yang
 * 2022-08-20 11:15:18
 */
function getHostPrefix(): string
{

    try {

        return str_replace("." . env("TOP_DOMAIN"), "", request()->host()) ?: "www";

    } catch (\Exception $exception) {


        return "www";

    }


}

/**
 * 处理文章一对多关系
 * Create by Peter Yang
 * 2022-09-26 14:49:45
 * @param int $mainId
 * @param array $expand_data
 */
function dealArticleAssociationObject(int $mainId, array $expand_data)
{

    foreach ($expand_data as $value) {


        if ($value['type'] === 7) {


            foreach ($value['value'] as $v) {


                ArticleAssociationObject::updateOrCreate(
                    ['main' => $mainId, 'slave' => $v['id']],
                    ['main' => $mainId, 'slave' => $v['id']]
                );

            }

        }


    }

}


/**
 * 获取当前文章在其他文章的关联列表(如：查找当前游戏在哪些游戏合集中)
 * Create by Peter Yang
 * 2023-02-22 14:19:45
 * @param Article $article 当前文章
 * @param int $otherArticleCid 管理文章id(如游戏合集)
 * @param $limit
 * @return Article[]|\App\Models\Base[]|array|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|\LaravelIdea\Helper\App\Models\_IH_Article_C|\LaravelIdea\Helper\App\Models\_IH_Base_C
 */
function getArticleInOtherArticleList(Article $article, int $otherArticleCid, $limit = 4)
{

    $list = ArticleAssociationObject::where('slave', $article->id)->withWhereHas('mainArticle',
        function ($query) use ($otherArticleCid) {

            $query->where('category_id', $otherArticleCid);

        })->limit($limit)->get();


    if (!$list) {


        return [];
    }

    $ids = $list->pluck('main')->all();

    return getArticleByIdList($ids);


}

/**
 * 获取下一篇文章（获取不到就随机）
 * Create by Peter Yang
 * 2022-09-28 13:54:58
 * @param Article $article
 * @param $limit
 * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
 * @throws Exception
 */
function nextArticleOrRandom(Article $article, $limit = 1)
{

    $list = getArticleByCategoryName($article->category->id, $limit, 0, [['id', '>', $article->id]]);


    if ($list->count() > 0) {

        return $list;
    }


    return getArticleByCategoryNameWithRandom($article->category->id, $limit);


}


/**
 * 获取上一篇文章（获取不到就随机）
 * Create by Peter Yang
 * 2022-09-28 13:54:58
 * @param Article $article
 * @param $limit
 * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
 * @throws Exception
 */
function prevArticleOrRandom(Article $article, $limit = 1)
{

    $list = getArticleByCategoryName($article->category->id, $limit, 0, [['id', '<', $article->id]]);


    if ($list->count() > 0) {

        return $list;
    }

    return getArticleByCategoryNameWithRandom($article->category->id, $limit);


}

/**
 * 获取发布时间格式
 * Create by Peter Yang
 * 2022-10-18 11:51:35
 * @param Article $article
 * @param string $format
 * @return string
 */
function getPushTime(Article $article, string $format = "Y-m-d"): string
{
    $time = strtotime($article->push_time);

    return date($format, $time);
}


/**
 * 获取 游戏/应用 版本号
 * Create by Peter Yang
 * 2023-01-04 11:22:04
 * @param Article $article
 * @return array|mixed|string
 */
function getVersion(Article $article): string
{


    $version = getObjPlus($article, 'ex.version');


    if (!$version) {

        return "";
    }


    if (!str_contains($version, "v") && !str_contains($version, "V")) {


        return "V" . $version;

    }

    return $version;

}


function getDesc(Article $article): string
{
    $version_str = [
        '手机版',
        '最新版',
        '官方版',
        '正式版',
        '最新安卓版',
        '官方安卓版'
    ];


    if (!str_contains($article->title, "版")) {


        return $version_str[array_rand($version_str)];

    }

    return "";


}


/**
 * 设置配置项（包含更新）
 * Create by Peter Yang
 * 2023-01-30 14:58:45
 * @param string $key
 * @param mixed $value
 * @param bool $autoload 自动加载
 * @throws JsonException
 */
function setOption(string $key, mixed $value, bool $autoload = false): void
{

    $type = "string";

    if (is_numeric($value)) {

        $type = "int";
    }


    if (is_array($value)) {

        $type = "array";

        $value = json_encode($value, JSON_THROW_ON_ERROR);
    }

    if ($autoload) {

        $autoloadNum = 1;

    } else {

        $autoloadNum = 0;
    }


    Options::updateOrCreate(['key' => $key],
        ['key' => $key, 'value' => $value, 'type' => $type, 'autoload' => $autoloadNum]);


}


/**
 * Create by Peter Yang
 * 2023-01-30 15:14:16
 * @param string $key
 * @param mixed|null $default
 * @return mixed
 * @throws JsonException
 */
function getOption(string $key, mixed $default = null): mixed
{


    if (app()->has('option_' . $key)) {


        return app()->get('option_' . $key);
    }


    $item = Options::where('key', $key)->first();


    if (!$item) {


        return $default;
    }

    $value = $item->value;

    if ($item->type === "array") {

        $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }


    return $value;
}


/**
 * 获取分类（与getCategoryIds不同的是getCategory返回的是category模型对象）
 * Create by Peter Yang
 * 2023-02-23 14:57:40
 * @param int|string|array $categoryName
 * @param int $limit
 * @param bool $exceptSelf 是否包含自己,默认包含自己
 * @param array $querys
 * @return \Illuminate\Database\Eloquent\Collection
 */
function getCategory(int|string|array $categoryName, int $limit = 15, $exceptSelf = false, array $querys = []): \Illuminate\Database\Eloquent\Collection
{

    $category = getCategoryIds($categoryName, $exceptSelf);

    $q = Category::whereIn('id', $category->all());

    if ($querys) {

        $q->where($querys);
    }

    return $q->limit($limit)->get();

}


/**
 * 自动设置一对多关系
 * @param Article $article
 * @return bool
 */
function autoAssociationObject(Article $article): bool
{

    $category = Category::where('id', $article->category_id)->first();


    $collect = Collect::whereIn('son_id', [$category->id, $category->parent->id])->first();


    //是否已经设置了关联
    $isFind = Article::whereRaw(" EXISTS(select *  from article_association_object  WHERE `slave` = article.id)")->where('id', $article->id)->first();

    if ($isFind) {

        return false;
    }

    if ($collect) {


        $content = $article->content;


        $t = CollectTag::whereRaw("? like CONCAT('%',title,'%')", [$content])->limit(10)->get();


        if ($t->count() > 0) {


            $mainList = Article::where('category_id', $collect->category_id)->where(function ($query) use ($t) {


                foreach ($t as $v) {

                    $query->orWhere('title', 'like', '%' . $v->title . '%');
                }

            })->limit(4)->get();


            foreach ($mainList as $main) {

                try {

                    ArticleAssociationObject::create([
                        'main' => $main->id,
                        'slave' => $article->id
                    ]);

                } catch (\Exception $exception) {

                }


                if (app()->runningInConsole()) {


                    echo $article->title . "=>" . $mainList->pluck('title')->join(",") . PHP_EOL;

                }


            }


            return true;

        }


    }

    return false;
}

/**
 * 根据标签id查询标签详情
 * @param $tagId
 * @param $notFoundReturn404
 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|\Ycore\Models\Base|\Ycore\Models\Tag|null
 */
function getTagById($tagId, $notFoundReturn404 = false)
{

    $tag = \Ycore\Models\Tag::where('id', $tagId)->first();

    if (!$tag && $notFoundReturn404) {

        abort(404);
    }

    return $tag;

}


/**
 * 根据标签id查询文章
 * @param $tagId
 * @param $size
 * @param $page
 * @param $path
 * @param $order
 * @return Closure|mixed|object
 */
function getArticleByTagId($tagId, $size = 10, $page = 1, $path = "/tag/list-[PAGE].html", $order = ['push_time', 'desc'])
{

    return ArticleListModel()->with('article_tag')->whereHas('article_tag', function ($query) use ($tagId) {


        $query->where('tag_id', $tagId);

    })->orderBy($order[0], $order[1])->seoPaginate(15, ['*'], $page,
        "/tag/" . $tagId . "/list-[PAGE]" . ".html");

}


/**
 * 加载当前主题的function
 * @return void
 * @throws JsonException
 */
function loadTheme()
{

    if (file_exists(base_path('theme/' . getOption("theme", 'demo') . '/function.php'))) {


        include base_path('theme/' . getOption("theme", 'demo') . '/function.php');

    }

}


/**
 * 加载自定义视图
 * @param string $view
 * @param string $seoTitle
 * @param string $seoKeyword
 * @param string $seoDesc
 * @param $data
 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
 */
function customView(string $view, string $seoTitle = "", string $seoKeyword = "", string $seoDesc = "", $data = [])
{

    $data['_seo_title'] = $seoTitle;
    $data['_seo_keyword'] = $seoKeyword;
    $data['_seo_desc'] = $seoDesc;


    return view($view, $data);
}


/**
 * 主题列表
 * @return array
 */
function themeList(): array
{


    $dirs = File::directories(base_path('theme'));


    return array_map(function ($item) {


        return basename($item);
    }, $dirs);

}


/**
 * 是否显示首页
 * @param $paginator
 * @return bool
 */
function showFirstPage($paginator): bool
{


    return !$paginator->onFirstPage();
}


/**
 * 是否显示最后一页
 * @param $paginator
 * @return bool
 */
function showLastPage($paginator): bool
{


    return $paginator->lastPage() !== $paginator->currentPage();
}


/**
 * 渲染页码
 * @param $paginator
 * @param  $showSize
 *
 */
function viewPage($paginator, $showSize = 8): \Tightenco\Collect\Support\Collection|\Illuminate\Support\Collection
{


    //最大页码数
    $maxPage = ceil($paginator->total() / $paginator->perPage());


    if ($paginator->currentPage() < $showSize) {

        //显示的起始页码
        $startPage = 1;

    } else {

        $startPage = ((int)($paginator->currentPage() / $showSize)) * $showSize;

    }


    if ($startPage === $paginator->currentPage() && ($startPage - 1) !== 0) {

        $startPage--;
    }


    $add = $startPage + $showSize;


    if ($add > $maxPage) {


        $add = $maxPage;
    }


    $list = \collect([]);

    for ($i = $startPage; $i <= $add; $i++) {


        $list->push($i);

    }


    return $list;
}


