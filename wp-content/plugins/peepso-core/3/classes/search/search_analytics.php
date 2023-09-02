<?php

class PeepSo3_Search_Analytics {
    const TABLE = 'peepso_search_ranking';
    private $db_version = 1;
    private $table;

    public function __construct() {

        @include_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        if(!function_exists('dbDelta')) {
            new PeepSoError("dbDelta() not found");
            return;
        }

        // Run dbDelta() once in a while no matter what
        $override = (rand(1,100) == 1) ? TRUE : FALSE;

        global $wpdb;
        $this->table = $wpdb->prefix . self::TABLE;

        $version = PeepSo::PLUGIN_VERSION.PeepSo::PLUGIN_RELEASE.'-'.$this->db_version;
        $charset_collate = $wpdb->get_charset_collate();

        // DB table: peepso_search_ranking
        if(get_option(self::TABLE) != $version || $override) {

            $sql = "CREATE TABLE {$this->table} (
					id BIGINT(20) NOT NULL AUTO_INCREMENT,
					user_id BIGINT(20) NOT NULL,
					search TEXT,	
					class VARCHAR(64),
					date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				    PRIMARY KEY (id)
				  ) ENGINE=InnoDB $charset_collate;";

            dbDelta($sql);
            update_option(self::TABLE, $version);
        }
    }

    public function store($search, $class) {
        $user_id = (int) get_current_user_id();
        global $wpdb;

        if(!empty($search)) {
            $wpdb->insert($this->table, ['user_id' => $user_id, 'search' => $search, 'class'=>$class]);
        }
    }

}