<?php

namespace Ycore\Service\Upload;

use InvalidArgumentException;
use RuntimeException;

class RemoteImage
{
    public static function allowedExtensionFromUrl(string $url, array $allowList): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowList = array_map(
            fn (string $item): string => strtolower(trim($item)),
            $allowList
        );
        $allowList = array_values(array_filter($allowList));

        if (!in_array($extension, $allowList, true)) {
            throw new InvalidArgumentException('不允许上传该类型文件:' . $extension);
        }

        return $extension;
    }

    public static function assertImageResponse(int $status, string $contentType, string $url): void
    {
        $contentType = strtolower(trim(explode(';', $contentType)[0] ?? ''));

        if ($status < 200 || $status >= 300 || !str_starts_with($contentType, 'image/')) {
            throw new RuntimeException("远程图片下载失败:{$url}");
        }
    }
}
