<?php

class LiamW_Macros_Extend_ViewPublic_Thread_Reply extends XFCP_LiamW_Macros_Extend_ViewPublic_Thread_Reply
{
	public function renderHtml()
	{
		$visitor = XenForo_Visitor::getInstance();
		$userId = $visitor->getUserId();

		/** @var LiamW_Macros_Model_Macros $macrosModel */
		$macrosModel = XenForo_Model::create('LiamW_Macros_Model_Macros');

		$userMacros = $macrosModel->getMacrosForUser($userId,
			$visitor->hasPermission('macro_permissions', 'use_staff_macros'));
		$adminMacros = $macrosModel->getAdminMacrosForUser($visitor->toArray(), true);

		if ($userId)
		{
			list($userMacros, $adminMacros) = $macrosModel->prepareArrayForDropDown($this, $userMacros, $adminMacros);
			$this->_params['userMacros'] = $userMacros;
			$this->_params['adminMacros'] = $adminMacros;

			$show = !$macrosModel->hiddenOnThreadCreateReply($userId);

			$this->_params['canViewMacros'] = ($macrosModel->canViewMacros($visitor) && $show && $this->_params['forum']['allow_macros']);
		}

		$this->_params['debug'] = XenForo_Application::debugMode();

		parent::renderHtml();
	}

}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ViewPublic_Thread_Reply extends XenForo_ViewPublic_Thread_Reply
	{
	}
}