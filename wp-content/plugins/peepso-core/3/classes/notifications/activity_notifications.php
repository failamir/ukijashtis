<?php

class PeepSo3_Activity_Notifications {

    private static $instance;
    private $table;
    private $post_id;
    private $user_id;
    private $follow;


    public function __construct($post_id, $user_id, $follow=1)
    {
        global $wpdb;
        $this->table = $wpdb->prefix.'peepso_activity_followers';

        $this->user_id = (int) $user_id;
        $this->post_id = (int) $post_id;
        $this->follow  = intval((bool) $follow);

        // Create a record if it does not exist
        ob_start();
        $wpdb->suppress_errors = TRUE;
        $sql = "INSERT IGNORE INTO {$this->table} (`post_id`,`user_id`, `follow`) VALUES ({$this->post_id}, {$this->user_id}, {$this->follow})";
        $wpdb->query($sql);
        $wpdb->suppress_errors = FALSE;
        ob_clean();
    }

    public function set($follow) {
        global $wpdb;
        $follow = intval((bool) $follow);
        $sql = "UPDATE {$this->table} SET `follow`=$follow WHERE `user_id`={$this->user_id} AND `post_id`={$this->post_id}";
        $wpdb->query($sql);
        echo $wpdb->last_error;
    }

    public function is_following() {
        return $this->follow;
    }
}