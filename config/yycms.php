<?php

return [
    // Opt in only after Nginx supplies YYCMS_VERIFIED_SPIDER on every PHP entry.
    'spider_identity' => env('YYCMS_SPIDER_IDENTITY', 'ua'),


    'upload_prefix' => env('UPLOAD_PREFIX', 'uploads'),
    //123网盘域名
    'wp_host' => env('WP_HOST', 'apk.down8818.com'),
    //123网盘鉴权秘钥
    'wp_secret' => env('WP_SECRET', 'iamyourfather6'),
    //123网盘uid
    'wp_uid' => env('WP_UID', '1818836746'),
    //禁用日志记录
    'disable_access_log' => env('DISABLE_ACCESS_LOG', false),

];
