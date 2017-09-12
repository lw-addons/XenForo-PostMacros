<?php

class LiamW_Macros_Extend_ViewPublic_Thread_Create extends XFCP_LiamW_Macros_Extend_ViewPublic_Thread_Create
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

		$forum = $this->_params['forum'];

		foreach ($userMacros as $key => $macro)
		{
			if ($forum)
			{
				$userMacros[$key]['content'] = str_replace("{forumname}", $forum['title'], $macro['content']);
			}
		}

		foreach ($adminMacros as $key => $macro)
		{
			if ($forum)
			{
				$adminMacros[$key]['content'] = str_replace("{forumname}", $forum['title'], $macro['content']);
			}
		}

		if ($userId)
		{
			$this->_params['macros'] = $macrosModel->prepareArrayForDropDown($this, $userMacros, $adminMacros);

			$show = !$macrosModel->hiddenOnThreadCreateReply($userId);

			$this->_params['canViewMacros'] = ($macrosModel->canViewMacros($visitor) && $show && $forum['allow_macros']);
			XenForo_CodeEvent::fire('liam_macros_ready', array(
				&$this->_params['macros'],
				&$this->_params['canViewMacros'],
				null,
				$forum
			));
		}

		parent::renderHtml();
	}

}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ViewPublic_Thread_Create extends XenForo_ViewPublic_Thread_Create
	{
	}
}