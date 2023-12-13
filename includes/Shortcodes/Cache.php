<?php

namespace FAU_Person\Shortcodes;

defined('ABSPATH') || exit;

class Cache
{
    private static string $prefix = 'fau_person_shortcode_';

    private static int $ttl = 24 * HOUR_IN_SECONDS;

    private static string $transientsOptionName = 'fau_person_shortcode_transients';

    private static function set(string $content, $value1, $value2, $value3 = ''): bool
    {
        $transient = self::getTransientName($value1, $value2, $value3);
        return $transient ? set_transient($transient, $content, self::$ttl) : false;
    }

    public static function get($value1, $value2, $value3 = ''): mixed
    {
        $transient = self::getTransientName($value1, $value2, $value3);
        return $transient ? get_transient($transient) : false;
    }

    public static function update(string $content, $value1, $value2, $value3 = '')
    {
        if (!self::set($content, $value1, $value2, $value3)) {
            return;
        }
        $transient = self::getTransientName($value1, $value2, $value3);
        if (!$transient) {
            return;
        }
        $transients = get_option(self::$transientsOptionName);
        if (!empty($transients)) {
            $transients[] = $transient;
        } else {
            $transients = [$transient];
        }
        update_option(self::$transientsOptionName, $transients);
    }

    public static function flush()
    {
        $transients = get_option(self::$transientsOptionName);
        if (!empty($transients) && is_array($transients)) {
            foreach ($transients as $transient) {
                delete_transient($transient);
            }
        }
        delete_option(self::$transientsOptionName);
    }


    private static function getTransientName($value1, $value2, $value3)
    {
        $value1 = json_encode($value1);
        $value2 = json_encode($value2);
        $value3 = json_encode($value3);
        if ($value1 && $value2 && $value3) {
            return self::$prefix . md5($value1 . $value2 . $value3);
        } else {
            return false;
        }
    }
}
