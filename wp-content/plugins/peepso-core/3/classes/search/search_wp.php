<?php

if(!class_exists('PeepSo3_Search')) {
    require_once(dirname(__FILE__) . '/search.php');
    new PeepSoError('Autoload issue: PeepSo3_Search not found ' . __FILE__);
}

class PeepSo3_Search_WP extends PeepSo3_Search {

    public $post_type = '';
    public $results = [];

    public function __construct($post_type, $section_title, $section_order, $section_url = NULL)
    {
        $this->post_type = $post_type;
        $this->order = $section_order;
        $this->title = $section_title;
        $this->url = $section_url;

        if(NULL == $this->url) {
            $this->url = '/?s=';
        }

        $this->section ='wp_'.$this->post_type;

        parent::__construct();
    }

    public function results() {

        $args=[
            's' => $this->query,
            'post_type' => $this->post_type,
        ];

        $the_query = new WP_Query($args);

        if ($the_query->have_posts()) {

            while ($the_query->have_posts()) {
                $the_query->the_post();

                $this->results[] = $this->map_item([
                    'title' => get_the_title(),
                    'text' => get_the_excerpt(),
                    'image' => get_the_post_thumbnail_url(),
                ]);
            }
        }

        wp_reset_postdata();

        return $this->results;
    }

}