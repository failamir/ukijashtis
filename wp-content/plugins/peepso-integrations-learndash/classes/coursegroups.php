<?php

class PeepSoCourseGroups
{	
	const TABLE = 'peepso_ld_course_group';

	private $_table;

	public function __construct()
	{
		global $wpdb;

		$this->_table = $wpdb->prefix . PeepSoCourseGroups::TABLE;
	}

	public function toggle_course_group($ld_course_id, $group)
	{
		global $wpdb;
        $group = intval($group);
        $ld_course_id = intval($ld_course_id);

		
		$sql = "REPLACE INTO {$this->_table} (`ld_course_id`,`group_id`) VALUES ('$ld_course_id','$group')";
		$wpdb->query($sql);
		
		if($wpdb->last_error) { return $wpdb->last_error; }

		return true;
	}

	public function update_course_group($ld_course_id, $groups)
	{
		global $wpdb;

		$sqlQuery = "DELETE FROM $this->_table WHERE `ld_course_id` = '" . esc_sql($ld_course_id) . "'";
		$wpdb->query($sqlQuery);
		
		if($wpdb->last_error) { return $wpdb->last_error; }

		// add the given links [back?] in...
		foreach($groups as $group)
		{
			if(is_string($r = $this->toggle_course_group( $ld_course_id, $group)))
			{
				return $r;
			}
		}

		return true;
	}

    public function get_groups_by_course($ld_course_id)
    {
        $ld_course_id = intval($ld_course_id);

        global $wpdb;
        $groups = $wpdb->get_col("SELECT c.group_id
											FROM {$this->_table} AS c
											WHERE c.ld_course_id = '" . $ld_course_id . "'");

        return $groups;
    }



    public function get_courses_by_group($group_id )
    {
        $group_id  = intval($group_id );

        global $wpdb;
        $courses = $wpdb->get_col("SELECT c.ld_course_id
											FROM {$this->_table} AS c
											WHERE c.group_id = '" . $group_id . "'");

        return $courses;
    }



}
