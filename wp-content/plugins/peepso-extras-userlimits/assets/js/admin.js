jQuery(function($) {
	// Blacklist and whitelist option toggle.
	var $enableBlacklist = $('input[name=limitusers_blacklist_domain_enable]');
	var $enableWhitelist = $('input[name=limitusers_whitelist_domain_enable]');
	if ($enableBlacklist.length && $enableWhitelist.length) {
		$enableBlacklist.on('click', function() {
			var $textarea = $('textarea[name=limitusers_blacklist_domain]');
			if (!this.checked) {
				$textarea.css({ opacity: 0.4 });
			} else {
				$textarea.css({ opacity: '' });
				$enableWhitelist[0].checked = false;
				$enableWhitelist.triggerHandler('click');
			}
		});
		$enableWhitelist.on('click', function() {
			var $textarea = $('textarea[name=limitusers_whitelist_domain]');
			if (!this.checked) {
				$textarea.css({ opacity: 0.4 });
			} else {
				$textarea.css({ opacity: '' });
				$enableBlacklist[0].checked = false;
				$enableBlacklist.triggerHandler('click');
			}
		});
		$enableBlacklist.triggerHandler('click');
		$enableWhitelist.triggerHandler('click');
	}
});
