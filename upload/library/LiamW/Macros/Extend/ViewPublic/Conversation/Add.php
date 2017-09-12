<?php

class LiamW_Macros_Extend_ViewPublic_Conversation_Add extends XFCP_LiamW_Macros_Extend_ViewPublic_Conversation_Add
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
			$this->_params['macros'] = $macrosModel->prepareArrayForDropDown($this, $userMacros, $adminMacros);

			$show = !$macrosModel->hiddenOnConversationCreateReply($userId);

			$this->_params['canViewMacros'] = ($macrosModel->canViewMacros($visitor) && $show);
			XenForo_CodeEvent::fire('liam_macros_ready', array(
				&$this->_params['macros'],
				&$this->_params['canViewMacros'],
				null,
				null
			));
		}

		parent::renderHtml();
	}

}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ViewPublic_Conversation_Add extends XenForo_ViewPublic_Conversation_Add
	{
	}
}