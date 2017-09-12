<?php

abstract class LiamW_PostMacros_ControllerAdmin_Abstract extends XenForo_ControllerAdmin_Abstract
{
	protected function _preDispatch($action)
	{
		$this->assertAdminPermission('lw_manageAdminMacros');
	}

	protected function _getMacrosPerPage()
	{
		return max(5, XenForo_Application::getOptions()->liam_postMacros_perPage);
	}

	/**
	 * @return LiamW_PostMacros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_PostMacros_Model_Macros');
	}
}