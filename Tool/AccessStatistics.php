<?php

namespace Ycore\Tool;

/**
 * 内容库（soft-library）访问统计代码的写入规则
 *
 * 统计代码用起止注释包起来，追加在原有脚本后面；再次写入时只替换这一段，
 * 站点原有的其他统计（如百度统计）保持不动。
 */
class AccessStatistics
{
    public const START = '/* soft-library-access:start */';

    public const END = '/* soft-library-access:end */';

    /**
     * 早期在 soft-library 后台「复制统计代码」手动装的统计代码，逐条语句匹配：
     * (function() { var hm = document.createElement("script"); hm.src = ".../api/third/access/accessJs?key=..."; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hm, s); })();
     * 只允许空白和引号不同，多一条、少一条语句或被改动过都不匹配；前面的 var _hmt = _hmt || []; 不在匹配范围内，保留不动。
     */
    private const LEGACY_PATTERN = '~\(\s*function\s*\(\s*\)\s*\{\s*'
        . 'var\s+hm\s*=\s*document\.createElement\(\s*(["\'])script\1\s*\)\s*;\s*'
        . 'hm\.src\s*=\s*(["\'])[^"\'\s]*/api/third/access/accessJs\?key=[0-9a-zA-Z-]+\2\s*;\s*'
        . 'var\s+s\s*=\s*document\.getElementsByTagName\(\s*(["\'])script\3\s*\)\s*\[\s*0\s*\]\s*;\s*'
        . 's\.parentNode\.insertBefore\(\s*hm\s*,\s*s\s*\)\s*;?\s*'
        . '\}\s*\)\s*\(\s*\)\s*;?~';

    /**
     * 把统计代码合并进现有脚本
     * @param string $current
     * @param string $code
     * @return string
     */
    public static function merge(string $current, string $code): string
    {
        $block = self::START . "\n" . trim($code) . "\n" . self::END;

        $range = self::blockRange($current);

        if ($range !== null) {
            return substr($current, 0, $range[0]) . $block . substr($current, $range[1]);
        }

        if (trim($current) === '') {
            return $block;
        }

        return rtrim($current) . "\n" . $block;
    }

    /**
     * 删除统计代码段以外、与早期模板完全一致的旧统计代码，其他代码原样保留
     * @param string $current
     * @return array [处理后的脚本, 删除的段数]
     */
    public static function removeLegacy(string $current): array
    {
        $range = self::blockRange($current);

        if ($range === null) {
            $result = preg_replace(self::LEGACY_PATTERN, '', $current, -1, $removed);

            return [$result, $removed];
        }

        $before = preg_replace(self::LEGACY_PATTERN, '', substr($current, 0, $range[0]), -1, $countBefore);
        $after = preg_replace(self::LEGACY_PATTERN, '', substr($current, $range[1]), -1, $countAfter);

        return [$before . substr($current, $range[0], $range[1] - $range[0]) . $after, $countBefore + $countAfter];
    }

    /**
     * 描述现有脚本的状态，供内容库判断是否已经配置
     * @param string $current
     * @return array
     */
    public static function describe(string $current): array
    {
        $range = self::blockRange($current);

        $other = $current;

        if ($range !== null) {
            $other = substr($current, 0, $range[0]) . substr($current, $range[1]);
        }

        preg_match_all('/accessJs\?key=([0-9a-zA-Z-]+)/', $current, $matches);

        // 统计代码引用的统计地址（协议 + 域名），换统计域名时据此判断是否需要重写
        preg_match_all('#(https?://[^/"\'\s]+)/api/third/access/accessJs#i', $current, $origins);

        // 统计代码段以外的引用：能按早期模板识别的算旧代码，其余算无法识别
        $references = preg_match_all('/accessJs\?key=/', $other);
        $legacy = preg_match_all(self::LEGACY_PATTERN, $other);

        return [
            'empty' => trim($current) === '',
            'managed' => $range !== null,
            'keys' => array_values(array_unique($matches[1])),
            'origins' => array_values(array_unique(array_map('strtolower', $origins[1]))),
            'other' => trim($other) !== '',
            'legacy' => $legacy,
            'unrecognized' => $references - $legacy,
        ];
    }

    /**
     * 统计代码段在脚本中的起止位置
     * @param string $current
     * @return array|null
     */
    private static function blockRange(string $current): ?array
    {
        $start = strpos($current, self::START);

        if ($start === false) {
            return null;
        }

        $end = strpos($current, self::END, $start);

        if ($end === false) {
            return null;
        }

        return [$start, $end + strlen(self::END)];
    }
}
