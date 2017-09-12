<?php

class LiamW_Macros_DataWriter_AdminDataWriter extends XenForo_DataWriter
{

	protected function _getFields()
	{
		return array(
			
			'xf_liam_macros_admin' => array(
				
				'macroid' => array(
					
					'type' => XenForo_Input::UINT,
					'autoIncrement' => true
				),
				'name' => array(
					
					'type' => XenForo_Input::STRING,
					'required' => true
				),
				'content' => array(
					
					'type' => XenForo_Input::STRING,
					'required' => true
				),
				'thread_title' => array(
					'type' => XenForo_Input::STRING,
					'default' => ''
				),
				'usergroups' => array(
					
					'type' => XenForo_Input::STRING,
					'required' => true
				)
			)
		);
	}

	protected function _getExistingData($data)
	{
		return array(
			
			'xf_liam_macros_admin' => $this->_getMacrosModel()->getMacroFromId($data, true)
		);
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'id = ' . $this->_db->quote($this->getExisting('id'));
	}

	/**
	 * Gets the model.
	 *
	 * @return LiamW_Macros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_Macros_Model_Macros');
	}

}