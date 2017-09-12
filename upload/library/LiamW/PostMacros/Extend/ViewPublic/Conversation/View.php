<?php

class LiamW_PostMacros_Extend_ViewPublic_Conversation_View extends XFCP_LiamW_PostMacros_Extend_ViewPublic_Conversation_View
{
	public function renderHtml()
	{
		/** @var LiamW_PostMacros_Model_Macros $macrosModel */
		$macrosModel = XenForo_Model::create('LiamW_PostMacros_Model_Macros');

		$this->_params['macros'] = $macrosModel->getMacrosForSelect();
		$this->_params['canUseMacros'] = XenForo_Visitor::getInstance()
			->hasPermission('liam_postMacros', 'liamMacros_canUseMacros');

		$this->_params['showMacrosSelect'] = $macrosModel->showMacrosSelect($this);

		$this->_params['debug'] = XenForo_Application::debugMode();

		parent::renderHtml();
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ViewPublic_Conversation_View extends XenForo_ViewPublic_Conversation_View
	{
	}
}