<?php

class LiamW_PostMacros_DataWriter_Macros extends XenForo_DataWriter
{
	protected $_existingDataErrorPhrase = 'liam_postMacros_requested_macro_not_found';

	protected function _getFields()
	{
		return array(
			'liam_post_macros' => array(
				'macro_id' => array(
					'type' => self::TYPE_UINT,
					'autoIncrement' => true
				),
				'user_id' => array(
					'type' => self::TYPE_UINT,
					'verification' => array(
						'XenForo_DataWriter_Helper_User',
						'verifyUserId'
					),
					'required' => true
				),
				'title' => array(
					'type' => self::TYPE_STRING,
					'maxLength' => 50,
					'required' => true,
					'requiredError' => 'liam_postMacros_please_enter_valid_title'
				),
				'thread_title' => array(
					'type' => self::TYPE_STRING,
					'maxLength' => 100,
					'default' => ''
				),
				'thread_prefix' => array(
					'type' => self::TYPE_UINT,
					'default' => 0
				),
				'content' => array(
					'type' => self::TYPE_STRING,
					'required' => true,
					'requiredError' => 'liam_postMacros_please_enter_valid_content'
				),
				'lock_thread' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 0
				),
				'staff_macro' => array(
					'type' => self::TYPE_BOOLEAN,
					'default' => 0
				),
				'display_order' => array(
					'type' => self::TYPE_UINT,
					'default' => 1
				)
			)
		);
	}

	protected function _getExistingData($data)
	{
		if (!$macroId = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}

		return array('liam_post_macros' => $this->_getMacrosModel()->getMacroById($macroId));
	}

	protected function _getUpdateCondition($tableName)
	{
		return 'macro_id = ' . $this->_db->quote($this->getExisting('macro_id'));
	}

	/**
	 * @return LiamW_PostMacros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_PostMacros_Model_Macros');
	}

}