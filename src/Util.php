<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Illuminate\Support\Str;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Util
{
    public static function normalizeContent(string $content, ?string $contentType = null): string
    {
        $allowedContentTypes = [
            'application/json',
            'text/json',
            'text/html',
            'application/xml',
            'text/xml',
            'application/xhtml+xml',
        ];

        if ($contentType && in_array($contentType, $allowedContentTypes)) {
            return $content;
        }


        if (Str::isJson($content)) {
            return $content;
        }

        return '[FILTERED_CONTENT]';
    }

    public static function flattenHeaders(array $headers): array
    {
        return array_map(function ($headers) {
            if (is_array($headers)) {
                return implode(';', $headers);
            }

            return $headers;
        }, $headers);
    }

    public static function getHostname(): string
    {
        return gethostname();
    }

    public static function getServerIp(): string
    {
        return gethostbyname(self::getHostname());
    }
}
