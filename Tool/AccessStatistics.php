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

        preg_match_all('/accessJs\?key=([0-9a-zA-Z]+)/', $current, $matches);

        return [
            'empty' => trim($current) === '',
            'managed' => $range !== null,
            'keys' => array_values(array_unique($matches[1])),
            'other' => trim($other) !== '',
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
