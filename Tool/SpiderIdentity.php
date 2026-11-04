<?php

namespace Ycore\Tool;

use Illuminate\Http\Request;

/** Uses a server-owned FastCGI parameter; HTTP headers are never trusted. */
class SpiderIdentity
{
    public static function usesVerifiedIp(): bool
    {
        // Only the explicit legacy value permits UA detection. Typos fail closed.
        return config('yycms.spider_identity', 'ua') !== 'ua';
    }

    public static function isSpider(Request $request): bool
    {
        if (!self::usesVerifiedIp()) {
            return \isSearchEngine($request->userAgent() ?? '');
        }

        return config('yycms.spider_identity') === 'verified_ip'
            && $request->server('YYCMS_VERIFIED_SPIDER') === '1';
    }
}
