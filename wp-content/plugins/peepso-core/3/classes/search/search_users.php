<?php

if(!class_exists('PeepSo3_Search')) {
    require_once(dirname(__FILE__) . '/search.php');
    new PeepSoError('Autoload issue: PeepSo3_Search not found ' . __FILE__);
}

class PeepSo3_Search_Users extends PeepSo3_Search {

    public function __construct()
    {
        $this->section = 'users';
        $this->title = __('Members', 'peepso-core');
        $this->url = PeepSo::get_page('members').'?filter=';

        $this->order = 200;

        parent::__construct();
    }

    public function results() {

        $results = [];

        // We trick the old AJAX based member search into giving us some raw data
        $_GET['no_html'] = 1;
        $_GET['limit'] = $this->config['items_per_section'];

        $PeepSoMemberSearch = PeepSoMemberSearch::get_instance();

        $resp = new PeepSoAjaxResponse();
        $PeepSoMemberSearch->search($resp);

        if(isset($resp->data['no_html_data']) && count($resp->data['no_html_data'])) {

            foreach($resp->data['no_html_data'] as $PeepSoUser) {

                $meta = [];

                $user_id = $PeepSoUser->get_id();
                if($user_id == get_current_user_id()) {
                    $meta['mine'] = 1;
                } elseif(class_exists('PeepSoFriends')) {
                    $meta = [];

                    // Friends
                    $PeepSoFriendsModel = new PeepSoFriendsModel();
                    if($PeepSoFriendsModel->are_friends(get_current_user_id(), $user_id))
                    {
                        $meta[] = [
                            'context' => 'friendship',
                            'icon' => 'gcis gci-check',
                            'title' => 'Friends',
                        ];
                    }

                    // Mutual friends
                    $mutual_friends = $PeepSoFriendsModel->get_mutual_friends(get_current_user_id(), $user_id);
                    $mutual_friends = count($mutual_friends);

                    $title = $mutual_friends . _n(' mutual friend', ' mutual friends', $mutual_friends, 'friendso');
                    if(!$mutual_friends) {
                        $title = __('No mutual friends', 'friendso');
                    }

                    $meta[] = [
                        'context' => 'mutual_friends',
                        'icon' => 'gcis gci-user-friends',
                        'title' => $title,
                    ];


                }

                $results[]= $this->map_item([
                    'id' => $user_id,
                    'title' => $PeepSoUser->get_fullname(),
                    'text' => '',
                    'url' => $PeepSoUser->get_profileurl(),
                    'image' => $PeepSoUser->get_avatar(),
                    'meta' => $meta,
                ]);
            }
        }

        return $results;
    }

}

new PeepSo3_Search_Users();