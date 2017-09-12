<?php

class LiamW_PostMacros_Listener
{
	public static function extendThreadViewView($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ViewPublic_Thread_View';
	}

	public static function extendThreadReplyView($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ViewPublic_Thread_Reply';
	}

	public static function extendThreadCreateView($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ViewPublic_Thread_Create';
	}

	public static function extendConversationAddView($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ViewPublic_Conversation_Add';
	}

	public static function extendConversationReplyView($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ViewPublic_Conversation_Reply';
	}

	public static function extendConversationViewView($class, array &$extend)
	{
		$extend[] = 'LiamW_PostMacros_Extend_ViewPublic_Conversation_View';
	}

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

	public static function extendImportModel($class, array &$extend)
	{
		XenForo_Model_Import::$extraImporters[] = 'LiamW_PostMacros_Importer_Macros';
	}

	public static function containerParams(array &$params, XenForo_Dependencies_Abstract $dependencies)
	{
		$params['canUseMacros'] = XenForo_Visitor::getInstance()
			->hasPermission('liam_postMacros', 'liamMacros_canUseMacros');
	}
}