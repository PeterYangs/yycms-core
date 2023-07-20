<?php

return [
    [
        'name' => '管理员管理',
        'children' => [
            ['name' => '管理员列表', 'path' => '/main/admin_list', 'apiPath' => '/admin/admin/admin_list', 'title' => ['用户管理', '管理员列表']],
            ['name' => '路由列表', 'path' => '/main/rule_list', 'apiPath' => '/admin/rule/rule_list', 'title' => ['用户管理', '路由列表']],
            ['name' => '角色列表', 'path' => '/main/group_list', 'apiPath' => '/admin/rule_group/group_list', 'title' => ['用户管理', '角色列表']],
        ]
    ],

    [
        'name' => '分类管理',
        'children' => [
            ['name' => '分类列表', 'path' => '/main/category_list', 'title' => ['订单管理', '订单列表'], 'apiPath' => '/admin/category/categoryList'],
            ['name' => '分类拓展字段', 'path' => '/main/expand_list', 'title' => ['订单管理', '订单列表'], 'apiPath' => '/admin/category/categoryList'],
        ]
    ],

    [
        'name' => 'cms管理',
        'children' => [
            ['name' => '文章应用列表', 'path' => '/main/article_list', 'title' => ['cms管理', '文章列表'], 'apiPath' => '/admin/article/list'],
            ['name' => '自定义模块', 'path' => '/main/mode_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '特殊属性', 'path' => '/main/special', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '文章静态', 'path' => '/main/static', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '新闻关联游戏', 'path' => '/main/find_game', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '标签列表', 'path' => '/main/tag_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => 'seo变动配置', 'path' => '/main/seo_title_change_edit', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '推送记录', 'path' => '/main/website_push_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
        ]
    ],
    [
        'name' => '采集管理',
        'children' => [
            ['name' => '采集列表', 'path' => '/main/store_article', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '采集配置', 'path' => '/main/spider_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '分类映射', 'path' => '/main/category_map_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '搜索引擎采集', 'path' => '/main/search_engine_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '采集自动发布设置', 'path' => '/main/auto_push_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],


        ]

    ],


    [
        'name' => '设置',
        'children' => [
            ['name' => '系统设置', 'path' => '/main/setting', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/queue/getTask'],
            ['name' => '网站设置', 'path' => '/main/site_setting', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/queue/getTask'],
            ['name' => '单页面', 'path' => '/main/page_list', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/queue/getTask'],
            ['name' => '拓展属性替换', 'path' => '/main/expand_change_list', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/queue/getTask'],
            ['name' => '主题设置', 'path' => '/main/theme', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/queue/getTask'],


        ]
    ],

];
