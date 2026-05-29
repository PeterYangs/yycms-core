<?php

namespace Ycore\Tool;

use InvalidArgumentException;

class SwitchCore
{
    public const ARTICLE_SPECIAL_ATTRIBUTE = 'article_special_attribute';

    private const DEFAULTS = [
        self::ARTICLE_SPECIAL_ATTRIBUTE => false,
    ];

    public static function enabled(string $key): bool
    {
        self::ensureKnown($key);

        return (int)getOption($key, self::DEFAULTS[$key] ? 1 : 0) === 1;
    }

    public static function disabled(string $key): bool
    {
        return !self::enabled($key);
    }

    public static function set(string $key, bool $enabled): void
    {
        self::ensureKnown($key);

        $value = $enabled ? 1 : 0;

        setOption($key, $value, true);
        app()->instance('option_' . $key, $value);
    }

    public static function allKeys(): array
    {
        return array_keys(self::DEFAULTS);
    }

    private static function ensureKnown(string $key): void
    {
        if (!array_key_exists($key, self::DEFAULTS)) {
            throw new InvalidArgumentException('未知开关：' . $key);
        }
    }
}
