<?php

class LiamW_PostMacros_Model_Import extends XenForo_Model
{
	public function importUserMacro($oldId, array $macro)
	{
		$macroDw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_Macros');

		$macroDw->setImportMode(true);
		$macroDw->bulkSet($macro);
		$macroDw->save();

		$macroId = $macroDw->get('macro_id');

		return $macroId;
	}

	public function importAdminMacro($oldId, array $macro)
	{
		$macroDw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_AdminMacros');

		$macroDw->setImportMode(true);
		$macroDw->bulkSet($macro);
		$macroDw->save();

		$macroId = $macroDw->get('admin_macro_id');

		return $macroId;
	}
}