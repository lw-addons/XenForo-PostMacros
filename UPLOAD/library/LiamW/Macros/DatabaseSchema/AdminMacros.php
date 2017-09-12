<?php

class LiamW_Macros_DatabaseSchema_AdminMacros extends LiamW_Shared_DatabaseSchema_Abstract2
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
			30608 => array(
				"ALTER TABLE liam_macros_admin ADD lock_thread TINYINT(1) UNSIGNED NOT NULL DEFAULT 0",
				"ALTER TABLE liam_macros_admin ADD apply_prefix INT(10) UNSIGNED NOT NULL DEFAULT 0"
			)
		);
	}

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getUninstallSql()
	 */
	protected function _getUninstallSql()
	{
		return array("DROP TABLE IF EXISTS xf_liam_macros_admin");
	}

	protected function _getClassName()
	{
		return get_class($this);
	}
}