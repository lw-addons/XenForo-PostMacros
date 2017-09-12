<?php

class LiamW_PostMacros_Extend_DataWriter_Discussion_Thread extends XFCP_LiamW_PostMacros_Extend_DataWriter_Discussion_Thread
{
	protected function _discussionPreSave()
	{
		if (XenForo_Application::isRegistered('liam_postMacros_set_prefix') && XenForo_Application::get('liam_postMacros_set_prefix'))
		{
			$this->set('prefix_id', XenForo_Application::get('liam_postMacros_set_prefix'));
		}

		if (XenForo_Application::isRegistered('liam_postMacros_set_locked'))
		{
			$this->set('discussion_open', false);
		}

		parent::_discussionPreSave();
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_DataWriter_Discussion_Thread extends XenForo_DataWriter_Discussion_Thread
	{
	}
}