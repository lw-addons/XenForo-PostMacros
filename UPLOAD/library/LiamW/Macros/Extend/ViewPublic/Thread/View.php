<?php

class LiamW_Macros_Extend_ViewPublic_Thread_View extends XFCP_LiamW_Macros_Extend_ViewPublic_Thread_View
{

	public function renderHtml()
	{
		$visitor = XenForo_Visitor::getInstance();

		/* Get current userId */
		$userId = $visitor->getUserId();

		/* @var $model LiamW_Macros_Model_Macros */
		$model = XenForo_Model::create('LiamW_Macros_Model_Macros');

		/* @var $usermode XenForo_Model_User */
		$userModel = XenForo_Model::create('XenForo_Model_User');

		/* Get user macros */
		$userMacros = $model->getMacrosForUser($userId,
			XenForo_Visitor::getInstance()->hasPermission('macro_permissions', 'use_staff_macros'));

		/* and admin ones... */
		$adminMacros = $model->getAdminMacrosForUser($userModel->getFullUserById($userId), true);

		$forum = @$this->_params['forum'];
		$thread = @$this->_params['thread'];

		foreach ($userMacros as &$macro)
		{
			if ($thread && $forum)
			{
				$macro['macro'] = $this->compileVariables($macro['macro'], $thread, $forum);
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
			$this->_params['macros'] = $model->prepareArrayForDropDown($this, $userMacros, $adminMacros);

			switch ($type)
			{
				case "qr":
					$show = !($model->hiddenOnQr($userId));
					break;
				case "ntnr":
					$show = !($model->hiddenOnNtNr($userId));
					break;
				case "covoqr":
					$show = !($model->hiddenOnConvoQr($userId));
					break;
				case "convoncnr":
					$show = !($model->hiddenOnConvoNcNr($userId));
			}

			if (!$forum)
			{
				$forum = array();
				$forum['allow_macros'] = true;
			}

			$this->_params['canviewmacros'] = ($model->canViewMacros($visitor) && $show && $forum['allow_macros']);
			XenForo_CodeEvent::fire('liammacros_macro_ready', array(
				&$this->_params['macros'],
				&$this->_params['canviewmacros'],
				$thread,
				$forum,
				$type
			));
		}

		return parent::renderHtml();
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

//class XFCP_LiamW_Macros_ViewPublic_Thread_View extends XenForo_ViewPublic_Thread_View {}