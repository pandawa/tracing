<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Util
{
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
