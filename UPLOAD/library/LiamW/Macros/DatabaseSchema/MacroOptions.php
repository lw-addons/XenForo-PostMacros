<?php

class LiamW_Macros_DatabaseSchema_MacroOptions extends LiamW_Shared_DatabaseSchema_Abstract
{

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getDropSql()
	*/
	protected function _getDropSql()
	{
		return "DROP TABLE IF EXISTS `xf_liam_macros_options`;";
	}

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getCreateSql()
	*/
	protected function _getSql()
	{
		$array = array(0 => "CREATE TABLE IF NOT EXISTS `xf_liam_macros_options` (
				`userid` int(10) NOT NULL,
				`macros_hide_qr` tinyint(1) NOT NULL,
				`macros_hide_ntnr` tinyint(1) NOT NULL,
				`macros_hide_convo_qr` tinyint(1) NOT NULL,
				`macros_hide_convo_ncnr` tinyint(1) NOT NULL,
				UNIQUE KEY `userid` (`userid`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;", 30300 => "ALTER TABLE `xf_liam_macros_options` ADD COLUMN `macros_hide_convo_qr` tinyint(1) NOT NULL, ADD COLUMN `macros_hide_convo_ncnr` tinyint(1) NOT NULL;");

		return $array;
	}

	protected function _getTableName()
	{
		return "xf_liam_macros_options";
	}
}

?>