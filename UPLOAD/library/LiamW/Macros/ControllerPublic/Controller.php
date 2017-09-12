<?php

/**
 * Macros controller.
 * @author Liam W
 * @package Post Macros
 *
 */
class LiamW_Macros_ControllerPublic_Controller extends XenForo_ControllerPublic_Abstract
{

	/**
	 * (non-PHPdoc)
	 *
	 * @see XenForo_Controller::_preDispatch()
	 */
	protected function _preDispatch($action)
	{
		/* Make sure the user is registered */
		$this->_assertRegistrationRequired();
		$this->_assertIpNotBanned();
		$this->_assertNotBanned();
	}

	/**
	 * Index action.
	 * Display existing macros.
	 *
	 * @return Ambigous <XenForo_ControllerResponse_Error, XenForo_ControllerResponse_Reroute>|XenForo_ControllerResponse_View
	 */
	public function actionIndex()
	{
		$visitor = XenForo_Visitor::getInstance();
		
		if (! $visitor->hasPermission('macro_permissions', 'can_use_macros'))
		{
			return $this->responseNoPermission();
		}
		
		$macros = $this->_getMacrosModel()->getMacrosForUser(XenForo_Visitor::getUserId(), $visitor->hasPermission('macro_permissions', 'use_staff_macros'));
		$adminmacros = $this->_getMacrosModel()->getAdminMacrosForUser($this->_getUserOrError(XenForo_Visitor::getUserId()));
		
		foreach ($macros as $key => $macro)
		{
			// is a staff macro and no permission to edit all staff macros and it's not their macro
			if ($macro['userid'] != $visitor->getUserId() && $this->isStaffMacro($macro) && ! $visitor->hasPermission('macro_permissions', 'can_edit_all_staff_macros'))
			{
				$macros[$key]['canedit'] = false;
			}
			else
			{
				$macros[$key]['canedit'] = true;
			}
			
			if ($this->isStaffMacro($macro) && $macro['userid'] != $visitor->getUserId() && ! $visitor->hasPermission('macro_permissions', 'can_delete_staff_macros'))
			{
				$macros[$key]['candelete'] = false;
			}
			else
			{
				$macros[$key]['candelete'] = true;
			}
			
			$user = XenForo_Model::create('XenForo_Model_User')->getUserById($macro['userid']);
			
			$macros[$key]['username'] = $user['username'];
		}
		
		$viewParams = array(
			
			'macros' => $macros,
			'adminmacros' => $adminmacros,
			'canUseMacros' => $this->_getMacrosModel()->canAddMacro($visitor)
		);
		
		return $this->responseView('LiamW_Macros_ViewPublic_View', 'macros', $viewParams);
	}

	/**
	 * Write the macros to the database.
	 */
	public function actionWrite()
	{
		$this->_assertPostOnly();
		
		/* @var $macrosDW LiamW_Macros_DataWriter_DataWriter */
		$macrosDW = XenForo_DataWriter::create('LiamW_Macros_DataWriter_DataWriter');
		
		$visitor = XenForo_Visitor::getInstance();
		
		/* Check the visitor has permission to use macros */
		if (! $visitor->hasPermission('macro_permissions', 'can_use_macros'))
		{
			return $this->responseNoPermission();
		}
		
		/* Gather data */
		$userid = XenForo_Visitor::getUserId();
		
		$input = $this->_input->filter(array(
			'macroid' => XenForo_Input::UINT,
			'name' => XenForo_Input::STRING,
			'thread_title' => XenForo_Input::STRING
		));
		
		$input['macro'] = $this->getHelper('Editor')->getMessageText('macro', $this->_input);
		$input['macro'] = XenForo_Helper_String::autoLinkBbCode($input['macro'], false);
		
		/* Check if we're editing an existing macro */
		if ($input['macroid'])
		{
			// existing macro being edited
			$existingmacro = $this->_getMacrosModel()->getMacroFromId($input['macroid']);
			$macrosDW->setExistingData($input['macroid']);
			$input['staff_macro'] = ($userid == $existingmacro['userid']) ? $this->_input->filterSingle('staff_macro', XenForo_Input::BOOLEAN) : $existingmacro['staff_macro'];
		}
		else
		{
			// new macro
			$macrosDW->set('userid', $userid);
			$input['staff_macro'] = $this->_input->filterSingle('staff_macro', XenForo_Input::BOOLEAN);
		}
		
		/* Set data */
		$macrosDW->bulkSet($input);
		
		/* Save macro */
		$macrosDW->save();
		
		/* Redirect to macros page */
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('macros'));
	}

	/**
	 * New Macro
	 */
	public function actionNew()
	{
		$visitor = XenForo_Visitor::getInstance();
		
		if (! $this->_getMacrosModel()->canAddMacro($visitor))
		{
			return $this->responseError(new XenForo_Phrase('macro_cant_add'));
		}
		
		$visitor = XenForo_Visitor::getInstance();
		
		$viewsm = $visitor->hasPermission('macro_permissions', 'can_create_staff_macros');
		
		$viewparams = array(
			
			'viewsm' => $viewsm
		);
		
		return $this->responseView('LiamW_Macros_ViewPublic_View', 'macros_modify', $viewparams);
	}

	/**
	 * Edit macro.
	 */
	public function actionEdit()
	{
		if (! $macroId = $this->_input->filterSingle('macroid', XenForo_Input::UINT))
		{
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildPublicLink('full:macros/new'));
		}
		
		$visitor = XenForo_Visitor::getInstance();
		
		$viewsm = $visitor->hasPermission('macro_permissions', 'can_create_staff_macros');
		
		$macro = $this->_getMacrosModel()->getMacroFromId($macroId);
		
		if (! $macro)
		{
			return $this->responseError(new XenForo_Phrase('macro_doesnt_exist'));
		}
		
		/* Staff Macro Disable */
		$smdis = ($visitor->getUserId() == $macro['userid']) ? 0 : 1;
		
		if ($this->isStaffMacro($macro) && ! $visitor->hasPermission('macro_permissions', 'can_edit_all_staff_macros') && $smdis)
		{
			return $this->responseNoPermission();
		}
		
		$viewparams = array(
			
			'macroid' => $macroId,
			'macroname' => $macro['name'],
			'macrotext' => $macro['macro'],
			'macroarray' => $macro,
			'viewsm' => $viewsm,
			'staffmacro' => $macro['staff_macro'],
			'smdis' => $smdis
		);
		
		return $this->responseView('LiamW_Macros_ViewPublic_View', 'macros_modify', $viewparams);
	}

	/**
	 * Delete macro.
	 */
	public function actionDelete()
	{
		$macroId = $this->_input->filterSingle('macroid', XenForo_Input::UINT);
		$macro = $this->_getMacrosModel()->getMacroFromId($macroId);
		
		if ($macro['userid'] != XenForo_Visitor::getUserId() && ! XenForo_Visitor::getInstance()->hasPermission('macro_permissions', 'can_delete_staff_macros'))
		{
			return $this->responseError(new XenForo_Phrase("postmacros_only_delete_own"));
		}
		
		if (! $this->isConfirmedPost())
		{
			return $this->responseView('XenForo_ViewPublic_View', 'macros_confirm_delete', array(
				
				'macro' => $macro
			));
		}
		else
		{
			$macroDW = XenForo_DataWriter::create('LiamW_Macros_DataWriter_DataWriter');
			$macroDW->setExistingData($macroId, true);
			$macroDW->delete();
			
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect());
		}
	}

	/**
	 * Gets macro and adds data to redirect javascript.
	 *
	 * @deprecated No longer needed. Cannot be extended.
	 * @return XenForo_ControllerResponse_Error
	 */
	final public function actionUseMacro()
	{
		// no longer used. If this has been called, something is wrong.
		return $this->responseError(new XenForo_Phrase("postmacros_usemacro_gone"), 410);
	}

	/**
	 * Action for macro options.
	 *
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionOptions()
	{
		$model = $this->_getMacrosModel();
		$userid = XenForo_Visitor::getUserId();
		$viewParams = array(
			
			'macros_hide_qr' => $model->hiddenOnQr($userid),
			'macros_hide_ntnr' => $model->hiddenOnNtNr($userid),
			'macros_hide_convo_qr' => $model->hiddenOnConvoQr($userid),
			'macros_hide_convo_ncnr' => $model->hiddenOnConvoNcNr($userid),
			'existing' => $model->optionsSaved($userid)
		);
		
		return $this->responseView('', 'macros_options', $viewParams);
	}

	/**
	 * Action for saving macro options.
	 *
	 * @return XenForo_ControllerResponse_Redirect
	 */
	public function actionSaveOptions()
	{
		$this->_assertPostOnly();
		
		$input = $this->_input->filter(array(
			
			'macros_hide_qr' => XenForo_Input::BOOLEAN,
			'macros_hide_ntnr' => XenForo_Input::BOOLEAN,
			'macros_hide_convo_qr' => XenForo_Input::BOOLEAN,
			'macros_hide_convo_ncnr' => XenForo_Input::BOOLEAN
		));
		
		/* @var $dw LiamW_Macros_DataWriter_MacrosOptions */
		$dw = XenForo_DataWriter::create('LiamW_Macros_DataWriter_MacrosOptions');
		
		if ($this->_input->filterSingle('existing', XenForo_Input::UINT))
		{
			$dw->setExistingData(XenForo_Visitor::getUserId(), true);
		}
		else
		{
			$dw->set('userid', XenForo_Visitor::getUserId());
		}
		
		$dw->bulkSet($input);
		$dw->save();
		
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect());
	}

	/**
	 * Gets the specified user or throws an exception.
	 *
	 * @param string $id        	
	 *
	 * @return array
	 */
	public function _getUserOrError($id)
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