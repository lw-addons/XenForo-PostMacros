<?php

class LiamW_Macros_ControllerAdmin_Macros extends XenForo_ControllerAdmin_Abstract
{

	public function actionIndex()
	{
		$macros = $this->_getModel()->getAdminMacros();
		
		$viewparams = array(
			'macros' => $macros,
			'totalMacros' => count($macros)
		);
		
		return $this->responseView('', 'liammacros_list', $viewparams);
	}

	public function actionNew()
	{
		$usergroupModel = $this->getModelFromCache('XenForo_Model_UserGroup');
		
		$usergroups = $usergroupModel->getAllUserGroupTitles();
		
		$usergroupsDrop = array();
		
		foreach ($usergroups as $key => $group)
		{
			$usergroupsDrop[] = array(
				'label' => $group,
				'value' => $key
			);
		}
		
		$viewparams = array(
			'usergroups' => $usergroupsDrop
		);
		
		return $this->responseView('', 'liammacros_modify', $viewparams);
	}

	public function actionEdit()
	{
		$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);
		$macro = $this->_getModel()->getMacroFromId($macroId, true);
		
		if (! $macro)
		{
			return $this->responseError(new XenForo_Phrase('liam_macros_not_found'), 404);
		}
		
		$usergroupModel = $this->getModelFromCache('XenForo_Model_UserGroup');
		
		$usergroups = $usergroupModel->getAllUserGroupTitles();
		
		$usergroupsDrop = array();
		
		foreach ($usergroups as $key => $group)
		{
			$usergroupsDrop[] = array(
				'label' => $group,
				'value' => $key,
				'selected' => in_array($key, $macro['usergroups'])
			);
		}
		
		$viewparams = array(
			'macro' => $macro,
			'usergroups' => $usergroupsDrop,
			'content' => $macro['content']
		);
		
		return $this->responseView('', 'liammacros_modify', $viewparams);
	}

	public function actionSave()
	{
		$content = $this->_input->filter(array(
			'name' => XenForo_Input::STRING,
			'content' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING
		));
		
		$dw = XenForo_DataWriter::create('LiamW_Macros_DataWriter_AdminMacros');
		
		$usergroups = $this->_input->filterSingle('usergroups', XenForo_Input::UINT, array(
			'array' => true
		));
		
		$content['usergroups'] = implode(',', $usergroups);
		
		if ($id = $this->_input->filterSingle('macro_id', XenForo_Input::UINT))
		{
			$dw->setExistingData($id, true);
		}
		
		$dw->bulkSet($content);
		$dw->save();
		
		$data = $dw->getMergedData();
		$url = XenForo_Link::buildAdminLink('macros') . $this->getLastHash($data['macro_id']);
		
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $url);
	}

	public function actionDelete()
	{
		$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);
		
		$dw = XenForo_DataWriter::create('LiamW_Macros_DataWriter_AdminMacros');
		$dw->setExistingData($macroId, true);
		$dw->delete();
		
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('macros'));
	}

	/**
	 * Gets the model
	 *
	 * @return LiamW_Macros_Model_Macros
	 */
	protected function _getModel()
	{
		return $this->getModelFromCache('LiamW_Macros_Model_Macros');
	}

}