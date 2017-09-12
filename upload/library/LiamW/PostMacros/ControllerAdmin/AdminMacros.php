<?php

class LiamW_PostMacros_ControllerAdmin_AdminMacros extends XenForo_ControllerAdmin_Abstract
{
	public function actionIndex()
	{
		$this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('post-macros'));

		$macros = $this->_getMacrosModel()->getAdminMacrosForDisplay();

		$viewParams = array(
			'macros' => $macros
		);

		return $this->responseView('LiamW_PostMacros_ViewAdmin_Index', 'postMacros_index', $viewParams);
	}

	public function actionNew()
	{
		$this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('post-macros/new'));

		return $this->_getMacroAddEditResponse();
	}

	public function actionEdit()
	{
		$macro = $this->_getMacroOrError();

		$this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('post-macros/edit', $macro));

		return $this->_getMacroAddEditResponse($macro);
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$data = $this->_input->filter(array(
			'title' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING,
			'content' => XenForo_Input::STRING,
			'thread_prefix' => XenForo_Input::UINT,
			'lock_thread' => XenForo_Input::BOOLEAN,
			'authorized_usergroups' => array(
				XenForo_Input::UINT,
				'array' => true
			)
		));

		$dw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_AdminMacros');
		if ($adminMacroId = $this->_input->filterSingle('admin_macro_id', XenForo_Input::UINT))
		{
			$dw->setExistingData($adminMacroId);
		}

		$dw->bulkSet($data);
		$dw->save();

		$lastHash = $this->getLastHash($dw->get('admin_macro_id'));

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('post-macros') . $lastHash);
	}

	public function actionDelete()
	{
		$macro = $this->_getMacroOrError();

		if ($this->isConfirmedPost())
		{
			$dw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_AdminMacros');
			$dw->setExistingData($macro, true);
			$dw->delete();

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('post-macros'));
		}
		else
		{
			$viewParams = array(
				'macro' => $macro
			);

			return $this->responseView('LiamW_PostMacros_ViewAdmin_Delete', 'postMacros_delete_confirm', $viewParams);
		}
	}

	protected function _getMacroOrError($adminMacroId = null)
	{
		if (!$adminMacroId)
		{
			$adminMacroId = $this->_input->filterSingle('admin_macro_id', XenForo_Input::UINT);
		}

		$macro = $this->_getMacrosModel()->getAdminMacroById($adminMacroId);

		if (!$macro)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('liam_postMacros_requested_macro_not_found'),
				404));
		}

		return $macro;
	}

	protected function _getMacroAddEditResponse($macro = null)
	{
		$visitor = XenForo_Visitor::getInstance();

		/** @var XenForo_Model_ThreadPrefix $threadPrefixModel */
		$threadPrefixModel = XenForo_Model::create('XenForo_Model_ThreadPrefix');

		$viewParams = array(
			'macro' => $macro,
			'userGroups' => XenForo_Model::create('XenForo_Model_UserGroup')
				->getUserGroupOptions(@unserialize($macro['authorized_usergroups'])),
			'canCreateStaffMacro' => $visitor->hasPermission('liam_postMacros', 'liamMacros_createStaff'),
			'canLockThread' => $visitor->hasPermission('forum', 'lockUnlockThread'),
			'canEditThread' => $visitor->hasPermission('forum', 'editAnyPost'),
			'threadPrefixes' => $threadPrefixModel->preparePrefixes($threadPrefixModel->getAllPrefixes())
		);

		if ($macro)
		{
			return $this->responseView('LiamW_PostMacros_ViewAdmin_Edit', 'postMacros_edit', $viewParams);
		}
		else
		{
			return $this->responseView('LiamW_PostMacros_ViewAdmin_Create', 'postMacros_edit', $viewParams);
		}
	}

	/**
	 * @return LiamW_PostMacros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_PostMacros_Model_Macros');
	}
}