<?php


if(!class_exists('PeepSo3_Search')) {
    require_once(dirname(__FILE__) . '/search.php');
    new PeepSoError('Autoload issue: PeepSo3_Search not found ' . __FILE__);
}

if(!class_exists('PeepSo3_Search_WP')) {
    require_once(dirname(__FILE__) . '/search_wp.php');
    new PeepSoError('Autoload issue: PeepSo3_Search_WP not found ' . __FILE__);
}

class PeepSo3_Search_WP_Posts extends PeepSo3_Search_WP {}

new PeepSo3_Search_WP_Pages(
    'post',
    __('Posts', 'peepso-core'),
    400
);