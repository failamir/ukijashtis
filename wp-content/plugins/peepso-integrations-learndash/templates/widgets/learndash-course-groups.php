<?php
echo $args['before_widget'];
?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
    <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
        <?php
        if ( ! empty( $instance['title_cg'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title_cg'] ). $args['after_title'];
        }
        ?>
    </div>
<?php

foreach($instance['groups'] as $group) {
    $avatar = $group->get_avatar_url();
    $url = $group->get('url');
    $name = $group->get('name');
    ?>
    <div class="ps-ld__group">
        <a class="ps-ld__group-inner" href="<?php echo $url;?>" title="<?php echo $name;?>">
            <div class="ps-ld__group-avatar ps-avatar"><img src="<?php echo $avatar;?>" /></div>
            <div class="ps-ld__group-name">
                <?php echo $name; ?>
                <div class="ps-ld__group-privacy">
                    <?php  if(intval($group->is_open)) { echo '<i class="ps-icon-globe"></i>' . __('Open', 'peepsolearndash'); }  ?>
                    <?php  if(intval($group->is_closed)) { echo '<i class="ps-icon-lock"></i>'.__('Closed', 'peepsolearndash'); }  ?>
                    <?php  if(intval($group->is_secret)) { echo '<i class="ps-icon-shield"></i>'.__('Secret', 'peepsolearndash'); }  ?>
                </div>    
            </div>
        </a>
    </div>
    <?php
}
?>
</div>
<?php
echo $args['after_widget'];
// EOF