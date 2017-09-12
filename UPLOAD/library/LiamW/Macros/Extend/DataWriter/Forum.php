<?php

class LiamW_Macros_Extend_DataWriter_Forum extends XFCP_LiamW_Macros_Extend_DataWriter_Forum
{
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter_Forum::_getFields()
	 */
	protected function _getFields()
	{
		$fields = parent::_getFields();
		
		$fields['xf_forum']['allow_macros'] = array(
			'type' => self::TYPE_BOOLEAN,
			'default' => true
		);
		
		return $fields;
	}
	

	/*
	 * (non-PHPdoc) @see XenForo_DataWriter_Node::_preSave()
	 */
	protected function _preSave()
	{
		if (XenForo_Application::isRegistered('allow_macros_forum'))
		{
			$this->set('allow_macros', XenForo_Application::get('allow_macros_forum'));
		}
		
		return parent::_preSave();
	}

}

/*class XFCP_LiamW_Macros_Extend_DataWriter_Forum extends XenForo_DataWriter_Forum
{

}*/