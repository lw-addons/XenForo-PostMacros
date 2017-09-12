<?php

class LiamW_Macros_DatabaseSchema_MacroOptions extends LiamW_Shared_DatabaseSchema_Abstract
{
	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getInstallSql()
	 */
	protected function _getInstallSql()
	{
		return array(
			0 => "CREATE TABLE IF NOT EXISTS `liam_macros_options` (
				`user_id` int(10) unsigned NOT NULL,
				`macros_hide_qr` tinyint(1) NOT NULL,
				`macros_hide_ntnr` tinyint(1) NOT NULL,
				`macros_hide_convo_qr` tinyint(1) NOT NULL,
				`macros_hide_convo_ncnr` tinyint(1) NOT NULL,
				UNIQUE KEY `user_id` (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
			30300 => "ALTER TABLE `xf_liam_macros_options` ADD COLUMN `macros_hide_convo_qr` tinyint(1) NOT NULL, ADD COLUMN `macros_hide_convo_ncnr` tinyint(1) NOT NULL;",
			30501 => array(
				"RENAME TABLE `xf_liam_macros_options` TO `liam_macros_options`",
				"ALTER TABLE `liam_macros_options` CHANGE `userid` `user_id` int(10) unsigned NOT NULL"
			)
		);
	}
	
	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getUninstallSql()
	 */
	protected function _getUninstallSql()
	{
		return 'DROP TABLE IF EXISTS xf_liam_macros_options';
	}
	
	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getTableName()
	 */
	protected function _getTableName()
	{
		return "xf_liam_macros_options";
	}

}

?>