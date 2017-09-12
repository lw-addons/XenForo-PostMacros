<?php

class LiamW_Macros_DatabaseSchema_Macros extends LiamW_Shared_DatabaseSchema_Abstract2
{

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getInstallSql()
	 */
	protected function _getInstallSql()
	{
		return array(
			0 => "
			CREATE TABLE IF NOT EXISTS liam_macros (
				macro_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id INT(10) UNSIGNED NOT NULL,
				name VARCHAR(50) NOT NULL,
				content TEXT NOT NULL,
				thread_title VARCHAR(50) NOT NULL,
				staff_macro BOOLEAN NOT NULL,
				PRIMARY KEY (macro_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
			5 => "ALTER TABLE xf_liam_macros ADD staff_macro BOOLEAN NOT NULL DEFAULT FALSE",
			30201 => "ALTER TABLE xf_liam_macros CONVERT TO CHARACTER SET utf8",
			30401 => "ALTER TABLE xf_liam_macros ADD COLUMN thread_title VARCHAR(50) NOT NULL AFTER macro;",
			30501 => array(
				"RENAME TABLE xf_liam_macros TO liam_macros",
				"ALTER TABLE liam_macros CHANGE macroid macro_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT",
				"ALTER TABLE liam_macros CHANGE userid user_id INT(10) UNSIGNED NOT NULL"
			),
			30604 => array(
				"ALTER TABLE liam_macros CHANGE macro content TEXT NOT NULL"
			)
		);
	}

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getUninstallSql()
	 */
	protected function _getUninstallSql()
	{
		return array('DROP TABLE IF EXISTS liam_macros');
	}
}