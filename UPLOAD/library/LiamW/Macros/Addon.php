<?php

/**
 *
 * Contains addon functions, install, uninstall, template hook calls etc.
 *
 * @author  Liam W
 * @package Post Macros
 *
 */
class LiamW_Macros_Addon
{

	public static function install($installedAddon)
	{
		if (XenForo_Application::$versionId < 1020070)
		{
			throw new XenForo_Exception('This addon requires XenForo 1.2.0 or higher. You are using XenForo ' . XenForo_Application::$version . '. Please upgrade and then install.',
				true);
		}

		$version = is_array($installedAddon) ? $installedAddon['version_id'] : 0;

		$dbMacros = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_Macros');
		$dbAdminMacros = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_AdminMacros');
		$dbMacroOptions = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_MacroOptions');
		$forum = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_Forum');

		$dbMacros->install($version);
		$dbAdminMacros->install($version);
		$dbMacroOptions->install($version);
		$forum->install($version);
	}

	public static function uninstall()
	{
		$dbMacros = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_Macros');
		$dbAdminMacros = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_AdminMacros');
		$dbMacroOptions = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_MacroOptions');
		$forum = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_Forum');

		$dbMacros->uninstall();
		$dbAdminMacros->uninstall();
		$dbMacroOptions->uninstall();
		$forum->uninstall();
	}

	public static function extendClass($class, array &$extend)
	{
		switch ($class)
		{
			case "XenForo_ViewPublic_Thread_Create":
			case "XenForo_ViewPublic_Thread_Reply":
				XenForo_Application::set('macro_type', 'ntnr');
				$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_View';
				break;
			case "XenForo_ViewPublic_Thread_View":
				XenForo_Application::set('macro_type', 'qr');
				$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_View';
				break;
			case "XenForo_ViewPublic_Conversation_View":
				XenForo_Application::set('macro_type', 'convoqr');
				$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_View';
				break;
			case "XenForo_ViewPublic_Conversation_Reply":
			case "XenForo_ViewPublic_Conversation_Add":
				XenForo_Application::set('macro_type', 'convoncnr');
				$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_View';
				break;
			case "XenForo_ControllerAdmin_Forum":
				$extend[] = 'LiamW_Macros_Extend_ControllerAdmin_Forum';
				break;
			case "XenForo_DataWriter_Forum":
				$extend[] = 'LiamW_Macros_Extend_DataWriter_Forum';
				break;
			case "XenForo_DataWriter_User":
				$extend[] = 'LiamW_Macros_Extend_DataWriter_User';
				break;
			case "XenForo_ControllerPublic_Account":
				$extend[] = 'LiamW_Macros_Extend_ControllerPublic_Account';
		}
	}

	public static function containerParam(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		/* @var $model LiamW_Macros_Model_Macros */
		$model = XenForo_Model::create('LiamW_Macros_Model_Macros');

		$params['canUseMacros'] = $model->canViewMacros(XenForo_Visitor::getInstance(), true);
	}

}

