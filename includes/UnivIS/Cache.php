<?php

namespace FAU_Person\UnivIS;

use function FAU_Person\Config\getConstants;

defined('ABSPATH') || exit;

class Cache {
    public static function set(string $content, string $url): bool {
        $transient = self::getTransientName($url);
        return $transient ? set_transient($transient, $content, self::get_ttl()) : false;
    }

    public static function get(string $url): mixed {
        $transient = self::getTransientName($url);
        return $transient ? get_transient($transient) : false;
    }

    public static function delete(string $url): bool {
        $transient = self::getTransientName($url);
        return $transient ? delete_transient($transient) : false;
    }

    private static function get_prefix(): string {
        $constants = getConstants();
        $prefix = $constants['UnivIS_Cache_Transient'] ?? 'fau_person_univis_cache';

        return is_string($prefix) && $prefix !== '' ? $prefix . '_' : 'fau_person_univis_cache_';
    }

    private static function get_ttl(): int {
        $constants = getConstants();
        $ttl = $constants['UnivIS_Cache_TTL'] ?? 24 * HOUR_IN_SECONDS;

        return is_int($ttl) && $ttl > 0 ? $ttl : 24 * HOUR_IN_SECONDS;
    }

    private static function getTransientName(string $url) {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            $prefix = parse_url($url, PHP_URL_SCHEME);
            $key = (strpos($url, $prefix) === 0) ? substr($url, strlen($prefix)) : $url;
            return self::get_prefix() . md5($key);
        } else {
            return false;
        }
    }
}
