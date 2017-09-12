<?php

class LiamW_Macros_DatabaseSchema_Macros extends LiamW_Shared_DatabaseSchema_Abstract
{

	protected function _getDropSql()
	{
		return "DROP TABLE IF EXISTS `xf_liam_macros`;";
	}

	protected function _getSql()
	{
		$array = array(0 => "CREATE TABLE IF NOT EXISTS `xf_liam_macros` (
				`macroid` int(100) NOT NULL AUTO_INCREMENT,
				`userid` int(100) NOT NULL,
				`name` text NOT NULL,
				`macro` text NOT NULL,
				`thread_title` VARCHAR(50) NOT NULL,
				`staff_macro` BOOLEAN NOT NULL DEFAULT FALSE,
				PRIMARY KEY (`macroid`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;", 5 => "ALTER TABLE  `xf_liam_macros` ADD  `staff_macro` BOOLEAN NOT NULL DEFAULT FALSE", 30201 => "ALTER TABLE `xf_liam_macros` CONVERT TO CHARACTER SET `utf8`", 30401 => "ALTER TABLE `xf_liam_macros` ADD COLUMN `thread_title` VARCHAR(50) NOT NULL AFTER `macro`;");

		return $array;
	}

	protected function _getTableName()
	{
		return "xf_liam_macros";
	}
}

?>