<?php

class LiamW_PostMacros_Model_Import extends XenForo_Model
{
	public function importUserMacro($oldId, array $macro)
	{
		$productDw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_Macros');

		$productDw->setImportMode(true);
		$productDw->bulkSet($macro);
		$productDw->save();

		$macroId = $productDw->get('macro_id');

		return $macroId;
	}

	public function importAdminMacro($oldId, array $macro)
	{
		$productDw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_AdminMacros');

		$productDw->setImportMode(true);
		$productDw->bulkSet($macro);
		$productDw->save();

		$macroId = $productDw->get('admin_macro_id');

		return $macroId;
	}
}