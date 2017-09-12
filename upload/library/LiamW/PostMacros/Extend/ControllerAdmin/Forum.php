<?php

class LiamW_PostMacros_Extend_ControllerAdmin_Forum extends XFCP_LiamW_PostMacros_Extend_ControllerAdmin_Forum
{
	public function actionSave()
	{
		XenForo_Application::set('liam_postMacros_forum', $this->_input->filter(array(
			'post_macros_enable' => XenForo_Input::BOOLEAN
		)));

		return parent::actionSave();
	}

	public function actionEdit()
	{
		$response = parent::actionEdit();

		if ($response instanceof XenForo_ControllerResponse_View && !isset($response->params['forum']['node_id']))
		{
			$response->params['forum']['post_macros_enable'] = 1;
		}

		return $response;
	}


}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ControllerAdmin_Forum extends XenForo_ControllerAdmin_Forum
	{
	}
}