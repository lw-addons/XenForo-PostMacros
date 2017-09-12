<?php

class LiamW_Macros_Extend_ControllerPublic_Account extends XFCP_LiamW_Macros_Extend_ControllerPublic_Account
{
	public function actionPreferencesSave()
	{
		$macroOptions = $this->_input->filter(array(
			'macros_hide_qr' => XenForo_Input::BOOLEAN,
			'macros_hide_ntnr' => XenForo_Input::BOOLEAN,
			'macros_hide_convo_qr' => XenForo_Input::BOOLEAN,
			'macros_hide_convo_ncnr' => XenForo_Input::BOOLEAN
		));

		XenForo_Application::set('liamMacros_userData', $macroOptions);

		return parent::actionPreferencesSave();
	}
}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ControllerPublic_Account extends XenForo_ControllerPublic_Account
	{
	}
}