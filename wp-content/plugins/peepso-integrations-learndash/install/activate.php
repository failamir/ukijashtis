<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class PeepSoLearnDashInstall extends PeepSoInstall
{
	public function plugin_activation( $is_core = FALSE )
	{
		parent::plugin_activation($is_core);

		return (TRUE);
	}

    public static function get_table_data()
    {
        $aRet = array(
            'ld_course_group' => "
				CREATE TABLE ld_course_group (
					ld_course_id int(11) unsigned NOT NULL,
					group_id int(11) unsigned NOT NULL,
					modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					UNIQUE KEY course_group (ld_course_id,group_id),
					UNIQUE KEY group_course (group_id,ld_course_id)
				) ENGINE=InnoDB",
            'ld_course_group_auto' => "
				CREATE TABLE ld_course_group_auto (
					ld_course_id int(11) unsigned NOT NULL,
					group_id int(11) unsigned NOT NULL,
					modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					UNIQUE KEY course_group (ld_course_id,group_id),
					UNIQUE KEY group_course (group_id,ld_course_id)
				) ENGINE=InnoDB",
            'ld_course_vip' => "
				CREATE TABLE ld_course_vip (
					ld_course_id int(11) unsigned NOT NULL,
					vip_id int(11) unsigned NOT NULL,
					modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					UNIQUE KEY course_vip (ld_course_id,vip_id),
					UNIQUE KEY vip_course (vip_id,ld_course_id)
				) ENGINE=InnoDB",
        );

        return $aRet;
    }
}