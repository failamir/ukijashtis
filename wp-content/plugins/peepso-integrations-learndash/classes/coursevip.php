<?php

/**
 * @todo what's missing
 *
 */
class PeepSoCourseVIP
{	
	const TABLE = 'peepso_ld_course_vip';

	private $_table;

	public function __construct()
	{
		global $wpdb;

		$this->_table = $wpdb->prefix . PeepSoCourseVIP::TABLE;
	}

	public function toggle_course_vipicon($ld_course_id, $vipicon)
	{
		global $wpdb;
        $vipicon = intval($vipicon);
        $ld_course_id = intval($ld_course_id);

		
		$sql = "REPLACE INTO {$this->_table} (`ld_course_id`,`vip_id`) VALUES ('$ld_course_id','$vipicon')";
		$wpdb->query($sql);
		
		if($wpdb->last_error) { return $wpdb->last_error; }

		return true;
	}

	public function update_course_vipicons($ld_course_id, $vipicons)
	{
		global $wpdb;

		// remove all existing links...
		$sqlQuery = "DELETE FROM $this->_table WHERE `ld_course_id` = '" . esc_sql($ld_course_id) . "'";
		$wpdb->query($sqlQuery);
		
		if($wpdb->last_error) { return $wpdb->last_error; }

		// add the given links [back?] in...
		foreach($vipicons as $vipicon)
		{
			if(is_string($r = $this->toggle_course_vipicon( $ld_course_id, $vipicon)))
			{
				//uh oh, error
				return $r;
			}
		}

		//all good
		return true;
	}

	public function get_vipicons_by_course($ld_course_id)
	{
        $ld_course_id = intval($ld_course_id);

		global $wpdb;
		$vipicons = $wpdb->get_col("SELECT c.vip_id
											FROM {$this->_table} AS c
											WHERE c.ld_course_id = '" . $ld_course_id . "'");

		return $vipicons;
	}

}
