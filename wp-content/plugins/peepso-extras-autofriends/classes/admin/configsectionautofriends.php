<?php

class PeepSoConfigSectionAutoFriends extends PeepSoConfigSectionAbstract
{
// Builds the groups array
	public function register_config_groups()
	{
		$this->context='full';
		$this->_autofriends_listusers();
	}

	private function _autofriends_listusers()
	{
		$this->set_field(
			'autofriends_searchbox',
			'<input type="search" id="search_user" name="search_user" value="" placeholder="'.__('search', 'autofriends-peepso').'" style="width:100%"/><p id="empty-message" style="color:gray;padding-left:15px;padding-right:15px;"></p>',
			'message'
		);

		$this->set_field(
			'autofriends_description_detail1',
			__('Adding a user to the list below will automatically create friendship between that user and any newly registered user. It will not create friendships between this user and already existing ones. <br> To create missing friendship connections, please use the button on the right. Once clicked it\'ll add that user as friends to everyone else in your PeepSo community. <br> Removing user from the list below will <strong>NOT</strong> remove existing friendship connections. ','autofriends-peepso'),
			'message'
		);

		wp_enqueue_style('autocompleteautofriends-css');
		wp_enqueue_script('peepso-window');
		wp_enqueue_script('jquery-ui-autocomplete', array( 'jquery' ));
		wp_enqueue_script('adminuserautofriends-js');

		$peepso_list_table = new PeepSoUserAutoFriendsListTable();
		$peepso_list_table->prepare_items();

		ob_start();
		echo "<div style='margin:0 15px;'>";
		wp_nonce_field('bulk-action', 'autofriends-nonce');
		$peepso_list_table->display();
		echo "</div>";
		$table = ob_get_clean();

		$this->set_field(
			'autofriends_table',
			$table,
			'message'
		);

		// Build Group
		$this->set_group(
			'listuser',
			__('List Users', 'autofriends-peepso')
		);
	}
}