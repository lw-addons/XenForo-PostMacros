<?php

/**
 * Model for the Post Macros add-on.
 *
 * @author Liam W
 * @package Post Macros
 *
 */
class LiamW_Macros_Model_Macros extends XenForo_Model
{

	/**
	 * Gets the macros for a specific user.
	 *
	 * @param int $userId
	 *        	UserID of the user to get macros for.
	 */
	public function getMacrosForUser($userId, $includestaff = false)
	{
		if ($includestaff)
		{
			return $this->_getDb()->fetchAssoc('SELECT * FROM xf_liam_macros WHERE userid=' . $userId . ' OR staff_macro=1 ORDER BY macroid');
		}
		
		return $this->_getDb()->fetchAssoc('SELECT * FROM xf_liam_macros WHERE userid=' . $userId);
	}

	/**
	 * Gets the admin macros a user is allowed to use.
	 *
	 * @param array $user
	 *        	User array.
	 * @param boolean $fordropdown
	 *        	If true, formats array for use in dropdown.
	 */
	public function getAdminMacrosForUser($user)
	{
		if (! is_array($user))
		{
			return array();
		}
		
		$macros = $this->_getDb()->fetchAssoc('SELECT * FROM xf_liam_macros_admin');
		
		$usergroups = explode(',', implode(',', array_merge((array) $user['user_group_id'], (array) $user['secondary_group_ids'])));
		
		unset($user);
		
		$allowed = array();
		
		foreach ($macros as $key => $macro)
		{
			if ($this->r_in_array(explode(',', $macro['usergroups']), $usergroups))
			{
				$allowed[] = $macro;
			}
		}
		
		return $allowed;
	}

	/**
	 * Gets the macro associated with a macro id.
	 *
	 * @param string $macroid
	 *        	Macro ID of the macro to get.
	 * @param boolean $adminmacro
	 *        	If true, will get macro from the predefined macros table.
	 */
	public function getMacroFromId($macroid, $adminmacro = false)
	{
		if ($macroid == '' || $macroid == '-')
			return null;
		
		if ($adminmacro)
		{
			$macroarray = $this->_getDb()->fetchRow('SELECT * from xf_liam_macros_admin WHERE id=' . $macroid);
			$macroarray['usergroups'] = explode(',', $macroarray['usergroups']);
			
			return $macroarray;
		}
		else
		{
			return $this->_getDb()->fetchRow('SELECT * from xf_liam_macros WHERE macroid=' . $macroid);
		}
	}

	/**
	 * Gets all admin macros.
	 *
	 * @return Ambigous <multitype:, multitype:mixed >
	 */
	public function getAdminMacros()
	{
		return $this->_getDb()->fetchAssoc('SELECT * FROM xf_liam_macros_admin');
	}

	/**
	 * Checks if the person can add a new macro.
	 * This checks things like maximum macros as well as permissions.
	 *
	 * @param XenForo_Visitor $visitor
	 *        	Instance of XenForo_Visitor class for the member you wish to check.
	 *        	
	 * @return boolean True if user can add a new macro, false otherwise.
	 */
	public function canAddMacro(XenForo_Visitor $visitor)
	{
		if (! $visitor->hasPermission('macro_permissions', 'can_use_macros'))
		{
			return false;
		}
		else if (($visitor->hasPermission('macro_permissions', 'max_macros') <= $this->numMacros($visitor->getUserId()) && ($visitor->hasPermission('macro_permissions', 'max_macros') != - 1)))
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
	 *        	Instance of the visitor you wish to check.
	 * @return boolean True if macros can be viewed, false otherwsie.
	 */
	public function canViewMacros(XenForo_Visitor $visitor, $permissionOnly = false)
	{
		if (! $visitor->hasPermission('macro_permissions', 'can_use_macros'))
		{
			return false;
		}
		if (! $permissionOnly && $this->numMacros($visitor->getUserId()) <= 0 && (count($this->getAdminMacrosForUser($this->getUserModel()
			->getFullUserById($visitor->getUserId()))) <= 0))
		{
			return false;
		}
		
		return true;
	}

	public function optionsSaved($userid)
	{
		$db = $this->_getDb()->fetchOne("SELECT `userid` FROM `xf_liam_macros_options` WHERE `userid`='$userid'");
		
		if ($db)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function hiddenOnQr($userid)
	{
		return $this->_getDb()->fetchOne("SELECT `macros_hide_qr` FROM `xf_liam_macros_options` WHERE `userid`='$userid'");
	}

	public function hiddenOnNtNr($userid)
	{
		return $this->_getDb()->fetchOne("SELECT `macros_hide_ntnr` FROM `xf_liam_macros_options` WHERE `userid`='$userid'");
	}

	public function hiddenOnConvoQr($userid)
	{
		return $this->_getDb()->fetchOne("SELECT `macros_hide_convo_qr` FROM `xf_liam_macros_options` WHERE `userid`='$userid'");
	}

	public function hiddenOnConvoNcNr($userid)
	{
		return $this->_getDb()->fetchOne("SELECT `macros_hide_convo_ncnr` FROM `xf_liam_macros_options` WHERE `userid`='$userid'");
	}

	/**
	 * Gets the amount of macros associated with a user.
	 *
	 * @param int $userId
	 *        	User ID of user to check
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
		$model = XenForo_Model::create('XenForo_Model_User');
		
		return $model;
	}

	public function getOptionsForUser($userid)
	{
		$db = $this->_getDb();
		
		return $db->fetchRow("SELECT * FROM `xf_liam_macros_options` WHERE `userid`='$userid'");
	}

	/**
	 * Formats arrays for use in drop down selector.
	 *
	 * @param array $a 	
	 * @param array $b
	 * @param array $_
	 * 
	 * @return array
	 */
	public function prepareArrayForDropDown(array $a, array $b, array $_ = null)
	{
		$macros = array();
		
		foreach (func_get_args() as $param)
		{
			$macros = array_merge($macros, $param);
		}
		
		$bbCodeParser = new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('Base'));
		
		foreach ($macros as $key => $macro)
		{
			if (! (count($macro) > 1))
			{
				continue;
			}
			
			if (array_key_exists('content', $macro))
			{
				$macros[$key]['macro'] = $macros[$key]['content'];
				unset($macros[$key]['content']);
			}
			
			$macros[$key]['macrohtml'] = new XenForo_BbCode_TextWrapper($macros[$key]['macro'], $bbCodeParser);
		}
		
		$tmp = array();
		foreach ($macros as &$ma)
			$tmp[] = &$ma["name"];
		array_multisort($tmp, $macros);
		
		$start = array(
			
			'name' => new XenForo_Phrase("postmacros_master_name"),
			'thread_title' => '-',
			'macrohtml' => '-',
			'macro' => '-'
		);
		
		array_unshift($macros, $start);
		
		return $macros;
	}

}