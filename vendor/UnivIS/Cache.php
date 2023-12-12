<?php

namespace RRZE\Lib\UnivIS;

defined('ABSPATH') || exit;

class Cache
{
    private static string $prefix = 'fau_person_';

    private static int $ttl = 24 * HOUR_IN_SECONDS;

    public static function set(string $content, string $url): bool
    {
        $transient = self::getTransientName($url);
        return $transient ? set_transient($transient, $content, self::$ttl) : false;
    }

    public static function get(string $url): mixed
    {
        $transient = self::getTransientName($url);
        return $transient ? get_transient($transient) : false;
    }

    public static function delete(string $url): bool
    {
        $transient = self::getTransientName($url);
        return $transient ? delete_transient($transient) : false;
    }

    private static function getTransientName(string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            $prefix = parse_url($url, PHP_URL_SCHEME);
            $key = (strpos($url, $prefix) === 0) ? substr($url, strlen($prefix)) : $url;
            return self::$prefix . md5($key);
        } else {
            return false;
        }
    }
}
