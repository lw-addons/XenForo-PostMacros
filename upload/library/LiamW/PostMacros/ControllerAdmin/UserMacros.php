<?php

class LiamW_PostMacros_ControllerAdmin_UserMacros extends LiamW_PostMacros_ControllerAdmin_Abstract
{
	public function actionIndex()
	{
		$this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('post-macros/user'));

		$filterName = $this->_input->filterSingle('username', XenForo_Input::STRING);

		$page = $this->_input->filterSingle('page', XenForo_Input::UINT);

		$perPage = $this->_getMacrosPerPage();

		$conditions = array();

		if ($filterName)
		{
			$conditions['username'] = $filterName;
		}

		$macros = $this->_getMacrosModel()
			->getMacros($conditions, array(
				'join' => LiamW_PostMacros_Model_Macros::FETCH_USER,
				'page' => $page,
				'perPage' => $perPage
			));

		$viewParams = array(
			'macros' => $macros,
			'filterName' => $filterName,
			'totalMacros' => $this->_getMacrosModel()
				->countMacros($conditions),
			'perPage' => $perPage,
			'page' => $page
		);

		return $this->responseView('LiamW_PostMacros_ViewAdmin_Index', 'postMacros_user_index', $viewParams);
	}

	public function actionNew()
	{
		$this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('post-macros/user/new'));

		return $this->_getMacroAddEditResponse();
	}

	public function actionEdit()
	{
		$macro = $this->_getMacroOrError();

		$this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('post-macros/user/edit', $macro));

		return $this->_getMacroAddEditResponse($macro);
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$username = $this->_input->filterSingle('username', XenForo_Input::STRING);

		$user = XenForo_Model::create('XenForo_Model_User')->getUserByName($username);

		if (!$user)
		{
			return $this->responseError(new XenForo_Phrase('requested_user_not_found'));
		}

		$data = $this->_input->filter(array(
			'title' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING,
			'content' => XenForo_Input::STRING,
			'thread_prefix' => XenForo_Input::UINT,
			'lock_thread' => XenForo_Input::BOOLEAN,
			'staff_macro' => XenForo_Input::BOOLEAN
		));

		$dw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_Macros');
		if ($macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT))
		{
			$dw->setExistingData($macroId);
		}

		$dw->set('user_id', $user['user_id']);
		$dw->bulkSet($data);
		$dw->save();

		$lastHash = $this->getLastHash($dw->get('macro_id'));

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('post-macros/user') . $lastHash);
	}

	public function actionDelete()
	{
		$macro = $this->_getMacroOrError();

		if ($this->isConfirmedPost())
		{
			$dw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_Macros');
			$dw->setExistingData($macro, true);
			$dw->delete();

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('post-macros/user'));
		}
		else
		{
			$viewParams = array(
				'macro' => $macro
			);

			return $this->responseView('LiamW_PostMacros_ViewAdmin_User_Delete', 'postMacros_user_delete_confirm',
				$viewParams);
		}
	}

	protected function _getMacroOrError($macroId = null)
	{
		if (!$macroId)
		{
			$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);
		}

		$macro = $this->_getMacrosModel()
			->getMacroById($macroId, array('join' => LiamW_PostMacros_Model_Macros::FETCH_USER));

		if (!$macro)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('liam_postMacros_requested_macro_not_found'),
				404));
		}

		return $macro;
	}

	protected function _getMacroAddEditResponse($macro = null)
	{
		/** @var XenForo_Model_ThreadPrefix $threadPrefixModel */
		$threadPrefixModel = XenForo_Model::create('XenForo_Model_ThreadPrefix');

		$viewParams = array(
			'macro' => $macro,
			'threadPrefixes' => $threadPrefixModel->getPrefixOptions()
		);

		if ($macro)
		{
			return $this->responseView('LiamW_PostMacros_ViewAdmin_User_Edit', 'postMacros_user_edit', $viewParams);
		}
		else
		{
			return $this->responseView('LiamW_PostMacros_ViewAdmin_User_Create', 'postMacros_user_edit', $viewParams);
		}
	}
}