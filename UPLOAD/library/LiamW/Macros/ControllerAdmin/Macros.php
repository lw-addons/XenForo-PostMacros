<?php

class LiamW_Macros_ControllerAdmin_Macros extends XenForo_ControllerAdmin_Abstract
{
	public function actionIndex()
	{
		$macros = $this->_getModel()->getAdminMacros();

		$viewParams = array(
			'macros' => $macros,
			'totalMacros' => count($macros)
		);

		return $this->responseView('', 'liammacros_list', $viewParams);
	}

	public function actionNew()
	{
		$userGroupModel = $this->getModelFromCache('XenForo_Model_UserGroup');

		$userGroups = $userGroupModel->getAllUserGroupTitles();

		$userGroupsDropDown = array();

		foreach ($userGroups as $key => $group)
		{
			$userGroupsDropDown[] = array(
				'label' => $group,
				'value' => $key
			);
		}

		/** @var XenForo_Model_ThreadPrefix $prefixModel */
		$prefixModel = XenForo_Model::create('XenForo_Model_ThreadPrefix');
		$prefixes = $prefixModel->preparePrefixes($prefixModel->getAllPrefixes());

		$viewParams = array(
			'usergroups' => $userGroupsDropDown,
			'prefixes' => $prefixes
		);

		return $this->responseView('', 'liammacros_modify', $viewParams);
	}

	public function actionEdit()
	{
		$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);
		$macro = $this->_getModel()->getMacroFromId($macroId, true);

		if (!$macro)
		{
			return $this->responseError(new XenForo_Phrase('liam_macros_not_found'), 404);
		}

		$userGroupModel = $this->getModelFromCache('XenForo_Model_UserGroup');

		$userGroups = $userGroupModel->getAllUserGroupTitles();

		$userGroupsDropDown = array();

		foreach ($userGroups as $key => $group)
		{
			$userGroupsDropDown[] = array(
				'label' => $group,
				'value' => $key,
				'selected' => in_array($key, $macro['usergroups'])
			);
		}

		/** @var XenForo_Model_ThreadPrefix $prefixModel */
		$prefixModel = XenForo_Model::create('XenForo_Model_ThreadPrefix');
		$prefixes = $prefixModel->preparePrefixes($prefixModel->getAllPrefixes());

		$viewParams = array(
			'macro' => $macro,
			'usergroups' => $userGroupsDropDown,
			'content' => $macro['content'],
			'prefixes' => $prefixes
		);

		return $this->responseView('', 'liammacros_modify', $viewParams);
	}

	public function actionSave()
	{
		$content = $this->_input->filter(array(
			'name' => XenForo_Input::STRING,
			'content' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING,
			'lock_thread' => XenForo_Input::BOOLEAN,
			'apply_prefix' => XenForo_Input::UINT
		));

		$dw = XenForo_DataWriter::create('LiamW_Macros_DataWriter_AdminMacros');

		$userGroups = $this->_input->filterSingle('usergroups', XenForo_Input::UINT, array(
			'array' => true
		));

		$content['usergroups'] = implode(',', $userGroups);

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

		if ($this->isConfirmedPost())
		{

			$dw = XenForo_DataWriter::create('LiamW_Macros_DataWriter_AdminMacros');
			$dw->setExistingData($macroId, true);
			$dw->delete();

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('macros'));
		}
		else
		{
			$macro = $this->_getModel()->getMacroFromId($macroId, true);

			$viewParams = array(
				'macro' => $macro
			);

			return $this->responseView('', 'macros_delete_confirm', $viewParams);
		}
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