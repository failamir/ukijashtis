<div class="peepso ps-page-profile">
<?php PeepSoTemplate::exec_template('general', 'navbar'); ?>

    <?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'about')); ?>

<section id="mainbody" class="ps-page-unstyled">
	<section id="component" role="article" class="ps-clearfix">
		<h4 class="ps-page-title">
			<?php echo __('Points History', 'peepsocreds'); ?>
		</h4>

		<?php echo do_shortcode('[mycred_history user_id='.$view_user_id.']');?>

		</section><!--end component-->
	</section><!--end mainbody-->
</div><!--end row-->

<div id="ps-dialogs" style="display:none">
	<?php 
	$PeepSoActivity = PeepSoActivity::get_instance();
	$PeepSoActivity->dialogs(); // give add-ons a chance to output some HTML 
	?>
	<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
</div>
