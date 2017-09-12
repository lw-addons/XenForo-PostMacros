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

	protected static $_canViewMacros = null;

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
		$dbMacroOptions = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_UserOption');
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
		$dbMacroOptions = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_UserOption');
		$forum = LiamW_Shared_DatabaseSchema_Abstract2::create('LiamW_Macros_DatabaseSchema_Forum');

		$dbMacros->uninstall();
		$dbAdminMacros->uninstall();
		$dbMacroOptions->uninstall();
		$forum->uninstall();
	}

	public static function containerParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		if (self::$_canViewMacros == null)
		{
			/* @var $model LiamW_Macros_Model_Macros */
			$model = XenForo_Model::create('LiamW_Macros_Model_Macros');

			self::$_canViewMacros = $model->canViewMacros(XenForo_Visitor::getInstance(), true);
		}

		$params['canViewMacros'] = self::$_canViewMacros;
	}

	public static function templateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
	{
		if (self::$_canViewMacros == null)
		{
			/* @var $model LiamW_Macros_Model_Macros */
			$model = XenForo_Model::create('LiamW_Macros_Model_Macros');

			self::$_canViewMacros = $model->canViewMacros(XenForo_Visitor::getInstance(), true);
		}

		$params['canViewMacros'] = self::$_canViewMacros;
	}

	public static function extendThreadCreateView($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_Create';
	}

	public static function extendThreadReplyView($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_Reply';
	}

	public static function extendThreadViewView($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_View';
	}

	public static function extendConversationAddView($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ViewPublic_Conversation_Add';
	}

	public static function extendConversationReplyView($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ViewPublic_Conversation_Reply';
	}

	public static function extendConversationViewView($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ViewPublic_Conversation_View';
	}

	public static function extendForumAdminController($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ControllerAdmin_Forum';
	}

	public static function extendAccountController($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ControllerPublic_Account';
	}

	public static function extendForumDataWriter($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_DataWriter_Forum';
	}

	public static function extendUserDataWriter($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_DataWriter_User';
	}

	public static function extendThreadController($class, array &$extend)
	{
		$extend[] = 'LiamW_Macros_Extend_ControllerPublic_Thread';
	}
}

