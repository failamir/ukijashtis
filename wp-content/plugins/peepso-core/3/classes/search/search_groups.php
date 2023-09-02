<?php

if(!class_exists('PeepSo3_Search')) {
    require_once(dirname(__FILE__) . '/search.php');
    new PeepSoError('Autoload issue: PeepSo3_Search not found ' . __FILE__);
}


class PeepSo3_Search_Groups  extends PeepSo3_Search {

    public function __construct()
    {
        $this->section = 'groups';
        $this->title = __('Groups', 'peepso-core');
        $this->url = 'groups/?category=0&filter=';

        $this->order = 300;

        parent::__construct();
    }

    public function results() {

        // look for $this->>query

        $PeepSoGroups = new PeepSoGroups();
        $results = [];

        $items = $PeepSoGroups->get_groups(0, $this->config['items_per_section'], '','', $this->query);


        if(count($items)) {
            foreach($items as $PeepSoGroup) {

                $meta = [
                    [
                        'context' => 'privacy',
                        'icon' => $PeepSoGroup->privacy['icon'],
                        'title' => $PeepSoGroup->privacy['name'],
                    ],
                ];

                $membership = NULL;

                $member = FALSE;
                if(get_current_user_id()) {
                    $PeepSoGroupUser = new PeepSoGroupUser($PeepSoGroup->get('id'));
                    $member = $PeepSoGroupUser->is_member;
                    $role = $PeepSoGroupUser->role_l8n;
                    if($member) {
                        $membership = [
                            'context' => 'membership',
                            'icon' => 'gcis gci-user-check',
                            'title' => ucfirst($role),
                            ];
                    }
                }

                if($membership) {
                    $meta[]=$membership;
                }

                $item = [
                    'id' => $PeepSoGroup->id,
                    'title' => $PeepSoGroup->get('name'),
                    'text' => $PeepSoGroup->get('description'),
                    'meta' => $meta,
                    'url' => $PeepSoGroup->get('url'),
                    'image' => $PeepSoGroup->get('avatar_url'),
                ];

                $results[] = $this->map_item(
                 $item
                );
            }
        }

        return $results;
    }

}

if(class_exists('PeepSoGroupsPlugin')){
    new PeepSo3_Search_Groups();
}