<?php

class PeepSo3_Utilities_Array {

    // @TODO this is included in PHP 7.3 - no longer needed when PeepSo requires PHP 7.3+
    public static function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }

        return array_keys($array)[count($array)-1];
    }

}