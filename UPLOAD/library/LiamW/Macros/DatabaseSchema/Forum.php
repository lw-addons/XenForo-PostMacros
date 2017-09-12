<?php

class LiamW_Macros_DatabaseSchema_Forum extends LiamW_Shared_DatabaseSchema_Abstract
{
	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getInstallSql()
	 */
	protected function _getInstallSql()
	{
		return array(
			0 => array(
				"ALTER TABLE  `xf_forum` ADD  `allow_macros` BOOLEAN NOT NULL COMMENT  'Post Macros' AFTER  `find_new`",
				"UPDATE `xf_forum` SET `allow_macros`='1'"
			),
			30601 => array(
				"ALTER TABLE  `xf_forum` ADD  `allow_macros` BOOLEAN NOT NULL COMMENT  'Post Macros' AFTER  `find_new`",
				"UPDATE `xf_forum` SET `allow_macros`='1'"
			)
		);
	}
	

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getTableName()
	 */
	protected function _getTableName()
	{
		return 'xf_forum';
	}
	

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getUninstallSql()
	 */
	protected function _getUninstallSql()
	{
		return 'ALTER TABLE `xf_forum` DROP `allow_macros`';
	}

}