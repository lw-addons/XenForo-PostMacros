<?php

class LiamW_Macros_Extend_ControllerPublic_Thread extends XFCP_LiamW_Macros_Extend_ControllerPublic_Thread
{
	public function actionAddReply()
	{
		$response = parent::actionAddReply();

		$visitor = XenForo_Visitor::getInstance();

		if (!($response instanceof XenForo_ControllerResponse_Error) && $prefixId = $this->_input->filterSingle('apply_prefix',
					XenForo_Input::UINT) && ($visitor['is_staff'] || $visitor['is_moderator'] || $visitor['is_admin'])
		)
		{
			$threadId = $this->_input->filterSingle('thread_id', XenForo_Input::UINT);

			$dw = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread');
			$dw->setExistingData($threadId);
			$dw->set('prefix_id', $prefixId);
			$dw->save();
		}

		return $response;
	}
}

if (false)
{
	class XFCP_LiamW_Macros_Extend_ControllerPublic_Thread extends XenForo_ControllerPublic_Thread
	{
	}
}