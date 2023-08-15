<?php


namespace Ycore\Tool;

use Illuminate\Database\Eloquent\Builder;

class Search
{

    static function searchList(Builder $builder, $search)
    {


        $search = json_decode($search, true);


        $custom = [];

        foreach ($search as $key => $value) {


            $val = trim($value['value']);

            if (!isset($val) || $val === '') continue;

            if (isset($value['custom'])) {

                $custom[] = $value;

                continue;
            }

            if (!isset($value['field'])) continue;


            switch ($value['condition']) {

                case 'like':

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], 'like', '%' . $val . "%");

                        });

                        break;
                    }

                    $builder->where($value['field'], 'like', '%' . $val . "%");

                    break;


                case '>':

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], '>', $val);

                        });

                        break;
                    }

                    $builder->where($value['field'], '>', $val);

                    break;


                case '<':

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], '<', $val);

                        });

                        break;
                    }

                    $builder->where($value['field'], '<', $val);

                    break;


                case '>=':

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], '>=', $val);

                        });

                        break;
                    }


                    $builder->where($value['field'], '>=', $val);

                    break;


                case '<=':

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], '<=', $val);

                        });

                        break;
                    }

                    $builder->where($value['field'], '<=', $val);

                    break;


                case '!=':

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], '!=', $val);

                        });

                        break;
                    }

                    $builder->where($value['field'], '!=', $val);

                    break;


                case '<>':

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], '<>', $val);

                        });

                        break;
                    }

                    $builder->where($value['field'], '<>', $val);

                    break;


                case "=":

                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], '=', $val);

                        });

                        break;
                    }

                    $builder->where($value['field'], '=', $val);

                    break;


                default :


                    if (count(explode('.', $value['field'])) > 1) {

                        $builder->whereHas(explode('.', $value['field'])[0], function ($query) use ($value, $val) {

                            $query->where(explode('.', $value['field'])[1], $val);

                        });

                        break;
                    }

                    $builder->where($value['field'], $val);

                    break;


            }


        }

        return $custom;

    }


}
