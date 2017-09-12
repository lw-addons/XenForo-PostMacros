<?php

class LiamW_PostMacros_Extend_DataWriter_DiscussionMessage_Post extends XFCP_LiamW_PostMacros_Extend_DataWriter_DiscussionMessage_Post
{
	protected function _messagePostSave()
	{
		parent::_messagePostSave();

		if (XenForo_Application::isRegistered('liam_postMacros_set_prefix') || XenForo_Application::isRegistered('liam_postMacros_set_locked'))
		{
			$threadDw = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread');
			$threadDw->setExistingData($this->get('thread_id'));

			if (XenForo_Application::isRegistered('liam_postMacros_set_prefix'))
			{
				$threadDw->set('prefix_id', XenForo_Application::get('liam_postMacros_set_prefix'));
			}

			if (XenForo_Application::isRegistered('liam_postMacros_set_locked'))
			{
				$threadDw->set('discussion_open', false);
			}

			$threadDw->save();
		}
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_DataWriter_DiscussionMessage_Post extends XenForo_DataWriter_DiscussionMessage_Post
	{
	}
}