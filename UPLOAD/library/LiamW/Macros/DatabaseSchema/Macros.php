<?php

class LiamW_Macros_DatabaseSchema_Macros extends LiamW_Shared_DatabaseSchema_Abstract2
{
	protected function _getInstallSql()
	{
		return array(
			0 => "
			CREATE TABLE IF NOT EXISTS xf_liam_macros (
				macroid INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				userid INT(10) UNSIGNED NOT NULL,
				name VARCHAR(50) NOT NULL,
				macro TEXT NOT NULL
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
			5 => "ALTER TABLE xf_liam_macros ADD staff_macro BOOLEAN NOT NULL DEFAULT FALSE",
			30201 => "ALTER TABLE xf_liam_macros CONVERT TO CHARACTER SET utf8",
			30401 => "ALTER TABLE xf_liam_macros ADD COLUMN thread_title VARCHAR(50) NOT NULL AFTER macro;",
			30501 => array(
				"RENAME TABLE xf_liam_macros TO liam_macros",
				"ALTER TABLE liam_macros CHANGE macroid macro_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT",
				"ALTER TABLE liam_macros CHANGE userid user_id INT(10) UNSIGNED NOT NULL"
			),
			30603 => array(
				"ALTER TABLE liam_macros CHANGE macro content TEXT NOT NULL"
			)
		);
	}

	protected function _getUninstallSql()
	{
		return array('DROP TABLE IF EXISTS liam_macros');
	}

	protected function _getClassName()
	{
		return get_class($this);
	}
}