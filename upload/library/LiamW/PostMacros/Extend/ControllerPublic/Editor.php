<?php

class LiamW_PostMacros_Extend_ControllerPublic_Editor extends XFCP_LiamW_PostMacros_Extend_ControllerPublic_Editor
{
	public function actionDialog()
	{
		$response = parent::actionDialog();

		if ($this->_input->filterSingle('dialog', XenForo_Input::STRING) == 'liam_postmacros')
		{
			/** @var LiamW_PostMacros_Model_Macros $macrosModel */
			$macrosModel = $this->getModelFromCache('LiamW_PostMacros_Model_Macros');

			$response->params['macros'] = $macrosModel->getMacrosForSelect();
		}

		return $response;
	}
}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ControllerPublic_Editor extends XenForo_ControllerPublic_Editor
	{
	}
}