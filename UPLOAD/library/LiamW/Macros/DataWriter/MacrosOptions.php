<?php

class LiamW_Macros_DataWriter_MacrosOptions extends XenForo_DataWriter
{
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getFields()
	 */
	protected function _getFields()
	{
		return array(
			
			'liam_macros_options' => array(
				
				'user_id' => array(
					
					'type' => XenForo_DataWriter::TYPE_UINT,
					'required' => true
				),
				'macros_hide_qr' => array(
					
					'type' => XenForo_DataWriter::TYPE_BOOLEAN,
					'default' => 0
				),
				'macros_hide_ntnr' => array(
					
					'type' => XenForo_DataWriter::TYPE_BOOLEAN,
					'default' => 0
				),
				'macros_hide_convo_qr' => array(
					
					'type' => XenForo_DataWriter::TYPE_BOOLEAN,
					'default' => 0
				),
				'macros_hide_convo_ncnr' => array(
					
					'type' => XenForo_DataWriter::TYPE_BOOLEAN,
					'default' => 0
				)
			)
		);
	}
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getExistingData()
	 */
	protected function _getExistingData($data)
	{
		$existing = array(
			
			'liam_macros_options' => $this->_getModel()->getOptionsForUser(XenForo_Visitor::getUserId())
		);
		
		return $existing;
	}
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getUpdateCondition()
	 */
	protected function _getUpdateCondition($tableName)
	{
		return 'user_id = ' . $this->_db->quote($this->getExisting('user_id'));
	}

	/**
	 * Gets macro model from cache.
	 *
	 * @return LiamW_Macros_Model_Macros
	 */
	private function _getModel()
	{
		return XenForo_Model::create('LiamW_Macros_Model_Macros');
	}

}