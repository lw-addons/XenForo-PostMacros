<?php

class LiamW_Macros_DatabaseSchema_Forum extends LiamW_Shared_DatabaseSchema_Abstract2
{
	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getInstallSql()
	 */
	protected function _getInstallSql()
	{
		return array(
			0 => array(
				"ALTER TABLE xf_forum ADD allow_macros BOOLEAN NOT NULL COMMENT 'Post Macros' DEFAULT 1 AFTER find_new",
			)
		);
	}

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getUninstallSql()
	 */
	protected function _getUninstallSql()
	{
		return array(
			"ALTER TABLE xf_forum DROP allow_macros"
		);
	}

}