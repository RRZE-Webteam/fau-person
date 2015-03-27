<?php

// Custom-Field-Präfix
$prefix = 'cms_basis_';

// Meta-Box-Schema
$meta_boxes['cms_basis_metabox_1'] = array(
    'id' => 'sa_taxonomy', // ID der Meta-Box
    'title' => __('Titel der Meta-Box', self::textdomain), // Titel der Meta-Box
    'pages' => array('post', 'page'), // Post-Types, die die Meta-Box freigibt
    'context' => 'normal', // Der Teil der Bearbeitungseite, in der die Meta-Box angezeigt werden soll ('normal'=direkt unter post-Editor, 'advanced'=weiter unterhalb post-Editor, oder 'side'=rechte Sidebar).
    'priority' => 'low', // Die Priorität in Bezug auf den Teil der Bearbeitungsseite (context), wo die Meta-Box angezeigt werden soll ('high', 'core', 'default' oder 'low').
    'show_names' => true, // Feldnamen auf der linken Seite anzeigen (true).
    'fields' => array() // Siehe https://github.com/humanmade/Custom-Meta-Boxes/wiki
);

