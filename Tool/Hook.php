<?php

namespace Ycore\Tool;

class Hook
{


    protected static array $filters = [];


    /**
     * 应用过滤器
     * @param string $filterName
     * @param ...$arg
     * @return mixed|true
     */
    public static function applyFilter(string $filterName, ...$arg)
    {


        if (array_key_exists($filterName, self::$filters)) {

//            dd($arg);

            return call_user_func_array(self::$filters[$filterName], $arg);
        }


        return null;

    }


    /**
     * 应用过滤器
     * @param string $filterName
     * @param $callback
     * @return void
     */
    public static function addFilter(string $filterName, $callback)
    {

        self::$filters[$filterName] = $callback;

    }


}
