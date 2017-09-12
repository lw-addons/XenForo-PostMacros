<?php

class LiamW_Macros_Extend_DataWriter_User extends XFCP_LiamW_Macros_Extend_DataWriter_User
{
	protected function _getFields()
	{
		$newFields = array(
			'xf_user_option' => array(
				'macros_hide_qr' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 0
				),
				'macros_hide_ntnr' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 0
				),
				'macros_hide_convo_qr' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 0
				),
				'macros_hide_convo_ncnr' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 0
				),
			)
		);

		return array_merge_recursive(parent::_getFields(), $newFields);
	}

	protected function _preSave()
	{
		if (XenForo_Application::isRegistered('liamMacros_userData'))
		{
			$this->bulkSet(XenForo_Application::get('liamMacros_userData'));
		}

		parent::_preSave();
	}
}

if (false)
{
	class XFCP_LiamW_Macros_Extend_DataWriter_User extends XenForo_DataWriter_User
	{
	}
}