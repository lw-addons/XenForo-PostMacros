<?php

class LiamW_PostMacros_DataWriter_AdminMacros extends XenForo_DataWriter
{
	/**
	 * Gets the fields that are defined for the table. This should return an array with
	 * each key being a table name and a further array with each key being a field in database.
	 * The value of each entry should be an array, which
	 * may have the following keys:
	 *    * type - one of the TYPE_* constants, this controls the type of data in the field
	 *    * autoIncrement - set to true when the field is an auto_increment field. Used to populate this field after an insert
	 *    * required - if set, inserts will be prevented if this field is not set or an empty string (0 is accepted)
	 *   * requiredError - the phrase title that should be used if the field is not set (only if required)
	 *    * default - the default value of the field; used if the field isn't set or if the set value is outside the constraints (if a {@link $_setOption} is set)
	 *    * maxLength - for string/binary types only, the maximum length of the data. For strings, in characters; for binary, in bytes.
	 *    * min - for numeric types only, the minimum value allowed (inclusive)
	 *    * max - for numeric types only, the maximum value allowed (inclusive)
	 *    * allowedValues - an array of allowed values for this field (commonly for enums)
	 *    * verification - a callback to do more advanced verification. Callback function will take params for $value and $this.
	 *
	 * @return array
	 */
	protected function _getFields()
	{
		return array(
			'liam_post_macros_admin' => array(
				'admin_macro_id' => array(
					'type' => self::TYPE_UINT,
					'autoIncrement' => true
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
					'default' => false
				),
				'authorized_usergroups' => array(
					'type' => self::TYPE_SERIALIZED,
					'required' => true
				)
			)
		);
	}

	/**
	 * Gets the actual existing data out of data that was passed in. This data
	 * may be a scalar or an array. If it's a scalar, assume that it is the primary
	 * key (if there is one); if it is an array, attempt to extract the primary key
	 * (or some other unique identifier). Then fetch the correct data from a model.
	 *
	 * @param mixed Data that can uniquely ID this item
	 *
	 * @return array|false
	 */
	protected function _getExistingData($data)
	{
		if (!$macroId = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}

		return array('liam_post_macros_admin' => $this->_getMacrosModel()->getAdminMacroById($macroId));
	}

	/**
	 * Gets SQL condition to update the existing record. Should read from {@link _existingData}.
	 *
	 * @param string Table name
	 *
	 * @return string
	 */
	protected function _getUpdateCondition($tableName)
	{
		return 'admin_macro_id = ' . $this->_db->quote($this->getExisting('admin_macro_id'));
	}

	/**
	 * @return LiamW_PostMacros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_PostMacros_Model_Macros');
	}

}