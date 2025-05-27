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
            ['name' => '分类拓展字段', 'path' => '/main/expand_list', 'title' => ['订单管理', '订单列表'], 'apiPath' => '/admin/article_expand/list'],
        ]
    ],

    [
        'name' => 'cms管理',
        'children' => [
            ['name' => '文章应用列表', 'path' => '/main/article_list', 'title' => ['cms管理', '文章列表'], 'apiPath' => '/admin/article/list'],
            ['name' => '草稿箱列表', 'path' => '/main/draft_article_list', 'title' => ['cms管理', '文章列表'], 'apiPath' => '/admin/article/list'],
            ['name' => '自定义模块', 'path' => '/main/mode_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/mode/list'],
            ['name' => '特殊属性', 'path' => '/main/special', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/special/list'],
            ['name' => '文章静态', 'path' => '/main/static', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/api/admin/mode/list'],
            ['name' => '新闻关联游戏', 'path' => '/main/find_game', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/api/admin/mode/list'],
            ['name' => '标签列表', 'path' => '/main/tag_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/tag/list'],
            ['name' => 'seo变动配置', 'path' => '/main/seo_title_change_edit', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/api/admin/mode/list'],
            ['name' => '推送记录', 'path' => '/main/website_push_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/website_push/list'],
            ['name' => '自动发布设置', 'path' => '/main/auto_push_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/auto_push/list'],
        ]
    ],
    [
        'name' => '采集管理',
        'children' => [
            ['name' => '采集列表', 'path' => '/main/store_article', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/store_article/list'],
            ['name' => '采集配置', 'path' => '/main/spider_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/spider/list'],
            ['name' => '分类映射', 'path' => '/main/category_map_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/category_map/list'],
            ['name' => '搜索引擎采集', 'path' => '/main/search_engine_list', 'title' => ['cms管理', '自定义模块列表'], 'apiPath' => '/admin/search_article/list'],

        ]

    ],

    [
        'name' => '设置',
        'children' => [
            ['name' => '系统设置', 'path' => '/main/setting', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/setting/getSetting'],
            ['name' => '网站设置', 'path' => '/main/site_setting', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/setting/getSetting'],
            ['name' => '单页面', 'path' => '/main/page_list', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/page/list'],
            ['name' => '拓展属性替换', 'path' => '/main/expand_change_list', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/expand_change/list'],
            ['name' => '主题设置', 'path' => '/main/theme', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/setting/themeList'],
            ['name' => '下载服务器设置', 'path' => '/main/download_site_list', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/download_site/list'],

        ]
    ],
    [
        'name' => 'seo优化',
        'children' => [
            ['name' => '404访问列表', 'path' => '/main/error_access', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/seo/errorAccessList'],
            ['name' => '网站地图', 'path' => '/main/sitemap', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/sitemap/list'],
            ['name' => '死链管理', 'path' => '/main/death', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/death/getLink'],

        ]
    ],
    [
        'name' => '第三方',
        'children' => [
            ['name' => 'AccessKey', 'path' => '/main/access_key', 'title' => ['消息队列', '正在运行的任务'], 'apiPath' => '/admin/access_key/list'],

        ]
    ],

];
