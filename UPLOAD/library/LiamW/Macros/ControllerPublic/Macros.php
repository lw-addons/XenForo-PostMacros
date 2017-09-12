<?php

/**
 * Macros controller.
 * @author  Liam W
 * @package Post Macros
 *
 */
class LiamW_Macros_ControllerPublic_Macros extends XenForo_ControllerPublic_Abstract
{
	protected function _preDispatch($action)
	{
		$this->_assertRegistrationRequired();
		$this->_assertCanViewMacros();
	}

	public function actionIndex()
	{
		$visitor = XenForo_Visitor::getInstance();

		$macrosModel = $this->_getMacrosModel();

		$macros = $macrosModel->getMacrosForUser(XenForo_Visitor::getUserId(),
			$visitor->hasPermission('macro_permissions', 'use_staff_macros'));
		$adminMacros = $macrosModel
			->getAdminMacrosForUser($this->_getUserOrError(XenForo_Visitor::getUserId()));

		foreach ($macros as $key => $macro)
		{
			// is a staff macro and no permission to edit all staff macros and it's not their macro
			if ($macro['user_id'] != $visitor->getUserId() && $this->isStaffMacro($macro) && !$visitor->hasPermission('macro_permissions',
					'can_edit_all_staff_macros')
			)
			{
				$macros[$key]['canEdit'] = false;
			}
			else
			{
				$macros[$key]['canEdit'] = true;
			}

			if ($this->isStaffMacro($macro) && $macro['user_id'] != $visitor->getUserId() && !$visitor->hasPermission('macro_permissions',
					'can_delete_staff_macros')
			)
			{
				$macros[$key]['canDelete'] = false;
			}
			else
			{
				$macros[$key]['canDelete'] = true;
			}

			$user = $this->_getUserModel()->getUserById($macro['user_id']);

			$macros[$key]['username'] = $user['username'];
		}

		$viewParams = array(
			'macros' => $macros,
			'adminMacros' => $adminMacros,
			'canUseMacros' => $this->_getMacrosModel()->canAddMacro($visitor)
		);

		return $this->responseView('LiamW_Macros_ViewPublic_View', 'macros', $viewParams);
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		/* @var $macrosDW LiamW_Macros_DataWriter_Macros */
		$macrosDW = XenForo_DataWriter::create('LiamW_Macros_DataWriter_Macros');

		$userId = XenForo_Visitor::getUserId();

		$input = $this->_input->filter(array(
			'name' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING,
		));

		$input['content'] = $this->getHelper('Editor')->getMessageText('content', $this->_input);
		$input['content'] = XenForo_Helper_String::autoLinkBbCode($input['content'], false);

		$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);

		/* Check if we're editing an existing macro */
		if ($macroId)
		{
			$existingMacro = $this->_getMacrosModel()->getMacroFromId($macroId);
			$this->_assertCanEditMacro($existingMacro);
			$macrosDW->setExistingData($existingMacro, true);
			$input['staff_macro'] = ($userId == $existingMacro['user_id']) ? $this->_input->filterSingle('staff_macro',
				XenForo_Input::BOOLEAN) : $existingMacro['staff_macro'];
		}
		else
		{
			$macrosDW->set('user_id', $userId);
			$input['staff_macro'] = $this->_input->filterSingle('staff_macro', XenForo_Input::BOOLEAN);
		}

		$macrosDW->bulkSet($input);
		$macrosDW->save();

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('macros'));
	}

	public function actionNew()
	{
		$visitor = XenForo_Visitor::getInstance();

		if (!$this->_getMacrosModel()->canAddMacro($visitor))
		{
			return $this->responseError(new XenForo_Phrase('macro_cant_add'));
		}

		$visitor = XenForo_Visitor::getInstance();

		$addStaffMacro = $visitor->hasPermission('macro_permissions', 'can_create_staff_macros');

		$viewParams = array(
			'addStaffMacro' => $addStaffMacro
		);

		return $this->responseView('LiamW_Macros_ViewPublic_Edit', 'macros_modify', $viewParams);
	}

	public function actionEdit()
	{
		$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);

		$macro = $this->_getMacrosModel()->getMacroFromId($macroId);

		if (!$macro)
		{
			return $this->responseError(new XenForo_Phrase('the_requested_macro_could_not_be_found'), 404);
		}

		$visitor = XenForo_Visitor::getInstance();

		$this->_assertCanEditMacro($macro);

		$addStaffMacro = $visitor->hasPermission('macro_permissions', 'can_create_staff_macros');
		$disableStaffMacro = ($visitor->getUserId() == $macro['user_id']) ? 0 : 1;

		$viewParams = array(
			'macro' => $macro,
			'disableStaffMacro' => $disableStaffMacro,
			'addStaffMacro' => $addStaffMacro
		);

		return $this->responseView('LiamW_Macros_ViewPublic_Edit', 'macros_modify', $viewParams);
	}

	public function actionDelete()
	{
		$macroId = $this->_input->filterSingle('macro_id', XenForo_Input::UINT);
		$macro = $this->_getMacrosModel()->getMacroFromId($macroId);

		$this->_assertCanViewMacros();

		if ($macro['user_id'] != XenForo_Visitor::getUserId() && !XenForo_Visitor::getInstance()
				->hasPermission('macro_permissions', 'can_delete_staff_macros')
		)
		{
			return $this->responseError(new XenForo_Phrase("postmacros_only_delete_own"));
		}

		if (!$this->isConfirmedPost())
		{
			return $this->responseView('XenForo_ViewPublic_View', 'macros_confirm_delete', array(
				'macro' => $macro
			));
		}
		else
		{
			$macroDW = XenForo_DataWriter::create('LiamW_Macros_DataWriter_Macros');
			$macroDW->setExistingData($macroId, true);
			$macroDW->delete();

			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect());
		}
	}

	protected function _assertCanViewMacros()
	{
		if (!XenForo_Visitor::getInstance()->hasPermission('macro_permissions', 'can_use_macros'))
		{
			throw $this->getNoPermissionResponseException();
		}
	}

	protected function _assertCanEditMacro(array $macro)
	{
		$this->_assertCanViewMacros();

		$visitor = XenForo_Visitor::getInstance();

		if (($this->isStaffMacro($macro) && $visitor->getUserId() != $macro['user_id'] && !$visitor->hasPermission('macro_permissions',
					'can_edit_all_staff_macros')) || (!$this->isStaffMacro($macro) && $visitor->getUserId() != $macro['user_id'])
		)
		{
			throw $this->getNoPermissionResponseException();
		}
	}

	/**
	 * Gets the specified user or throws an exception.
	 *
	 * @param string $id
	 *
	 * @return array
	 */
	protected function _getUserOrError($id)
	{
		$userModel = $this->_getUserModel();

		return $this->getRecordOrError($id, $userModel, 'getFullUserById', 'requested_user_not_found');
	}

	public static function getSessionActivityDetailsForList(array $activities)
	{
		return new XenForo_Phrase('macros_managing_macros');
	}

	/**
	 * Get the macros model.
	 *
	 * @return LiamW_Macros_Model_Macros
	 */
	protected function _getMacrosModel()
	{
		return $this->getModelFromCache('LiamW_Macros_Model_Macros');
	}

	/**
	 * Get XenForo user model.
	 *
	 * @return XenForo_Model_User
	 */
	protected function _getUserModel()
	{
		return $this->getModelFromCache('XenForo_Model_User');
	}

	protected function isStaffMacro($macro)
	{
		return $macro['staff_macro'];
	}
}