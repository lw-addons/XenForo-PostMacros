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
			throw new XenForo_Exception('This addon is compatible with xenForo 1.2.0+ only!', true);
		}
		
		if (is_array($installedAddon))
		{
			$version = $installedAddon["version_id"];
		}
		else
		{
			$version = 0;
		}
		
		// this new system is much cleaner, don't you think?
		
		$dbMacros = new LiamW_Macros_DatabaseSchema_Macros($version);
		$dbAdminMacros = new LiamW_Macros_DatabaseSchema_AdminMacros($version);
		$dbMacroOptions = new LiamW_Macros_DatabaseSchema_MacroOptions($version);
		
		$r1 = $dbMacros->run();
		$r2 = $dbAdminMacros->run();
		$r3 = $dbMacroOptions->run();
		
		if ($r1 !== true || $r2 !== true || $r3 !== true)
		{
			throw new XenForo_Exception("An error occured while installing/updating tables. Please check the server error log for more info.", true);
		}
	}

	/**
	 * Function that removes the database table for this addon.
	 *
	 * @author Liam W
	 */
	public static function uninstall($installedAddon)
	{
		$dbMacros = new LiamW_Macros_DatabaseSchema_Macros(0, true);
		$dbAdminMacros = new LiamW_Macros_DatabaseSchema_AdminMacros(0, true);
		$dbMacroOptions = new LiamW_Macros_DatabaseSchema_MacroOptions(0, true);
		
		$dbMacros->run();
		$dbAdminMacros->run();
		$dbMacroOptions->run();
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
				$extend[] = 'LiamW_Macros_ViewPublic_Thread_View';
				break;
			case "XenForo_ViewPublic_Conversation_View":
			case "XenForo_ViewPublic_Thread_View":
				XenForo_Application::set('macro_type', 'qr');
				$extend[] = 'LiamW_Macros_ViewPublic_Thread_View';
				break;
			case "XenForo_ViewPublic_Conversation_View":
				XenForo_Application::set('macro_type', 'convoqr');
				$extend[] = 'LiamW_Macros_ViewPublic_Thread_View';
				break;
			case "XenForo_ViewPublic_Conversation_Reply":
			case "XenForo_ViewPublic_Conversation_Add":
				XenForo_Application::set('macro_type', 'convoncnr');
				$extend[] = 'LiamW_Macros_ViewPublic_Thread_View';
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

