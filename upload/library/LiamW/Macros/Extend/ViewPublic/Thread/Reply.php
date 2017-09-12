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

		$forum = $this->_params['forum'];
		$thread = $this->_params['thread'];

		foreach ($userMacros as $key => $macro)
		{
			if ($thread && $forum)
			{
				$userMacros[$key]['content'] = $this->compileVariables($macro['content'], $thread, $forum);
			}
		}

		foreach ($adminMacros as $key => $macro)
		{
			if ($thread && $forum)
			{
				$adminMacros[$key]['content'] = $this->compileVariables($macro['content'], $thread, $forum);
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
				$thread,
				$forum
			));
		}

		parent::renderHtml();
	}

	private function compileVariables($macro, array $thread, array $forum)
	{
		$threadUser = $thread['username'];
		$threadName = $thread['title'];
		$forumName = $forum['title'];

		return str_replace(array(
			"{threaduser}",
			"{threadname}",
			"{forumname}"
		), array(
			$threadUser,
			$threadName,
			$forumName
		), $macro);
	}

}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ViewPublic_Thread_Reply extends XenForo_ViewPublic_Thread_Reply
	{
	}
}