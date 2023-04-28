<?php


namespace Ycore\Tool;

use Illuminate\Database\Eloquent\Builder;

class Search
{

    static function searchList(Builder $builder,$search){


        $search=json_decode($search,true);


        $custom=[];

        foreach ($search as $key=>$value){


            if(!isset($value['value'])||$value['value']==='') continue;

            if(isset($value['custom'])){

                $custom[]=$value;

                continue;
            }

            if(!isset($value['field'])) continue;


            switch ($value['condition']){

                case 'like':

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'like','%'.$value['value']."%");

                        });

                        break;
                    }

                    $builder->where($value['field'],'like','%'.$value['value']."%");

                    break;


                case '>':

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'>',$value['value']);

                        });

                        break;
                    }

                    $builder->where($value['field'],'>',$value['value']);

                    break;


                case '<':

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'<',$value['value']);

                        });

                        break;
                    }

                    $builder->where($value['field'],'<',$value['value']);

                    break;


                case '>=':

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'>=',$value['value']);

                        });

                        break;
                    }


                    $builder->where($value['field'],'>=',$value['value']);

                    break;


                case '<=':

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'<=',$value['value']);

                        });

                        break;
                    }

                    $builder->where($value['field'],'<=',$value['value']);

                    break;


                case '!=':

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'!=',$value['value']);

                        });

                        break;
                    }

                    $builder->where($value['field'],'!=',$value['value']);

                    break;


                case '<>':

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'<>',$value['value']);

                        });

                        break;
                    }

                    $builder->where($value['field'],'<>',$value['value']);

                    break;


                case "=":

                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],'=',$value['value']);

                        });

                        break;
                    }

                    $builder->where($value['field'],'=',$value['value']);

                    break;



                default :


                    if(count(explode('.',$value['field']))>1){

                        $builder->whereHas(explode('.',$value['field'])[0],function ($query)use ($value){

                            $query->where(explode('.',$value['field'])[1],$value['value']);

                        });

                        break;
                    }

                    $builder->where($value['field'],$value['value']);

                    break;


            }



        }

        return $custom;

    }


}
