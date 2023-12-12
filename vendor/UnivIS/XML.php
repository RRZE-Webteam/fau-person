<?php

namespace RRZE\Lib\UnivIS;

defined('ABSPATH') || exit;

class XML
{
    public static function isXML(string $xml = '')
    {
        $xml = $xml ?: '<>';

        libxml_use_internal_errors(true);

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        // Check for errors while loading the XML
        $errors = libxml_get_errors();
        libxml_clear_errors();

        if (empty($errors)) {
            return true;
        }

        $error = $errors[0];
        if ($error->level < 3) {
            return true;
        }

        $explodedxml = explode('r', $xml);
        $badxml = $explodedxml[($error->line) - 1];
        $message = $error->message . ' at line ' . $error->line . '. Invalid XML: ' . htmlentities($badxml);

        return new \WP_Error('fau-person-xml-error', $message);
    }
}
