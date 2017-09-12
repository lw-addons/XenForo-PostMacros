<?php

class LiamW_Macros_DatabaseSchema_AdminMacros extends LiamW_Shared_DatabaseSchema_Abstract
{
	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getInstallSql()
	 */
	protected function _getInstallSql()
	{
		return array(
			0 => "
			CREATE TABLE IF NOT EXISTS liam_macros_admin (
				macro_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(50) NOT NULL,
				content BLOB NOT NULL,
				thread_title VARCHAR(50) NOT NULL,
				usergroups TEXT NOT NULL,
				PRIMARY KEY (macro_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			30201 => "ALTER TABLE xf_liam_macros_admin CONVERT TO CHARACTER SET utf8",
			30401 => "ALTER TABLE xf_liam_macros_admin ADD COLUMN thread_title VARCHAR(50) NOT NULL AFTER content;",
			30501 => array(
				"RENAME TABLE xf_liam_macros_admin TO liam_macros_admin",
				"ALTER TABLE liam_macros_admin CHANGE id macro_id int(10) unsigned NOT NULL AUTO_INCREMENT",
				'ignoreerror' => "ALTER TABLE liam_macros_admin ADD thread_title VARCHAR(50) NOT NULL"
			)
		);
	}


	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getUninstallSql()
	 */
	protected function _getUninstallSql()
	{
		return 'DROP TABLE IF EXISTS xf_liam_macros_admin';
	}

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getTableName()
	 */
	protected function _getTableName()
	{
		return "xf_liam_macros_admin";
	}

}