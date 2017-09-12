<?php

class LiamW_Macros_DatabaseSchema_UserOption extends LiamW_Shared_DatabaseSchema_Abstract2
{
	private $_isOldOptions = false;

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getInstallSql()
	 */
	protected function _getInstallSql()
	{
		return array(
			30603 => "
				ALTER TABLE xf_user_option
					ADD macros_hide_qr TINYINT(1) NOT NULL DEFAULT 0,
					ADD macros_hide_ntnr TINYINT(1) NOT NULL DEFAULT 0,
					ADD macros_hide_convo_qr TINYINT(1) NOT NULL DEFAULT 0,
					ADD macros_hide_convo_ncnr TINYINT(1) NOT NULL DEFAULT 0
			",
		);
	}

	/*
	 * (non-PHPdoc) @see LiamW_Shared_DatabaseSchema_Abstract::_getUninstallSql()
	 */
	protected function _getUninstallSql()
	{
		return array(
			"ALTER TABLE xf_user_option DROP macros_hide_qr",
			"ALTER TABLE xf_user_option DROP macros_hide_ntnr",
			"ALTER TABLE xf_user_option DROP macros_hide_convo_qr",
			"ALTER TABLE xf_user_option DROP macros_hide_convo_ncnr"
		);
	}

	protected function _preInstall()
	{
		if ($this->_installedVersion == 0)
		{
			// New installations use the alter table mechanism.
			$this->_installedVersion = 30603;
		}
		else if ($this->_installedVersion <= 30603)
		{
			$this->_isOldOptions = true;
		}
	}

	protected function _postInstall()
	{
		try
		{
			// Disabled until future version to allow data recovery.
			//$this->_db->query('DROP TABLE IF EXISTS liam_macros_options');
		} catch (Zend_Db_Exception $e)
		{
		}
	}
}