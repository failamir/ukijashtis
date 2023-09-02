<?php

abstract class PeepSo3_Search {

    public $query;
    public $section = NULL;
    public $title = NULL;
    public $url = NULL;
    public $order = 0;
    public $config;

    public $item = array(
        'id' => 0,
        'title' => '',
        'text' => '',
        'url' => '',
        'image' => '',
        'meta' => [],
        'extras' => [],
    );

    public function __construct() {

        // Default config can be overriden by passing a config array to meta
        $this->config = [
            'items_per_section' => 10,
            'max_length_title' => 75,
            'max_length_text' => 200,
        ];

        $this->order = apply_filters('peepso_search_order_section_'.$this->section, $this->order);
        add_filter('peepso_search_results', array(&$this, 'filter_peepso_search_results'), $this->order);
    }

    public function filter_peepso_search_results($results)
    {
        // skip if the filter is asking for a specific section
        if(!$this->filter_check_section($results)) {
            return $results;
        }

        $this->query = $results['meta']['query'];


        if(isset($results['meta']['config'])) {
           $this->config = array_merge($this->config,$results['meta']['config']);
        }

        $results['meta']['sections'][$this->section] = array(
            'title' => $this->title,
            'url' => $this->url . $this->query,
            'order' => $this->order,
        );

        $this_results = [];
        if(strlen($this->query)) {
            $this_results = $this->results();
        }

        $results['results'][$this->section] = $this_results;


        return $results;
    }

    public function filter_check_section($results) {
        return (!$results['meta']['section'] || $this->section == $results['meta']['section']);
    }

    public function map_item($item) {
        $item = array_merge($this->item, $item);
        foreach($item as $k=>$v) {
            if(!array_key_exists($k, $this->item)) {
                unset($item[$k]);
            }
        }

        if(isset($item['title']) && strlen($item['title']) > $this->config['max_length_title']) {
            $item['title'] = truncateHtml($item['title'], $this->config['max_length_title']);
        }

        if(isset($item['text']) && strlen($item['text']) > $this->config['max_length_text']) {
            $item['text'] = truncateHtml($item['text'], $this->config['max_length_text']);
        }

        if(!is_array($item['meta']) || !count($item['meta'])) {
            unset($item['meta']);
        }

        return $item;
    }

    abstract function results();
}