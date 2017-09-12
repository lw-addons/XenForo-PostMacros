<?php

class LiamW_PostMacros_Extend_ControllerPublic_Forum extends XFCP_LiamW_PostMacros_Extend_ControllerPublic_Forum
{
	public function actionAddThread()
	{
		$setPrefix = $this->_input->filterSingle('set_prefix', XenForo_Input::UINT);
		$setLocked = $this->_input->filterSingle('set_locked', xenforo_input::BOOLEAN);

		if (XenForo_Visitor::getInstance()->hasPermission('forum', 'editAnyPost'))
		{
			if ($setPrefix)
			{
				XenForo_Application::set('liam_postMacros_set_prefix', $setPrefix);
			}

			if ($setLocked)
			{
				XenForo_Application::set('liam_postMacros_set_locked', $setLocked);
			}
		}

		return parent::actionAddThread();
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ControllerPublic_Forum extends XenForo_ControllerPublic_Forum
	{
	}
}