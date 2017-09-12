<?php

class LiamW_PostMacros_ControllerPublic_Macros extends XenForo_ControllerPublic_Abstract
{
	protected function _preDispatch($action)
	{
		$this->_assertRegistrationRequired();
		$this->_assertCanUseMacros();
	}

	public function actionIndex()
	{
		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('post-macros'));

		$macros = $this->_getMacrosModel()->getMacrosForSelect();
		$macros['user'] = $this->_getMacrosModel()->prepareMacros($macros['user']);
		$macros['admin'] = $this->_getMacrosModel()->prepareAdminMacros($macros['admin']);

		$viewParams = array(
			'macros' => $macros,
			'canCreate' => $this->_getMacrosModel()->canCreateMacro()
		);

		return $this->responseView('LiamW_PostMacros_ViewPublic_Index', 'postMacros_index', $viewParams);
	}

	public function actionNew()
	{
		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('post-macros/new'));

		$this->_assertCanCreateMacro();

		return $this->_getMacroAddEditResponse();
	}

	public function actionEdit()
	{
		$macro = $this->_getMacroOrError();

		$this->canonicalizeRequestUrl(XenForo_Link::buildPublicLink('post-macros/edit', $macro));

		$this->_assertCanEditMacro($macro);

		return $this->_getMacroAddEditResponse($macro);
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$data = $this->_input->filter(array(
			'title' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING,
			'thread_prefix' => XenForo_Input::UINT,
			'lock_thread' => XenForo_Input::BOOLEAN,
			'staff_macro' => XenForo_Input::BOOLEAN
		));

		$data['content'] = $this->_getEditorHelper()->getMessageText('content', $this->_input);
		$data['content'] = XenForo_Helper_String::autoLinkBbCode($data['content']);

		$dw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_Macros');
		if ($macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT))
		{
			$dw->setExistingData($macroId);
			$this->_assertCanEditMacro($dw->getMergedExistingData());
		}
		else
		{
			$this->_assertCanCreateMacro();
			$dw->set('user_id', XenForo_Visitor::getUserId());
		}

		$visitor = XenForo_Visitor::getInstance();

		if (!XenForo_Visitor::getInstance()->hasPermission('liam_postMacros', 'liamMacros_createStaff'))
		{
			unset($data['staff_macro']);
		}

		if (!$visitor->hasPermission('forum', 'editAnyPost'))
		{
			unset($data['thread_prefix']);
		}

		if (!$visitor->hasPermission('forum', 'lockUnlockThread'))
		{
			unset($data['lock_thread']);
		}

		$dw->bulkSet($data);
		$dw->save();

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('post-macros'));
	}

	public function actionDelete()
	{
		$macro = $this->_getMacroOrError();

		$this->_assertCanDeleteMacro($macro);

		if ($this->isConfirmedPost())
		{
			$dw = XenForo_DataWriter::create('LiamW_PostMacros_DataWriter_Macros');
			$dw->setExistingData($macro, true);
			$dw->delete();

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildPublicLink('post-macros'));
		}
		else
		{
			$viewParams = array(
				'macro' => $macro
			);

			return $this->responseView('LiamW_PostMacros_ViewPublic_Delete', 'postMacros_delete_confirm', $viewParams);
		}
	}

	public function actionPreview()
	{
		$this->_assertCanCreateMacro();

		$content = $this->_getEditorHelper()->getMessageText('content', $this->_input);
		$content = XenForo_Helper_String::autoLinkBbCode($content);

		$viewParams = array(
			'content' => $content
		);

		return $this->responseView('LiamW_PostMacros_ViewPublic_Preview', 'postMacros_preview_macro', $viewParams);
	}

	public function actionUse()
	{
		$this->_assertPostOnly();

		$data = $this->_input->filter(array(
			'macro_id' => XenForo_Input::UINT,
			'render' => XenForo_Input::BOOLEAN,
			'type' => XenForo_Input::STRING,
			'formAction' => XenForo_Input::STRING
		));

		if ($data['type'] == 'user')
		{
			$macro = $this->_getMacroOrError($data['macro_id']);
		}
		else
		{
			$macro = $this->_getMacrosModel()->getAdminMacroById($data['macro_id']);

			if (!$macro)
			{
				return $this->responseError(new XenForo_Phrase('liam_postMacros_requested_macro_not_found'),
					404);
			}
		}

		$this->_assertCanUseMacro($macro, $data['type']);

		$parsedRoute = $this->parseRouteUrl(XenForo_Link::convertUriToAbsoluteUri($data['formAction'], true));

		$thread = array();
		$forum = array();

		if (isset($parsedRoute['params']['thread_id']))
		{
			$thread = XenForo_Model::create('XenForo_Model_Thread')->getThreadById($parsedRoute['params']['thread_id']);
			$forum = XenForo_Model::create('XenForo_Model_Forum')
				->getForumByThreadId($parsedRoute['params']['thread_id']);
		}
		else if (isset($parsedRoute['params']['node_id']))
		{
			$forum = XenForo_Model::create('XenForo_Model_Forum')->getForumById($parsedRoute['params']['node_id']);
		}

		if (isset($forum['node_id']))
		{
			$this->_assertMacrosEnabled($forum);
		}

		$viewParams = array(
			'macro' => $macro,
			'thread' => $thread,
			'forum' => $forum,
			'render' => $data['render']
		);

		return $this->responseView('LiamW_PostMacros_ViewPublic_Use', '', $viewParams);
	}

	protected function _getMacroOrError($macroId = null)
	{
		if (!$macroId)
		{
			$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);
		}

		$macro = $this->_getMacrosModel()->getMacroById($macroId);

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

		/** @var XenForo_Model_ThreadPrefix $prefixModel */
		$prefixModel = XenForo_Model::create('XenForo_Model_ThreadPrefix');

		$viewParams = array(
			'macro' => $macro,
			'canCreateStaffMacro' => $visitor->hasPermission('liam_postMacros', 'liamMacros_createStaff'),
			'canLockThread' => $visitor->hasPermission('forum', 'lockUnlockThread'),
			'canEditThread' => $visitor->hasPermission('forum', 'editAnyPost'),
			'threadPrefixes' => $prefixModel->preparePrefixes($prefixModel->getAllPrefixes())
		);

		if ($macro)
		{
			return $this->responseView('LiamW_PostMacros_ViewPublic_Edit', 'postMacros_edit', $viewParams);
		}
		else
		{
			return $this->responseView('LiamW_PostMacros_ViewPublic_Create', 'postMacros_edit', $viewParams);
		}
	}

	/**
	 * Asserts that the viewing user has permission to use macros, throws an exception if they don't.
	 */
	protected function _assertCanUseMacros()
	{
		if (!XenForo_Visitor::getInstance()->hasPermission('liam_postMacros', 'liamMacros_canUseMacros'))
		{
			throw $this->getNoPermissionResponseException();
		}
	}

	protected function _assertCanUseMacro(array $macro, $type = 'user')
	{
		if (!$this->_getMacrosModel()->canUseMacro($macro, $type))
		{
			throw $this->getNoPermissionResponseException();
		}
	}

	protected function _assertCanCreateMacro()
	{
		if (!$this->_getMacrosModel()->canCreateMacro($errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	}

	protected function _assertCanEditMacro(array $macro)
	{
		if (!$this->_getMacrosModel()->canEditMacro($macro, $errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	}

	protected function _assertCanDeleteMacro(array $macro)
	{
		if (!$this->_getMacrosModel()->canDeleteMacro($macro, $errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	}

	protected function _assertMacrosEnabled(array $forum)
	{
		if (!$this->_getMacrosModel()->macrosEnabledInForum($forum))
		{
			throw $this->getNoPermissionResponseException();
		}
	}

	/**
	 * @return XenForo_ControllerHelper_Editor
	 */
	protected function _getEditorHelper()
	{
		return $this->getHelper('Editor');
	}

	/**
	 * @return LiamW_PostMacros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_PostMacros_Model_Macros');
	}
}