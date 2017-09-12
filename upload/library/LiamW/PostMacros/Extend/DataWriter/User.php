<?php

class LiamW_PostMacros_Extend_DataWriter_User extends XFCP_LiamW_PostMacros_Extend_DataWriter_User
{
	protected function _getFields()
	{
		$newFields = array(
			'xf_user_option' => array(
				'post_macros_hide_new_thread_reply' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 1
				),
				'post_macros_hide_thread_quick_reply' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 1
				),
				'post_macros_hide_new_conversation_reply' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 1
				),
				'post_macros_hide_conversation_quick_reply' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 1
				)
			)
		);

		return array_merge_recursive(parent::_getFields(), $newFields);
	}

	protected function _preSave()
	{
		if (XenForo_Application::isRegistered('liam_postMacros_options'))
		{
			$this->bulkSet(XenForo_Application::get('liam_postMacros_options'));
		}

		parent::_preSave();
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_DataWriter_User extends XenForo_DataWriter_User
	{
	}
}