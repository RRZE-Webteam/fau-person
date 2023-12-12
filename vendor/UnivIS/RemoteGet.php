<?php

namespace RRZE\Lib\UnivIS;

defined('ABSPATH') || exit;

class RemoteGet
{
    private static $defaultArgs = [
        'timeout' => 5,
        'sslverify' => true,
        'method' => 'GET',
        'validate' => 'xml'
    ];

    public static function retrieveContent(string $url, array $args = [], int $code = 200, bool $safe = true)
    {
        $args = wp_parse_args($args, self::$defaultArgs);
        $args = array_intersect_key($args, self::$defaultArgs);

        $content = Cache::get($url);
        if ($content === false) {
            $response = self::remoteGet($url, $args, $safe);
            if (is_wp_error($response)) {
                $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                do_action(
                    'rrze.log.error',
                    'Plugin: {plugin} WP-Error: {wp-error}',
                    [
                        'plugin' => 'fau-person',
                        'url' => $url,
                        'wp-error' => $response->get_error_message(),
                        'debug-backtrace' => $debugBacktrace[1]['function'] ?? ''
                    ]
                );
                $response = [];
            } elseif (wp_remote_retrieve_response_code($response) != $code) {
                $response = [];
            }
            $content = $response['body'] ?? '';
            switch ($args['validate']) {
                case 'xml':
                    if ($content && is_wp_error(XML::isXML($content))) {
                        $content = '';
                    }
                    break;
                default:
                    break;
            }
            if ($content) {
                Cache::set($content, $url);
            }
        }

        return $content;
    }

    private static function remoteGet(string $url, array $args, bool $safe)
    {
        if ($safe) {
            return wp_safe_remote_get($url, $args);
        } else {
            return wp_remote_get($url, $args);
        }
    }
}
