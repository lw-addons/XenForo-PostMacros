<?php

class LiamW_PostMacros_Installer
{
	/** @var Zend_Db_Adapter_Abstract */
	protected static $_db = null;

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
			PRIMARY KEY (admin_macro_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		"
	);

	protected static $_coreChanges = array(
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

		return true;
	}

	public static function install($installedAddon)
	{
		if (!self::_canBeInstalled($error))
		{
			throw new XenForo_Exception($error, true);
		}

		self::$_db = XenForo_Application::getDb();
		XenForo_Db::beginTransaction(self::$_db);

		self::_installTables();

		if (!$installedAddon)
		{
			self::_installCoreAlters();
		}

		XenForo_Db::commit(self::$_db);
	}

	public static function uninstall()
	{
		self::$_db = XenForo_Application::getDb();
		XenForo_Db::beginTransaction(self::$_db);

		self::_uninstallTables();
		self::_uninstallCoreAlters();

		XenForo_Db::commit(self::$_db);
	}

	protected static function _installTables()
	{
		XenForo_Db::beginTransaction(self::$_db);

		foreach (self::$_tables AS $tableName => $installSql)
		{
			self::$_db->query($installSql);
		}

		XenForo_Db::commit(self::$_db);
	}

	protected static function _installCoreAlters()
	{
		XenForo_Db::beginTransaction(self::$_db);

		foreach (self::$_coreChanges as $installSql)
		{
			foreach ($installSql as $sql)
			{
				self::$_db->query($sql);
			}
		}

		XenForo_Db::commit(self::$_db);
	}

	protected static function _uninstallTables()
	{
		XenForo_Db::beginTransaction(self::$_db);

		foreach (self::$_tables AS $tableName => $installSql)
		{
			$tableName = self::$_db->quote($tableName);

			self::$_db->query("DROP TABLE IF EXISTS $tableName");
		}

		XenForo_Db::commit(self::$_db);
	}

	protected static function _uninstallCoreAlters()
	{
		XenForo_Db::beginTransaction(self::$_db);

		foreach (self::$_coreChanges AS $tableName => $installSql)
		{
			$tableName = self::$_db->quote($tableName);

			foreach ($installSql as $columnName => $sql)
			{
				$columnName = self::$_db->quote($columnName);

				self::$_db->query("ALTER TABLE $tableName DROP $columnName");
			}
		}

		XenForo_Db::commit(self::$_db);
	}
}