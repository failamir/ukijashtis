<div class="ps-learndash__course">
	<div class="ps-learndash__course-inside">
		<div class="ps-learndash__course-body">

			<!-- Featured image -->
			<?php
			$image_position = "";

			if(PeepSo::get_option('ld_profile_featured_image_position') == "left") {
				$image_position = "ps-learndash__course-image--left";
			}

			if(PeepSo::get_option('ld_profile_featured_image_position') == "right") {
				$image_position = "ps-learndash__course-image--right";
			}

			if(PeepSo::get_option('ld_profile_featured_image_enable') && (has_post_thumbnail($course) || PeepSo::get_option('ld_profile_featured_image_enable_if_empty'))) : ?>
				<div style="background-image: url('<?php echo get_the_post_thumbnail_url($course);?>');" class="ps-learndash__course-image <?php echo $image_position; ?>">
					<a href="<?php echo get_permalink($course);?>"></a>
				</div>
			<?php endif; ?>

			<!-- Post title -->
            <?php if(1 == PeepSo::get_option('ld_profile_titles', 1)) { ?>
                <h2 class="ps-learndash__course-title">
                    <a title="<?php echo get_the_title($course);?>" href="<?php echo get_permalink($course);?>">
                        <?php echo get_the_title($course);?>
                    </a>
                </h2>
            <?php } ?>
			<!-- Post meta -->
<!--			<div class="ps-learndash__course-meta">-->
<!--				--><?php //echo get_the_date('',$course);?>
<!--			</div>-->

			<!-- Post content -->
			<?php if(FALSE !== $course_content):?>
				<div class="ps-learndash__course-content">
					<?php echo $course_content; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
