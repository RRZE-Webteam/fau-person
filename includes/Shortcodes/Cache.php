<?php

namespace FAU_Person\Shortcodes;

use function FAU_Person\Config\getConstants;

defined('ABSPATH') || exit;

class Cache {
    private static function set(string $content, $value1, $value2, $value3 = ''): bool {
        $transient = self::getTransientName($value1, $value2, $value3);
        return $transient ? set_transient($transient, $content, self::get_ttl()) : false;
    }

    public static function get($value1, $value2, $value3 = ''): mixed {
        $transient = self::getTransientName($value1, $value2, $value3);
        return $transient ? get_transient($transient) : false;
    }

    public static function update(string $content, $value1, $value2, $value3 = '') {
        if (!self::set($content, $value1, $value2, $value3)) {
            return;
        }
        $transient = self::getTransientName($value1, $value2, $value3);
        if (!$transient) {
            return;
        }
        $transients = get_option(self::get_transients_option_name());
        if (!empty($transients)) {
            $transients[] = $transient;
        } else {
            $transients = [$transient];
        }
        update_option(self::get_transients_option_name(), $transients);
    }

    public static function flush() {
        $transients = get_option(self::get_transients_option_name());
        if (!empty($transients) && is_array($transients)) {
            foreach ($transients as $transient) {
                delete_transient($transient);
            }
        }
        delete_option(self::get_transients_option_name());
    }


    private static function get_prefix(): string {
        $constants = getConstants();
        $prefix = $constants['Shortcode_Transient'] ?? 'fau_person_shortcode';

        return is_string($prefix) && $prefix !== '' ? $prefix . '_' : 'fau_person_shortcode_';
    }

    private static function get_transients_option_name(): string {
        $constants = getConstants();
        $baseName = $constants['Shortcode_Transient'] ?? 'fau_person_shortcode';

        return is_string($baseName) && $baseName !== '' ? $baseName . '_transients' : 'fau_person_shortcode_transients';
    }

    private static function get_ttl(): int {
        $constants = getConstants();
        $ttl = $constants['Shortcode_Cache_TTL'] ?? 24 * HOUR_IN_SECONDS;

        return is_int($ttl) && $ttl > 0 ? $ttl : 24 * HOUR_IN_SECONDS;
    }

    private static function getTransientName($value1, $value2, $value3) {
        $value1 = json_encode($value1);
        $value2 = json_encode($value2);
        $value3 = json_encode($value3);
        if ($value1 && $value2 && $value3) {
            return self::get_prefix() . md5($value1 . $value2 . $value3);
        } else {
            return false;
        }
    }
}
