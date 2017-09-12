<?php

class LiamW_Macros_Extend_ControllerAdmin_Forum extends XFCP_LiamW_Macros_Extend_ControllerAdmin_Forum
{
	public function actionSave()
	{
		XenForo_Application::set('allow_macros_forum',
			$this->_input->filterSingle('allow_macros', XenForo_Input::BOOLEAN));

		return parent::actionSave();
	}
}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ControllerAdmin_Forum extends XenForo_ControllerAdmin_Forum
	{

	}
}