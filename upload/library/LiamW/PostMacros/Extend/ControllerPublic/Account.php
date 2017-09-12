<?php

class LiamW_PostMacros_Extend_ControllerPublic_Account extends XFCP_LiamW_PostMacros_Extend_ControllerPublic_Account
{
	public function actionPreferences()
	{
		$response = parent::actionPreferences();

		if ($response instanceof XenForo_ControllerResponse_View)
		{
			$response->subView->params['canUseMacros'] = $this->_canUseMacros();
		}

		return $response;
	}

	public function actionPreferencesSave()
	{
		if ($this->_canUseMacros())
		{
			$macrosOptions = $this->_input->filter(array(
				'post_macros_hide_new_thread_reply' => XenForo_Input::BOOLEAN,
				'post_macros_hide_thread_quick_reply' => XenForo_Input::BOOLEAN,
				'post_macros_hide_new_conversation_reply' => XenForo_Input::BOOLEAN,
				'post_macros_hide_conversation_quick_reply' => XenForo_Input::BOOLEAN
			));

			XenForo_Application::set('liam_postMacros_options', $macrosOptions);
		}

		return parent::actionPreferencesSave();
	}

	protected function _canUseMacros()
	{
		return XenForo_Visitor::getInstance()->hasPermission('liam_postMacros', 'liamMacros_canUseMacros');
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ControllerPublic_Account extends XenForo_ControllerPublic_Account
	{
	}
}