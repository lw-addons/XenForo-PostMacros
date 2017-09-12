<?php

class LiamW_Macros_DataWriter_MacrosOptions extends XenForo_DataWriter
{
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getFields()
	 */
	protected function _getFields()
	{
		return array(
			
			'xf_liam_macros_options' => array(
				
				'userid' => array(
					
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
			
			'xf_liam_macros_options' => $this->_getModel()->getOptionsForUser(XenForo_Visitor::getUserId())
		);
		
		return $existing;
	}
	
	/*
	 * (non-PHPdoc) @see XenForo_DataWriter::_getUpdateCondition()
	 */
	protected function _getUpdateCondition($tableName)
	{
		return "`userid`='{$this->_db->quote($this->getExisting('userid'))}'";
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