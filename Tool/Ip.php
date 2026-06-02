<?php

namespace Ycore\Tool;

use Symfony\Component\HttpFoundation\IpUtils;

class Ip
{


    /**
     * Get real client IP.
     * @return string
     */
    static function getRealIp(): string
    {
        $candidates = [];
        $remoteIp = self::normalizeIp($_SERVER['REMOTE_ADDR'] ?? '');

        foreach ([
                     'HTTP_CF_CONNECTING_IP',
                     'HTTP_X_REAL_IP',
                     'HTTP_CLIENT_IP',
                     'HTTP_X_FORWARDED_FOR',
                 ] as $key) {
            if (empty($_SERVER[$key])) {
                continue;
            }

            foreach (explode(',', $_SERVER[$key]) as $value) {
                $ip = self::normalizeIp($value);

                if ($ip) {
                    $candidates[] = $ip;
                }
            }
        }

        foreach ($candidates as $ip) {
            if (self::isPublicIp($ip)) {
                return $ip;
            }
        }

        $requestIp = '';
        try {
            $requestIp = self::normalizeIp(request()->getClientIp());
        } catch (\Throwable $exception) {
        }

        foreach (array_merge($candidates, [$remoteIp, $requestIp]) as $value) {
            $ip = self::normalizeIp($value);

            if ($ip) {
                return $ip;
            }
        }

        return '';
    }

    /**
     * Keep only valid IPs and support IPv4:port / [IPv6]:port values.
     */
    private static function normalizeIp($value): string
    {
        $value = trim((string)$value);
        $value = trim($value, '"\'');

        if ($value === '' || strtolower($value) === 'unknown') {
            return '';
        }

        if (preg_match('/^\[([0-9a-fA-F:.]+)\](?::\d+)?$/', $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match('/^(\d{1,3}(?:\.\d{1,3}){3}):\d+$/', $value, $matches)) {
            $value = $matches[1];
        }

        return filter_var($value, FILTER_VALIDATE_IP) ? $value : '';
    }

    /**
     * Prefer public IPs over private/reserved proxy-chain values.
     */
    private static function isPublicIp($ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * 判断 IP 是否在 white_ips.txt 白名单内，支持单 IP 和 CIDR。
     */
    static function isInWhiteList(string $ip, ?string $path = null): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        $path = $path ?: base_path('white_ips.txt');
        if (!is_file($path) || !is_readable($path)) {
            return false;
        }

        $rules = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$rules) {
            return false;
        }

        foreach ($rules as $rule) {
            $rule = trim(preg_replace('/\s*#.*/', '', $rule));
            if ($rule === '') {
                continue;
            }

            if (IpUtils::checkIp($ip, $rule)) {
                return true;
            }
        }

        return false;
    }

}
