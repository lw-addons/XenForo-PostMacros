<?php

class LiamW_PostMacros_Listener
{
	public static function extendAccountController($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ControllerPublic_Account';
	}

	public static function extendThreadController($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ControllerPublic_Thread';
	}

	public static function extendForumAdminController($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ControllerAdmin_Forum';
	}

	public static function extendForumDataWriter($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_DataWriter_Forum';
	}

	public static function extendUserDataWriter($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_DataWriter_User';
	}

	public static function extendPostDataWriter($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_DataWriter_DiscussionMessage_Post';
	}

	public static function extendForumController($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ControllerPublic_Forum';
	}

	public static function extendThreadDataWriter($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_DataWriter_Discussion_Thread';
	}

	public static function extendImportModel($class, array &$extend)
	{
		XenForo_Model_Import::$extraImporters[] = 'LiamW_PostMacros_Importer_Macros';
	}

	public static function editorSetup(XenForo_View $view, $formCtrlName, &$message, array &$editorOptions, &$showWysiwyg)
	{
		if (!XenForo_Visitor::getUserId() || !($view instanceof XenForo_ViewPublic_Base) || !empty($editorOptions['noMacros']))
		{
			return;
		}

		/** @var LiamW_PostMacros_Model_Macros $macrosModel */
		$macrosModel = XenForo_Model::create('LiamW_PostMacros_Model_Macros');

		$editorOptions['macros'] = $macrosModel->getMacrosForSelect();
		$editorOptions['canUseMacros'] = XenForo_Visitor::getInstance()
			->hasPermission('liam_postMacros', 'liamMacros_canUseMacros');
		$editorOptions['showMacrosSelect'] = $macrosModel->showMacrosSelect($view) && (count($editorOptions['macros']['user']) || count($editorOptions['macros']['admin']));
		$editorOptions['debug'] = XenForo_Application::debugMode();

		if (!$editorOptions['height'])
		{
			// For quick reply - we have to use the quick_reply template, which doesn't normally have access to the editorOptions array.
			$view->setParams(array(
				'editorOptions' => $editorOptions,
				'editorId' => 'ctrl_' . $formCtrlName
			));
		}
	}

	public static function containerParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		$params['canUseMacros'] = XenForo_Visitor::getInstance()
			->hasPermission('liam_postMacros', 'liamMacros_canUseMacros');
	}

	// The view extension functions are still here because XF has a habit of not letting installs go through
	// if a function for a listener that has been removed doesn't exist...

	public static function extendThreadViewView($class, array &$extend)
	{
	}

	public static function extendThreadReplyView($class, array &$extend)
	{
	}

	public static function extendThreadCreateView($class, array &$extend)
	{
	}

	public static function extendConversationAddView($class, array &$extend)
	{
	}

	public static function extendConversationReplyView($class, array &$extend)
	{
	}

	public static function extendConversationViewView($class, array &$extend)
	{
	}
}