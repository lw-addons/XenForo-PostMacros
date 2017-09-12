<?php

class LiamW_PostMacros_Extend_DataWriter_Forum extends XFCP_LiamW_PostMacros_Extend_DataWriter_Forum
{
	protected function _getFields()
	{
		$existingFields = parent::_getFields();

		$existingFields['xf_forum']['post_macros_enable'] = array(
			'type' => self::TYPE_BOOLEAN,
			'default' => 1
		);

		return $existingFields;
	}

	protected function _preSave()
	{
		if (XenForo_Application::isRegistered('liam_postMacros_forum'))
		{
			$this->bulkSet(XenForo_Application::get('liam_postMacros_forum'));
		}

		parent::_preSave();
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_DataWriter_Forum extends XenForo_DataWriter_Forum
	{
	}
}