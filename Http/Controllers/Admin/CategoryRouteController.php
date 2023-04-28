<?php

namespace Ycore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Ycore\Models\CategoryRoute;
use Ycore\Tool\Json;

class CategoryRouteController extends Controller
{


    function getControllerList()
    {

        $type = request()->input('type');


        if ($type === 1) {

            $list = \Storage::disk('http')->files('Controllers/Pc');

        } else {

            $list = \Storage::disk('http')->files('Controllers/Mobile');
        }


        return Json::code(1, 'success', $list);

    }


    /**
     * 获取方法列表
     * Create by Peter Yang
     * 2022-08-26 19:46:50
     * @return string
     * @throws \ReflectionException
     */
    function getActionList()
    {

//        $type = request()->input('type');

        $controller = request()->input('controller');


        try {

            return Json::code(1, 'success',
                $this->getClassMethods("\App\Http\\" . str_replace('.php', '', str_replace("/", "\\", $controller))));


        } catch (\Exception $exception) {


            return Json::code(1, "\App\Http\\" . str_replace("/", "\\", $controller));
        }


    }


    /**
     * 获取类的所有方法
     * Create by Peter Yang
     * 2022-08-26 19:45:07
     * @param string $className
     * @return array
     * @throws \ReflectionException
     */
    function getClassMethods(string $className): array
    {

        $rt = new \ReflectionClass($className);


//        $rt = new \ReflectionClass("\App\Http\Controllers\Pc\Game");


        $pClass = $rt->getParentClass();

        $pArr = [];

        foreach ($pClass->getMethods() as $v) {


            $pArr[] = $v->getName();

        }


        $arr = [];

        foreach ($rt->getMethods() as $v) {


            $arr[] = $v->getName();
        }

        return array_diff($arr, $pArr);

    }


    function CreateChannelRoute()
    {


        \Artisan::call('CreateChannelRoute');

        return Json::code(1, 'success');
    }


    function destroy()
    {

        $id = request()->input('id');

        $id = (int)$id;

        CategoryRoute::destroy($id);

        return Json::code(1, 'success');

    }


}
