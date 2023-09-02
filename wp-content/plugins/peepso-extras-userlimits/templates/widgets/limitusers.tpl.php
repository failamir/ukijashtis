<?php
echo $args['before_widget'];

$PeepSoLimitUsers = PeepSoLimitUsers::get_instance();

// widget title
if (!empty( $instance['title'])) {
	echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
}

// short message to the user
if( !empty($instance['message'])) {
	echo '<p>' , $instance['message'] , '</p>';
}

// profile completeness
if($instance['user_id'] > 0)
{	
	if(isset($instance['stats']['fields_all']) && $instance['stats']['fields_all'] > 0) {
		$style = '';
		if ($instance['stats']['completeness'] >= 100) {
			$style.='display:none;';
		}
	?>
	<div class="ps-progress-status ps-completeness-status" style="<?php echo $style;?>">
	<?php
		echo $instance['stats']['completeness_message'];
		do_action('peepso_action_render_profile_completeness_message_after', $instance['stats']);
	?>
	</div>
	<div class="ps-progress-bar ps-completeness-bar" style="<?php echo $style;?>">
		<span style="width:<?php echo $instance['stats']['completeness'];?>%"></span>
	</div>
	<?php
	}
}

PeepSoLimitUsers::get_instance()->debug_formatted();

echo $args['after_widget'];

// EOF
