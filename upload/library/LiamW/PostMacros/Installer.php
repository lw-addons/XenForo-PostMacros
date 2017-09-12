<?php

class LiamW_PostMacros_Installer
{
	protected static $_tables = array(
		'liam_post_macros' => "
			CREATE TABLE IF NOT EXISTS liam_post_macros (
				macro_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id INT(10) UNSIGNED NOT NULL,
				title VARCHAR(50) NOT NULL,
				thread_title VARCHAR(100) NOT NULL,
				thread_prefix INT(10) UNSIGNED NOT NULL DEFAULT 0,
				content MEDIUMTEXT NOT NULL,
				lock_thread TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
				staff_macro TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
				display_order INT(10) UNSIGNED NOT NULL DEFAULT 1,
			PRIMARY KEY (macro_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		",
		'liam_post_macros_admin' => "
			CREATE TABLE IF NOT EXISTS liam_post_macros_admin (
				admin_macro_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				title VARCHAR(50) NOT NULL,
				thread_title VARCHAR(100) NOT NULL,
				thread_prefix INT(10) UNSIGNED NOT NULL DEFAULT 0,
				content MEDIUMTEXT NOT NULL,
				lock_thread TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
				authorized_usergroups BLOB NULL DEFAULT NULL,
				display_order INT(10) UNSIGNED NOT NULL DEFAULT 1,
			PRIMARY KEY (admin_macro_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		"
	);

	protected static $_coreAlters = array(
		'xf_user_option' => array(
			'post_macros_hide_new_thread_reply' => "
				ALTER TABLE xf_user_option ADD post_macros_hide_new_thread_reply TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
			",
			'post_macros_hide_thread_quick_reply' => "
				ALTER TABLE xf_user_option ADD post_macros_hide_thread_quick_reply TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
			",
			'post_macros_hide_new_conversation_reply' => "
				ALTER TABLE xf_user_option ADD post_macros_hide_new_conversation_reply TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
			",
			'post_macros_hide_conversation_quick_reply' => "
				ALTER TABLE xf_user_option ADD post_macros_hide_conversation_quick_reply TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
			",
			"post_macros_hide_other" => "
				ALTER TABLE xf_user_option ADD post_macros_hide_other TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER post_macros_hide_conversation_quick_reply
			"
		),
		'xf_forum' => array(
			'post_macros_enable' => "
				ALTER TABLE xf_forum ADD post_macros_enable TINYINT(1) UNSIGNED NOT NULL DEFAULT 1
			"
		)
	);

	protected static function _canBeInstalled(&$error)
	{
		if (XenForo_Application::$versionId < 1020070)
		{
			$error = "This add-on requires XenForo 1.2.0+.";

			return false;
		}

		$errors = XenForo_Helper_Hash::compareHashes(LiamW_PostMacros_CheckSums::getHashes());

		if ($errors)
		{
			$error = "The following file(s) don't exist or contain incorrect content: <ul>";

			foreach ($errors as $file => $errorType)
			{
				$error .= "<li>$file - " . ($errorType == 'mismatch' ? 'File content incorrect' : 'File not found') . '</li>';
			}

			$error .= "</ul> Please ensure that any file listed above exists, and that it contains the correct contents by reuploading it.";

			return false;
		}

		return true;
	}

	public static function install($installedAddon)
	{
		if (!self::_canBeInstalled($error))
		{
			throw new XenForo_Exception($error, true);
		}

		self::_installTables();
		self::_installCoreAlters();

		if ($installedAddon)
		{
			$installedVersion = $installedAddon['version_id'];

			if ($installedVersion <= 400070)
			{
				self::_runQuery("ALTER TABLE liam_post_macros ADD display_order INT(10) UNSIGNED NOT NULL DEFAULT 1");
			}

			if ($installedVersion <= 400100)
			{
				self::_runQuery("ALTER TABLE liam_post_macros_admin ADD display_order INT(10) UNSIGNED NOT NULL DEFAULT 1");
			}
		}
	}

	public static function uninstall()
	{
		$db = XenForo_Application::getDb();
		XenForo_Db::beginTransaction($db);

		self::_uninstallTables($db);
		self::_uninstallCoreAlters($db);

		XenForo_Db::commit($db);
	}

	protected static function _installTables(Zend_Db_Adapter_Abstract $db = null)
	{
		foreach (self::$_tables AS $tableName => $installSql)
		{
			self::_runQuery($installSql, $db);
		}
	}

	protected static function _installCoreAlters(Zend_Db_Adapter_Abstract $db = null)
	{
		foreach (self::$_coreAlters as $tableName => $coreAlters)
		{
			foreach ($coreAlters as $columnName => $installSql)
			{
				self::_runQuery($installSql, $db);
			}
		}
	}

	protected static function _uninstallTables(Zend_Db_Adapter_Abstract $db = null)
	{
		foreach (self::$_tables AS $tableName => $installSql)
		{
			self::_runQuery("DROP TABLE IF EXISTS $tableName", $db);
		}
	}

	protected static function _uninstallCoreAlters(Zend_Db_Adapter_Abstract $db = null)
	{
		foreach (self::$_coreAlters AS $tableName => $coreAlters)
		{
			foreach ($coreAlters as $columnName => $sql)
			{
				self::_runQuery("ALTER TABLE $tableName DROP $columnName", $db);
			}
		}
	}

	protected static function _runQuery($sql, Zend_Db_Adapter_Abstract $db = null)
	{
		if ($db == null)
		{
			$db = XenForo_Application::getDb();
		}

		try
		{
			$db->query($sql);
		} catch (Zend_Db_Exception $e)
		{

		}
	}
}