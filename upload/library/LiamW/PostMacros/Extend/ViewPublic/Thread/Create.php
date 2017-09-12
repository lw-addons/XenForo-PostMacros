<?php

class LiamW_PostMacros_Extend_ViewPublic_Thread_Create extends XFCP_LiamW_PostMacros_Extend_ViewPublic_Thread_Create
{
	public function renderHtml()
	{
		parent::renderHtml();

		if (!XenForo_Visitor::getUserId())
		{
			return;
		}

		/** @var LiamW_PostMacros_Model_Macros $macrosModel */
		$macrosModel = XenForo_Model::create('LiamW_PostMacros_Model_Macros');

		$this->_params['macros'] = $macrosModel->getMacrosForSelect();
		$this->_params['canUseMacros'] = XenForo_Visitor::getInstance()
			->hasPermission('liam_postMacros', 'liamMacros_canUseMacros');

		$this->_params['showMacrosSelect'] = $macrosModel->showMacrosSelect($this);

		$this->_params['debug'] = XenForo_Application::debugMode();
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ViewPublic_Thread_Create extends XenForo_ViewPublic_Thread_Create
	{
	}
}