<?php

class LiamW_PostMacros_Extend_DataWriter_DiscussionMessage_Post extends XFCP_LiamW_PostMacros_Extend_DataWriter_DiscussionMessage_Post
{
	protected function _messagePostSave()
	{
		parent::_messagePostSave();

		if (XenForo_Application::isRegistered('liam_postMacros_set_prefix'))
		{
			$threadDw = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread',
				XenForo_DataWriter::ERROR_SILENT);
			$threadDw->setExistingData($this->get('thread_id'));
			$threadDw->set('prefix_id', XenForo_Application::isRegistered('liam_postMacros_set_prefix'));
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