<?php

class LiamW_Macros_DataWriter_AdminMacros extends XenForo_DataWriter
{

	protected function _getFields()
	{
		return array(
			'liam_macros_admin' => array(
				'macro_id' => array(
					'type' => XenForo_Input::UINT,
					'autoIncrement' => true
				),
				'name' => array(
					'type' => XenForo_Input::STRING,
					'required' => true,
					'maxLength' => 50
				),
				'content' => array(
					'type' => XenForo_Input::STRING,
					'required' => true
				),
				'thread_title' => array(
					'type' => XenForo_Input::STRING,
					'default' => '',
					'maxLength' => 50
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
			'liam_macros_admin' => $this->_getMacrosModel()->getMacroFromId($data, true)
		);
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'macro_id = ' . $this->_db->quote($this->getExisting('macro_id'));
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