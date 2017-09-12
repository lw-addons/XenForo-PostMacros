<?php

/**
 * Post Macros Datawriter. Used to write data to the database.
 *
 * @author Liam W
 * @package Post Macros
 * @see XenForo_DataWriter
 *
 */
class LiamW_Macros_DataWriter_Macros extends XenForo_DataWriter
{
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getFields()
	 */
	protected function _getFields()
	{
		return array(
			
			'liam_macros' => array(
				
				'macro_id' => array(
					
					'type' => self::TYPE_UINT,
					'autoIncrement' => true
				),
				'user_id' => array(
					
					'type' => self::TYPE_UINT,
					'required' => true
				),
				'name' => array(
					
					'type' => self::TYPE_STRING,
					'required' => true,
					'requiredError' => 'macros_no_name'
				),
				'macro' => array(
					
					'type' => self::TYPE_STRING,
					'required' => true,
					'requiredError' => 'macros_no_macro'
				),
				'thread_title' => array(
					'type' => self::TYPE_STRING,
					'default' => ''
				),
				'staff_macro' => array(
					
					'type' => self::TYPE_BOOLEAN,
					'required' => true
				)
			)
		);
	}
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getExistingData()
	 */
	protected function _getExistingData($data)
	{
		if (! $macroid = $this->_getExistingPrimaryKey($data, 'macro_id'))
		{
			return false;
		}
		
		return array(
			'liam_macros' => $this->_getMacrosModel()->getMacroFromId($macroid)
		);
	}
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getUpdateCondition()
	 */
	protected function _getUpdateCondition($tableName)
	{
		return 'macro_id = ' . $this->_db->quote($this->getExisting('macro_id'));
	}

	/**
	 * Get the macros model.
	 *
	 * @return LiamW_Macros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_Macros_Model_Macros');
	}

}