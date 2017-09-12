<?php

class LiamW_Macros_DatabaseSchema_AdminMacros extends LiamW_Shared_DatabaseSchema_Abstract
{

	protected function _getDropSql()
	{
		return "DROP TABLE IF EXISTS `xf_liam_macros_admin`;";
	}

	protected function _getSql()
	{
		$array = array(
			0 => "CREATE TABLE IF NOT EXISTS `xf_liam_macros_admin` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`name` text NOT NULL,
			`content` blob NOT NULL,
			`usergroups` text NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `id` (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
			30201 => "ALTER TABLE `xf_liam_macros_admin` CONVERT TO CHARACTER SET `utf8`",
			30401 => "ALTER TABLE `xf_liam_macros_admin` ADD COLUMN `thread_title` VARCHAR(50) NOT NULL AFTER `content`;"
		);
		
		return $array;
	}

	protected function _getTableName()
	{
		return "xf_liam_macros_admin";
	}

}

?>