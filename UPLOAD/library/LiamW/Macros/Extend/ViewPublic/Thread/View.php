<?php

class LiamW_Macros_Extend_ViewPublic_Thread_View extends XFCP_LiamW_Macros_Extend_ViewPublic_Thread_View
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

		$forum = null;
		$thread = null;

		if (isset($this->_params['forum']))
		{
			$forum = $this->_params['forum'];
		}

		if (isset($this->_params['thread']))
		{
			$thread = $this->_params['thread'];
		}

		foreach ($userMacros as &$macro)
		{
			if ($thread && $forum)
			{
				$macro['content'] = $this->compileVariables($macro['content'], $thread, $forum);
			}
		}

		foreach ($adminMacros as &$macro)
		{
			if ($thread && $forum)
			{
				$macro['content'] = $this->compileVariables($macro['content'], $thread, $forum);
			}
		}

		$type = XenForo_Application::get('macro_type');

		if ($userId)
		{
			$this->_params['macros'] = $macrosModel->prepareArrayForDropDown($this, $userMacros, $adminMacros);

			switch ($type)
			{
				case "qr":
					$show = !$macrosModel->hiddenOnQr($userId);
					break;
				case "ntnr":
					$show = !$macrosModel->hiddenOnNtNr($userId);
					break;
				case "covoqr":
					$show = !$macrosModel->hiddenOnConvoQr($userId);
					break;
				case "convoncnr":
					$show = !$macrosModel->hiddenOnConvoNcNr($userId);
					break;
				default:
					throw new XenForo_Exception("Invalid Show Content ($type)");
			}

			if (!$forum)
			{
				$forum = array();
				$forum['allow_macros'] = true;
			}

			$this->_params['canViewMacros'] = ($macrosModel->canViewMacros($visitor) && $show && $forum['allow_macros']);
			XenForo_CodeEvent::fire('liammacros_macro_ready', array(
				&$this->_params['macros'],
				&$this->_params['canViewMacros'],
				$thread,
				$forum,
				$type
			));
		}

		parent::renderHtml();
	}

	private function compileVariables($macro, array $thread, array $forum)
	{
		$threadUser = @$thread['username'];
		$threadName = @$thread['title'];
		$forumName = @$forum['title'];

		$macro = str_replace(array(
			"{threaduser}",
			"{threadname}",
			"{forumname}"
		), array(
			$threadUser,
			$threadName,
			$forumName
		), $macro);

		return $macro;
	}

}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ViewPublic_Thread_View extends XenForo_ViewPublic_Thread_View
	{
	}
}