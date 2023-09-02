<div class="cstream-attachment giphy-attachment">
	<div class="ps-media--giphy ps-clearfix ps-js-giphy">
        <?php
        $alt= '';
        $preview = '';
        global $post;
        if($post instanceof WP_Post) {
            $PeepSoUser = PeepSoUser::get_instance($post->post_author);
            $alt = sprintf(__('%s shared a GIF','peepso-giphy'), $PeepSoUser->get_fullname());
            $preview = __('Shared a GIF','picso');
        }
        ?>
		<img src="<?php echo $giphy;?>" alt="<?php echo $alt;?>" data-preview="<?php echo $preview;?>">
		<!-- <div class="ps-media-loading ps-js-loading">
			<div class="ps-spinner">
				<div class="ps-spinner-bounce1"></div>
				<div class="ps-spinner-bounce2"></div>
				<div class="ps-spinner-bounce3"></div>
			</div>
		</div> -->
	</div>
</div>
