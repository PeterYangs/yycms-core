<?php


namespace Ycore\Tool;


use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Container\Container;

class AcademyPaginator extends LengthAwarePaginator
{

    /**
     * 重写页面 URL 实现代码，去掉分页中的问号，实现伪静态链接
     * @param int $page
     * @return string
     */
    public function url($page)
    {

        if ($page <= 0) {
            $page = 1;
        }

        $pathCustom=$this->options['pathCustom'];

        $pathCustom=str_replace('[PAGE]',$page,$pathCustom);

        return $pathCustom;
    }



    /**
     * 重写当前页设置方法
     *
     * @param  int  $currentPage
     * @param  string  $pageName
     * @return int
     */
    protected function setCurrentPage($currentPage, $pageName)
    {


        return $this->options['currentPage']??1;

    }


    /**
     * 将新增的分页方法注册到查询构建器中，以便在模型实例上使用
     * 注册方式：
     * 在 AppServiceProvider 的 boot 方法中注册：AcademyPaginator::rejectIntoBuilder();
     * 使用方式：
     * 将之前代码中在模型实例上调用 paginate 方法改为调用 seoPaginate 方法即可：
     * Article::where('status', 1)->seoPaginate(15, ['*'], 'page', page);
     */
    public static function injectIntoBuilder()
    {

        /**
         * $perPage 查询条数
         * $columns 查询字段
         * $page 当前页面
         * $pathCustom 自定义路径 例：/list_[PAGE].html
         */
        Builder::macro('seoPaginate', function ($perPage, $columns, $page,$pathCustom='',$pageName='page') {


            $page=(int)$page;

            $perPage = $perPage ?: $this->model->getPerPage();

            $items = ($total = $this->toBase()->getCountForPagination())
                ? $this->forPage($page, $perPage)->get($columns)
                : $this->model->newCollection();

            $options = [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
                'pathCustom'=>request()->server('REQUEST_SCHEME').'://'.request()->getHttpHost(). $pathCustom,
                'currentPage'=>$page
            ];

            return Container::getInstance()->makeWith(AcademyPaginator::class, compact(
                'items', 'total', 'perPage', 'page', 'options'
            ));
        });
    }




}
