<?php

class LiamW_Shared_ExtraField
{
	public static function addExtraField($dwName, $tableName, $fieldName, $fieldType, $fieldInfo, array &$extendArray, $functionName = '_preSave()')
	{
		// We need the the classes instantiating
		$extendArray[] = 'LiamW_ExtraField_AutoAdd_' . $dwName;

		$classLine = "
			class XFCP_LiamW_ExtraField_AutoAdd_$dwName extends $dwName {}

			class LiamW_ExtraField_AutoAdd_$dwName extends XFCP_LiamW_ExtraField_AutoAdd_$dwName
			{
				protected function _getFields()
				{
					\$existingFields = parent::_getFields();

					\$existingFields[$tableName][$fieldName] = $fieldInfo;

					return \$existingFields;
				}

				protected function $functionName
				{
					\$input = new XenForo_Input(new Zend_Controller_Request_Http());

					if (\$fieldValue = \$input->filterSingle($fieldName, $fieldType))
					{
						\$this->set($fieldName, \$fieldValue);
					}

					parent::$functionName;
				}
			}
		";

		eval($classLine);
	}
}