<?php

class LiamW_Macros_Extend_ControllerAdmin_Forum extends XFCP_LiamW_Macros_Extend_ControllerAdmin_Forum
{

	public function actionEdit()
	{
		$response = parent::actionEdit();

		if ($response instanceof XenForo_ControllerResponse_View)
		{
			$params = $response->params;

			if (!isset($params['forum']['allow_macros']))
			{
				$params['forum']['allow_macros'] = true;
			}

			$response->params = $params;
		}

		return $response;
	}

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