<?php

namespace Ycore\Tool;

class Hook
{


    protected static array $filters = [];


    /**
     * 应用过滤器,不存在则返回null
     * @param string $filterName
     * @param ...$arg
     * @return mixed|true
     */
    public static function applyFilter(string $filterName, ...$arg)
    {


        foreach (self::$filters as $filter) {

            if ($filter['filterName'] === $filterName) {

                return call_user_func_array($filter['callback'], $arg);

            }

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

        self::$filters[] = ['filterName' => $filterName, 'callback' => $callback];

    }


    /**
     * 判断过滤器是否存在
     * Create by Peter Yang
     * 2023-11-26 11:38:23
     * @param string $filterName
     * @return bool
     */
    public static function exist(string $filterName): bool
    {
        foreach (self::$filters as $filter) {

            if ($filter['filterName'] === $filterName) {

                return true;
            }

        }

        return false;
    }


}
