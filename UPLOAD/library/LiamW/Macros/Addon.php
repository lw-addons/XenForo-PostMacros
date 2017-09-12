<?php

/**
 *
 * Contains addon functions, install, uninstall, template hook calls etc.
 *
 * @author Liam W
 * @package Post Macros
 *
 */
class LiamW_Macros_Addon
{

	/**
	 * Function that creates the database table for this addon.
	 *
	 * @author Liam W
	 */
	public static function install($installedAddon)
	{
		if (XenForo_Application::$versionId < 1020070)
		{
			throw new XenForo_Exception('This addon required XenForo 1.2.0 or higher. You are using XenForo ' . XenForo_Application::$version . '. Please upgrade then install.', true);
		}
		
		$version = is_array($installedAddon) ? $installedAddon['version_id'] : 0;
		
		$dbMacros = new LiamW_Macros_DatabaseSchema_Macros($version);
		$dbAdminMacros = new LiamW_Macros_DatabaseSchema_AdminMacros($version);
		$dbMacroOptions = new LiamW_Macros_DatabaseSchema_MacroOptions($version);
		$forum = new LiamW_Macros_DatabaseSchema_Forum($version);
		
		$dbMacros->install();
		$dbAdminMacros->install();
		$dbMacroOptions->install();
		$forum->install();
	}

	/**
	 * Function that removes the database table for this addon.
	 *
	 * @author Liam W
	 */
	public static function uninstall($installedAddon)
	{
		$dbMacros = new LiamW_Macros_DatabaseSchema_Macros();
		$dbAdminMacros = new LiamW_Macros_DatabaseSchema_AdminMacros();
		$dbMacroOptions = new LiamW_Macros_DatabaseSchema_MacroOptions();
		$forum = new LiamW_Macros_DatabaseSchema_Forum();
		
		$dbMacros->uninstall();
		$dbAdminMacros->uninstall();
		$dbMacroOptions->uninstall();
		$forum->uninstall();
	}

	/**
	 * Listener to extend classes for drop down.
	 *
	 * @param unknown $class        	
	 * @param array $extend        	
	 */
	public static function extendClass($class, array &$extend)
	{
		switch ($class) {
			case "XenForo_ViewPublic_Thread_Create":
			case "XenForo_ViewPublic_Thread_Reply":
				XenForo_Application::set('macro_type', 'ntnr');
				$extend[] = 'LiamW_Macros_Extend_ViewPublic_Thread_View';
				break;
			case "XenForo_ViewPublic_Conversation_View":
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
		}
		
		return true;
	}

	public static function containerParam(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		/* @var $model LiamW_Macros_Model_Macros */
		$model = XenForo_Model::create('LiamW_Macros_Model_Macros');
		
		$params['canUseMacros'] = $model->canViewMacros(XenForo_Visitor::getInstance(), true);
	}

}

