<?php
$PeepSoProfile = PeepSoProfile::get_instance();
$PeepSoUser = $PeepSoProfile->user;
?>
<div class="ps-userlimits__debug-wrapper">
    <a href="<?php echo $PeepSoUser->get_profileurl();?>about">
    <?php

    if (isset($data['avatar']) && count($data['avatar'])) {
        echo '<div class="ps-userlimits__debug">';
        echo '<strong>', __('Upload an avatar to:', 'peepsolimitusers'), '</strong>';
        echo '<div class="ps-userlimits__debug-list">';
        foreach ($data['avatar'] as $section) {
            echo '<span class="ps-userlimits__debug-item">', $sections_descriptions[$section], '</span>';
        }
        echo '</div>';
        echo '</div>';
    }

    if (isset($data['cover']) && count($data['cover'])) {
        echo '<div class="ps-userlimits__debug">';
        echo '<strong>', __('Upload a cover to:', 'peepsolimitusers'), '</strong>';
        echo '<div class="ps-userlimits__debug-list">';
        foreach ($data['cover'] as $section) {
            echo '<span class="ps-userlimits__debug-item">', $sections_descriptions[$section], '</span>';
        }
        echo '</div>';
        echo '</div>';
    }

    if (isset($data['profile']) && count($data['profile'])) {

        ksort($data['profile']);

        echo '<div class="ps-userlimits__debug">';
        echo '<strong>', __('Complete your profile to at least:', 'peepsolimitusers'), '</strong>';
        echo '<div class="ps-userlimits__debug-list">';
        foreach ($data['profile'] as $limit => $sections) {
            foreach ($sections as $section) {
                echo '<span class="ps-userlimits__debug-item">',$sections_icon[$section],$limit,'% ',__('to', 'peepsolimitusers'),' ', $sections_descriptions[$section],'</span>';
            }

        }

        echo '</div>';
        echo '</div>';
    }

    ?>
    </a>
</div>
