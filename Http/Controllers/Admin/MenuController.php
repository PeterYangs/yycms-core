<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Tool\Json;

class MenuController extends AuthCheckController
{

    /**
     * @Auth(type='no_check')
     * 菜单列表
     * Create by Peter Yang
     * 2022-06-20 14:25:10
     */
    function getMenu()
    {
        $menu = config('menu');
        $allRule = resolve("allRule");

//        dd($allRule);

        if ($allRule === true) {
            return Json::code(1, 'success', $menu);
        }

        foreach ($allRule as $k => $rule) {
            $allRule[$k] = preg_replace('#^/api#', '', $rule);
        }

        return Json::code(1, 'success', $this->filterMenuByPermission($menu, $allRule));
    }


    function filterMenuByPermission(array $menus, array $permissions): array
    {
        $filteredMenus = [];
        foreach ($menus as $menu) {
            if (!isset($menu['children']) || !is_array($menu['children'])) {
                continue;
            }
            // 过滤 children 中没有权限的
            $filteredChildren = array_filter($menu['children'], function ($child) use ($permissions) {
                return isset($child['apiPath']) && in_array($child['apiPath'], $permissions);
            });
            // 如果过滤后还有 children，就保留父级菜单
            if (!empty($filteredChildren)) {
                $menu['children'] = array_values($filteredChildren); // 重建索引
                $filteredMenus[] = $menu;
            }
        }
        return $filteredMenus;
    }

}
