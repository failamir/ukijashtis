<?php

class PeepSo3_Compatibility_AIO_SEO {
    private static $instance;

    public static function get_instance() {
        return isset(self::$instance) ? self::$instance : self::$instance = new self;
    }

    private function __construct()
    {
        add_filter('aioseo_disable', function($ret){
            if (!$ret) {
                $url = PeepSoUrlSegments::get_instance();
                if ($url->_shortcode == 'peepso_recover' && 'POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['email'])) {
                    $ret = true;
                } elseif ($url->_shortcode == 'peepso_reset' && 'POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['pass1'])) {
                    $ret = true;
                }
            }

            return $ret;
        });

        add_filter('aioseo_meta_views', function($ret) {
            $url = PeepSoUrlSegments::get_instance();

            if ($url->_shortcode == 'peepso_profile' && $url->get(3) == 'create') {
                return '';
            } else {
                return $ret;
            }
        }, 999);
    }
}

if(!defined('PEEPSO_DISABLE_COMPATIBILITY_AIO_SEO')) {
    PeepSo3_Compatibility_AIO_SEO::get_instance();
 }