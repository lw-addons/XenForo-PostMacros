<?php

class LiamW_Macros_ControllerAdmin_Controller extends XenForo_ControllerAdmin_Abstract
{

	public function actionIndex()
	{
		$macros = $this->_getModel()->getAdminMacros();
		$viewparams = array(
			
			'macros' => $macros,
			'totalMacros' => count($macros)
		);
		
		return $this->responseView('XenForo_ViewAdmin_Base', 'liammacros_list', $viewparams);
	}

	public function actionNew()
	{
		$ugmodel = $this->getModelFromCache('XenForo_Model_UserGroup');
		
		$groups = $ugmodel->getAllUserGroupTitles();
		
		$usergroups = array();
		
		foreach ($groups as $key => $group)
		{
			$usergroups[] = array(
				
				'label' => $group,
				'value' => $key
			);
		}
		
		$viewparams = array(
			
			'usergroups' => $usergroups
		);
		
		return $this->responseView('XenForo_ViewAdmin_Base', 'liammacros_modify', $viewparams);
	}

	public function actionEdit()
	{
		if (! $this->_input->filterSingle('id', XenForo_Input::UINT))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('macros/new'));
		}
		
		$macro = $this->_getModel()->getMacroFromId($this->_input->filterSingle('id', XenForo_Input::UINT), true);
		
		$ugmodel = $this->getModelFromCache('XenForo_Model_UserGroup');
		
		$groups = $ugmodel->getAllUserGroupTitles();
		
		$usergroups = array();
		
		foreach ($groups as $key => $group)
		{
			$usergroups[] = array(
				
				'label' => $group,
				'value' => $key,
				'selected' => in_array($key, $macro['usergroups'])
			);
		}
		
		$viewparams = array(
			
			'macro' => $macro,
			'usergroups' => $usergroups,
			'content' => $macro['content']
		);
		
		return $this->responseView('XenForo_ViewAdmin_Base', 'liammacros_modify', $viewparams);
	}

	public function actionSave()
	{
		$dw = XenForo_DataWriter::create('LiamW_Macros_DataWriter_AdminDataWriter');
		
		$content = $this->_input->filter(array(
			'name' => XenForo_Input::STRING,
			'content' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING
		));
		
		$usergroups = $this->_input->filterSingle('usergroups', XenForo_Input::UINT, array(
			'array' => true
		));
		
		$content['usergroups'] = implode(',', $usergroups);
		
		if ($id = $this->_input->filterSingle('id', XenForo_Input::UINT))
		{
			$dw->setExistingData($id, true);
		}
		
		$dw->bulkSet($content);
		$dw->save();
		
		return ($id) ? $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED, XenForo_Link::buildAdminLink('macros/#' . $id)) : $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CREATED, XenForo_Link::buildAdminLink('macros/#' . $id));
	}

	public function actionDelete()
	{
		$dw = XenForo_DataWriter::create('LiamW_Macros_DataWriter_AdminDataWriter');
		
		$id = $this->_input->filterSingle('id', XenForo_Input::UINT);
		
		$dw->setExistingData($id, true);
		
		$dw->delete();
		
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('macros'));
	}

	/**
	 * Gets the model
	 *
	 * @return LiamW_Macros_Model_Macros
	 */
	public function _getModel()
	{
		return $this->getModelFromCache('LiamW_Macros_Model_Macros');
	}

}