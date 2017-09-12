<?php

class LiamW_PostMacros_Extend_ControllerPublic_Thread extends XFCP_LiamW_PostMacros_Extend_ControllerPublic_Thread
{
	public function actionAddReply()
	{
		if (XenForo_Visitor::getInstance()->hasPermission('forum', 'editAnyPost'))
		{
			XenForo_Application::set('liam_postMacros_set_prefix',
				$this->_input->filterSingle('set_prefix', XenForo_Input::UINT));
		}

		return parent::actionAddReply();
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ControllerPublic_Thread extends XenForo_ControllerPublic_Thread
	{
	}
}