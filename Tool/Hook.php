<?php

namespace Ycore\Tool;

class Hook
{


    protected static array $filters = [];

    protected static array $actions = [];


    /**
     * 应用过滤器,不存在则返回null(过滤器最好不要返回null，否则会被视为无过滤器)
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
     * @param string $actionName
     * @param ...$arg
     * @return void
     */
    public static function applyAction(string $actionName, ...$arg)
    {

        foreach (self::$actions as $action) {

            if ($action['actionName'] === $actionName) {

                call_user_func_array($action['callback'], $arg);
            }

        }

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
     * 应用事件
     * @param string $actionName
     * @param $callback
     * @return void
     */
    public static function addAction(string $actionName, $callback)
    {
        self::$actions[] = ['actionName' => $actionName, 'callback' => $callback];
    }


    /**
     * 判断过滤器是否存在
     * Create by Peter Yang
     * 2023-11-26 11:38:23
     * @param string $filterName
     * @return bool
     */
    public static function filterExist(string $filterName): bool
    {
        foreach (self::$filters as $filter) {

            if ($filter['filterName'] === $filterName) {

                return true;
            }

        }

        return false;
    }


    /**
     * 判断事件是否存在
     * @param string $actionName
     * @return bool
     */
    public static function actionExist(string $actionName): bool
    {
        foreach (self::$actions as $action) {

            if ($action['actionName'] === $actionName) {

                return true;
            }

        }

        return false;
    }


}
