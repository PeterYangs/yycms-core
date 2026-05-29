<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Tool\Json;
use Ycore\Tool\SwitchCore;

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

        if (SwitchCore::disabled(SwitchCore::ARTICLE_SPECIAL_ATTRIBUTE)) {
            $menu = $this->removeMenusByPath($menu, [
                '/main/special',
                '/main/expand_change_list',
            ]);
        }

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


    private function removeMenusByPath(array $menus, array $paths): array
    {
        $filteredMenus = [];

        foreach ($menus as $menu) {
            if (!isset($menu['children']) || !is_array($menu['children'])) {
                $filteredMenus[] = $menu;
                continue;
            }

            $menu['children'] = array_values(array_filter($menu['children'], function ($child) use ($paths) {
                return !in_array($child['path'] ?? '', $paths, true);
            }));

            if (!empty($menu['children'])) {
                $filteredMenus[] = $menu;
            }
        }

        return $filteredMenus;
    }

}
