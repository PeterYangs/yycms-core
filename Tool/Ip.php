<?php

namespace Ycore\Tool;

use Symfony\Component\HttpFoundation\IpUtils;

class Ip
{


    /**
     * 获取真实ip
     * @return string
     */
    static function getRealIp(): string
    {
        $candidates = [];

        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $candidates[] = $_SERVER["HTTP_CLIENT_IP"];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $candidates = array_merge($candidates, explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']));
        }

        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $candidates[] = $_SERVER['HTTP_X_REAL_IP'];
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $candidates[] = $_SERVER['REMOTE_ADDR'];
        }

        try {
            $clientIp = request()->getClientIp();
            if ($clientIp) {
                $candidates[] = $clientIp;
            }
        } catch (\Throwable $exception) {
        }

        $validIps = [];

        foreach ($candidates as $candidate) {
            $ip = trim((string)$candidate);
            if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
                continue;
            }

            $validIps[] = $ip;

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }

        return $validIps[0] ?? '';
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
