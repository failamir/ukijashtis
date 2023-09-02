<div class="peepso">
	<?php 
	PeepSoTemplate::exec_template('general','navbar'); 
	if (PeepSo::get_option('wpadverts_allow_guest_access_to_classifieds', 0) === 0) {
		PeepSoTemplate::exec_template('general', 'register-panel');
	}
	if (get_current_user_id() || PeepSo::get_option('wpadverts_allow_guest_access_to_classifieds', 0) === 1) { ?>
		<?php echo $ads_form;?>
	<?php } ?>
</div><!--end row-->

<?php

if(get_current_user_id()) {
	PeepSoTemplate::exec_template('activity', 'dialogs');
}
