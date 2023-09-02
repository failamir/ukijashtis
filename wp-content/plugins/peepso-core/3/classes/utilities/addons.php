<?php

class PeepSo3_Helper_Addons {

    public static function license_to_id($license, $cache = TRUE) {

        $mayfly = 'license_to_id';

        $id = 0;

        new PeepSoError("Before cache $id");
        if($cache) {
            $id = PeepSo3_Mayfly_Int::get($mayfly);

            if($id !== NULL) {
                new PeepSoError("Cache $id");
                return $id;
            }
        }

        new PeepSoError("After cache $id");
        $url = PeepSoLicense::PEEPSO_HOME;

        $api_params = [
            'license_to_id' => $license,
        ];

        $request = wp_remote_post($url, ['timeout' => 15, 'sslverify' => TRUE, 'body' => $api_params]);

        if (is_wp_error($request)) {
            $request = wp_remote_post($url, ['timeout' => 15, 'sslverify' => FALSE, 'body' => $api_params]);
        }

        if (!is_wp_error($request)) {

            $info = json_decode(wp_remote_retrieve_body($request),TRUE);
            if(!$info['error']) {
                $id = intval($info['bundle_id']);
            }
        }

        new PeepSoError("After API $id");

        if(is_int($id)) {
            PeepSo3_Mayfly_Int::set($mayfly, $id);
        }

        return $id;
    }

    public static function get_addons() {

        $has_new = FALSE;

        if(isset($_REQUEST['nocache'])) {
            PeepSo3_Mayfly::del('bundle_info');
        }

        $url = PeepSoLicense::PEEPSO_HOME;
        $bundle_info = PeepSo3_Mayfly::get('bundle_info');

        if (!$bundle_info) {
            global $wp_version;

            $url .= "/?product_bundles_list&wp_version=".$wp_version."&ver_php=".PHP_VERSION."&ver_locale=".get_locale()."&theme=".urlencode(wp_get_theme()->get('Name'));
            $request = wp_remote_get($url, ['timeout' => 15, 'sslverify' => TRUE]);

            if (is_wp_error($request)) {
                $request = wp_remote_post($url, ['timeout' => 15, 'sslverify' => FALSE]);
            }

            if (!is_wp_error($request)) {
                $bundle_info = json_decode(wp_remote_retrieve_body($request));
                PeepSo3_Mayfly::set('bundle_info', $bundle_info, 3600);

                foreach($bundle_info as $item) {
                    if(isset($item->new)) {
                        $has_new = $item->id;
                        break;
                    }
                }

                if($has_new) {
                    PeepSo3_Mayfly_Int::set('installer_has_new', $has_new);
                } else {
                    PeepSo3_Mayfly_Int::del('installer_has_new');
                }
            }
        }

        return $bundle_info;
    }
}