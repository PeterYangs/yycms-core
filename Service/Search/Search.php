<?php


namespace Ycore\Service\Search;


use Ycore\Tool\Json;

class Search implements SearchInterface
{

    protected $allowModelList;

    /**
     * Create by Peter
     * 2019/09/27 13:34:36
     * Email:904801074@qq.com
     * @param string $model
     * @param array $condition
     * @param array $label
     * @param int $page
     * @param array $defaultCondition
     * @param string $namespace 命名空间
     * @param array $with 模型关联
     * @return array
     * @throws \Exception
     */
    function AlertSearch(string $model, array $condition, array $label, int $page = 1,array $defaultCondition=[],string $namespace='\Ycore\Models\\',$with=[]):array
    {
        // TODO: Implement AlertSearch() method.


        if(!in_array($model, $this->allowModelList, true)) {
            throw new \Exception('该模型不允许搜索', 2);
        }

        $p=$page;

        //命名空间写死
        $namespace="\Ycore\Models\\";

        //获取模型对象
        $obj=$namespace.$model;

        //返回查询器对象
        $list=$obj::orderBy('id','desc');


        //输入框条件处理
        $this->dealCondition($list,$condition);

        //自定义条件处理
        $this->dealCondition($list,$defaultCondition);

        //设置关联模型
        foreach ($with as $key=>$value){

            $list->with($value);
        }

        //执行模型关联预处理
        foreach ($label as $key=>$value){

            $withModel=explode('.',$value['key']);

            if(count($withModel)>1) {
                $list->with($withModel[0]);
            }


        }

        //分页
        $list=paginate($list,$p);


        return $list;

    }

    /**
     * 处理条件
     * Create by Peter
     * 2020/04/28 14:03:32
     * Email:904801074@qq.com
     * @param \Illuminate\Database\Eloquent\Builder $list
     * @param $condition
     */
    function dealCondition(\Illuminate\Database\Eloquent\Builder $list,$condition){



        //筛选查询条件
        foreach ($condition as $key=>$value){


            if($value['value']==="") continue;


            switch ($value['condition']){

                case "like":

                    $value['value']='%'.$value['value'].'%';

                    break;



            }



            $withField=explode('.',$value['field']);



            if(count($withField)==1){

                $list->where($value['field'],$value['condition'],$value['value']);

                continue;
            }

            $list->whereHas($withField[0],function ($query)use($value,$withField){

                $query->where($withField[1],$value['condition'],$value['value']);
            });


        }


    }


    public function __construct($allModelList)
    {



        $this->allowModelList=$allModelList;
    }
}
