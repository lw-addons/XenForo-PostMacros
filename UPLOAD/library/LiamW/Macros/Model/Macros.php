<?php

/**
 * Model for the Post Macros add-on.
 *
 * @author  Liam W
 * @package Post Macros
 *
 */
class LiamW_Macros_Model_Macros extends XenForo_Model
{

	/**
	 * Gets the macros for a specific user.
	 *
	 * @param int  $userId
	 *                userId of the user to get macros for.
	 * @param bool $includeStaff
	 *                Include staff macros.
	 *
	 * @return array
	 */
	public function getMacrosForUser($userId, $includeStaff = false)
	{
		if ($includeStaff)
		{
			return $this->fetchAllKeyed('SELECT * FROM liam_macros WHERE user_id=? OR staff_macro=1 ORDER BY macro_id',
				'macro_id', $userId);
		}

		return $this->fetchAllKeyed('SELECT * FROM liam_macros WHERE user_id=? ORDER BY macro_id',
			'macro_id', $userId);
	}

	/**
	 * Gets the admin macros a user is allowed to use.
	 *
	 * @param array $user
	 *            User array.
	 *
	 * @return array
	 */
	public function getAdminMacrosForUser($user)
	{
		if (!is_array($user))
		{
			return array();
		}

		$macros = $this->_getDb()->fetchAssoc('SELECT * FROM liam_macros_admin');

		$userGroups = explode(',',
			implode(',', array_merge((array)$user['user_group_id'], (array)$user['secondary_group_ids'])));

		$allowed = array();

		foreach ($macros as $macro)
		{
			if ($this->r_in_array(explode(',', $macro['usergroups']), $userGroups))
			{
				$allowed[] = $macro;
			}
		}

		return $allowed;
	}

	/**
	 * Gets the macro associated with a macro id.
	 *
	 * @param string  $macroId
	 *            Macro ID of the macro to get.
	 * @param boolean $adminMacro
	 *            If true, will get macro from the predefined macros table.
	 *
	 * @return array|null
	 */
	public function getMacroFromId($macroId, $adminMacro = false)
	{
		if ($macroId == '' || $macroId == '-')
		{
			return null;
		}

		if ($adminMacro)
		{
			$macroArray = $this->_getDb()->fetchRow('SELECT * FROM liam_macros_admin WHERE macro_id=?', $macroId);
			$macroArray['usergroups'] = explode(',', $macroArray['usergroups']);

			return $macroArray;
		}
		else
		{
			return $this->_getDb()->fetchRow('SELECT * FROM liam_macros WHERE macro_id=?', array(
				$macroId
			));
		}
	}

	/**
	 * Gets all admin macros.
	 *
	 * @return array|false
	 */
	public function getAdminMacros()
	{
		return $this->_getDb()->fetchAssoc('SELECT * FROM liam_macros_admin');
	}

	/**
	 * Checks if the person can add a new macro.
	 * This checks things like maximum macros as well as permissions.
	 *
	 * @param XenForo_Visitor $visitor
	 *            Instance of XenForo_Visitor class for the member you wish to check.
	 *
	 * @return boolean True if user can add a new macro, false otherwise.
	 */
	public function canAddMacro(XenForo_Visitor $visitor)
	{
		if (!$visitor->hasPermission('macro_permissions', 'can_use_macros'))
		{
			return false;
		}
		else if (($visitor->hasPermission('macro_permissions',
				'max_macros') <= $this->numMacros($visitor->getuserid()) && ($visitor->hasPermission('macro_permissions',
					'max_macros') != -1))
		)
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks to see if the visitor can view macros.
	 * This is mainly used to insert the macro chooser on post pages.
	 *
	 * @param XenForo_Visitor $visitor
	 *            Instance of the visitor you wish to check.
	 *
	 * @param bool            $permissionOnly
	 *
	 * @return boolean True if macros can be viewed, false otherwsie.
	 */
	public function canViewMacros(XenForo_Visitor $visitor, $permissionOnly = false)
	{
		if (!$visitor->hasPermission('macro_permissions', 'can_use_macros'))
		{
			return false;
		}
		if (!$permissionOnly && $this->numMacros($visitor->getuserid()) <= 0 && (count($this->getAdminMacrosForUser($this->getUserModel()
					->getFullUserById($visitor->getuserid()))) <= 0)
		)
		{
			return false;
		}

		return true;
	}

	public function hiddenOnThreadQuickReply($userId)
	{
		return $this->_getDb()->fetchOne("SELECT macros_hide_qr FROM xf_user_option WHERE user_id=?", $userId);
	}

	public function hiddenOnThreadCreateReply($userId)
	{
		return $this->_getDb()->fetchOne("SELECT macros_hide_ntnr FROM xf_user_option WHERE user_id=?", $userId);
	}

	public function hiddenOnConversationQuickReply($userId)
	{
		return $this->_getDb()->fetchOne("SELECT macros_hide_convo_qr FROM xf_user_option WHERE user_id=?", $userId);
	}

	public function hiddenOnConversationCreateReply($userId)
	{
		return $this->_getDb()->fetchOne("SELECT macros_hide_convo_ncnr FROM xf_user_option WHERE user_id=?", $userId);
	}

	/**
	 * Gets the amount of macros associated with a user.
	 *
	 * @param int $userId
	 *            User ID of user to check
	 *
	 * @return int Number of macros that belong to the user id specified.
	 */
	public function numMacros($userId)
	{
		return count($this->getMacrosForUser($userId));
	}

	private function r_in_array(array $needle, array $haystack)
	{
		foreach ($needle as $item)
		{
			if (in_array($item, $haystack))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets the user model
	 *
	 * @return XenForo_Model_User
	 */
	public function getUserModel()
	{
		return $this->getModelFromCache('XenForo_Model_User');
	}

	public function getOptionsForUser($userId)
	{
		return $this->_getDb()
			->fetchRow("SELECT macros_hide_convo_qr, macros_hide_convo_ncnr, macros_hide_ntnr, macros_hide_qr FROM xf_user_option WHERE user_id=?",
				$userId);
	}

	/**
	 * @param XenForo_View $view
	 * @param array        $userMacros
	 * @param array        $adminMacros
	 *
	 * @return array
	 */
	public function prepareArrayForDropDown(XenForo_View $view, array $userMacros, array $adminMacros)
	{
		$tmp = array();
		foreach ($userMacros as &$ma)
			$tmp[] = &$ma["name"];
		array_multisort($tmp, $userMacros);

		$tmp = array();
		foreach ($adminMacros as &$ma)
			$tmp[] = &$ma["name"];
		array_multisort($tmp, $adminMacros);

		return array($userMacros, $adminMacros);
	}

}