<?php

class LiamW_PostMacros_Model_Macros extends XenForo_Model
{
	protected $_orderOptions = array(
		'display_order' => 'display_order'
	);

	public function getMacros(array $conditions = array(), array $fetchOptions = array())
	{
		$whereConditions = $this->_prepareMacrosConditions($conditions);
		$orderByClause = ' ' . $this->getOrderByClause($this->_orderOptions, $fetchOptions);

		return $this->fetchAllKeyed(
			'SELECT * FROM liam_post_macros WHERE ' . $whereConditions . $orderByClause, 'macro_id'
		);
	}

	public function getAllAdminMacros()
	{
		return $this->fetchAllKeyed(
			'SELECT * FROM liam_post_macros_admin', 'admin_macro_id'
		);
	}

	public function getMacroById($macroId)
	{
		return $this->_getDb()->fetchRow(
			'SELECT * FROM liam_post_macros WHERE macro_id=?', $macroId
		);
	}

	public function getAdminMacroById($adminMacroId)
	{
		return $this->_getDb()->fetchRow(
			'SELECT * FROM liam_post_macros_admin WHERE admin_macro_id=?'
			, $adminMacroId);
	}

	public function countMacrosForUser($userId)
	{
		return $this->_getDb()->fetchOne('SELECT COUNT(*) FROM liam_post_macros WHERE user_id=?', $userId);
	}

	public function getMacrosForDisplay(array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		if (XenForo_Permission::hasPermission($viewingUser['permissions'], 'liam_postMacros', 'liamMacros_createStaff'))
		{
			return $this->fetchAllKeyed('SELECT * FROM liam_post_macros WHERE user_id=? OR staff_macro=1 ORDER BY display_order ASC',
				'macro_id',
				$viewingUser['user_id']);
		}
		else
		{
			return $this->getMacros(array('user_id' => $viewingUser['user_id']), array('order' => 'display_order'));
		}
	}

	public function getAdminMacrosForDisplay(array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		return $this->getAllAdminMacros();
	}

	public function getAdminMacrosForPublicDisplay(array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		$allMacros = $this->prepareAdminMacros($this->getAllAdminMacros());

		$inUsergroups = explode(',', $viewingUser['secondary_group_ids']);
		$inUsergroups[] = $viewingUser['user_group_id'];

		foreach ($allMacros as $key => $macro)
		{
			if (array_diff($macro['authorized_usergroups'], $inUsergroups) === $macro['authorized_usergroups'])
			{
				unset($allMacros[$key]);
			}
		}

		return $allMacros;
	}

	public function getMacrosForSelect(array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		return array(
			'user' => $this->getMacrosForDisplay($viewingUser),
			'admin' => $this->getAdminMacrosForPublicDisplay($viewingUser)
		);
	}

	public function prepareAdminMacros(array $macros)
	{
		$return = array();

		foreach ($macros as $key => $macro)
		{
			$return[$key] = $this->prepareAdminMacro($macro);
		}

		return $return;
	}

	public function prepareAdminMacro(array $macro)
	{
		if (is_string($macro['authorized_usergroups']))
		{
			$macro['authorized_usergroups'] = @unserialize($macro['authorized_usergroups']);
		}

		return $macro;
	}

	public function prepareMacros(array $macros)
	{
		$return = array();

		foreach ($macros as $key => $macro)
		{
			$return[$key] = $this->prepareMacro($macro);
		}

		return $return;
	}

	public function prepareMacro(array $macro)
	{
		$macro['title'] = XenForo_Helper_String::censorString($macro['title']);
		$macro['thread_title'] = XenForo_Helper_String::censorString($macro['thread_title']);
		$macro['content'] = XenForo_Helper_String::censorString($macro['content']);

		$macro['canEdit'] = $this->canEditMacro($macro);
		$macro['canDelete'] = $this->canDeleteMacro($macro);

		return $macro;
	}

	protected function _prepareMacrosConditions(array $conditions)
	{
		$sqlConditions = array();

		$db = $this->_getDb();

		if (!empty($conditions['macro_id']))
		{
			if (is_array($conditions['macro_id']))
			{
				$sqlConditions[] = 'macro_id IN (' . $db->quote($conditions['macro_id']) . ')';
			}
			else
			{
				$sqlConditions[] = 'macro_id = ' . $db->quote($conditions['macro_id']);
			}
		}

		if (!empty($conditions['user_id']))
		{
			$sqlConditions[] = 'user_id = ' . $db->quote($conditions['user_id']);
		}

		if (!empty($conditions['staff_macro']))
		{
			if ($conditions['staff_macro'])
			{
				$sqlConditions[] = 'staff_macro = 1';
			}
			else
			{
				$sqlConditions[] = 'staff_macro = 0';
			}
		}

		return $this->getConditionsForClause($sqlConditions);
	}

	public function macrosEnabledInForum(array $forum)
	{
		return $forum['post_macros_enable'];
	}

	public function canUseMacro(array $macro, $type = 'user', array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		switch ($type)
		{
			case 'user':
				if ($macro['user_id'] == $viewingUser['user_id'])
				{
					return true;
				}

				if ($macro['staff_macro'] && XenForo_Permission::hasPermission($viewingUser['permissions'],
						'liam_postMacros',
						'liamMacros_useStaff')
				)
				{
					return true;
				}

				break;
			case 'admin':
				$macro = $this->prepareAdminMacro($macro);

				$inUsergroups = explode(',', $viewingUser['secondary_group_ids']);
				$inUsergroups[] = $viewingUser['user_group_id'];

				if (array_diff($macro['authorized_usergroups'], $inUsergroups) !== $macro['authorized_usergroups'])
				{
					return true;
				}
				break;
		}

		return false;
	}

	public function canCreateMacro(&$errorPhraseKey = '', array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		if (!XenForo_Permission::hasPermission($viewingUser['permissions'], 'liam_postMacros',
			'liamMacros_canCreate')
		)
		{
			return false;
		}

		if (XenForo_Permission::hasPermission($viewingUser['permissions'], 'liam_postMacros',
				'liamMacros_maxMacros') > -1 && XenForo_Permission::hasPermission($viewingUser['permissions'],
				'liam_postMacros',
				'liamMacros_maxMacros') <= $this->countMacrosForUser($viewingUser['user_id'])
		)
		{
			$errorPhraseKey = 'liam_postMacros_you_have_the_max_macros';

			return false;
		}

		return true;
	}

	public function canEditMacro(array $macro, &$errorPhraseKey = '', array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		if ($macro['user_id'] == $viewingUser['user_id'])
		{
			return true;
		}

		if ($macro['staff_macro'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'liam_postMacros',
				'liamMacros_editAllStaff')
		)
		{
			return true;
		}

		return false;
	}

	public function canDeleteMacro(array $macro, &$errorPhraseKey = '', array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		if ($macro['user_id'] == $viewingUser['user_id'])
		{
			return true;
		}

		if ($macro['staff_macro'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'liam_postMacros',
				'liamMacros_deleteAllStaff')
		)
		{
			return true;
		}

		return false;
	}

	public function showMacrosSelect(XenForo_View $view, array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);

		$options = XenForo_Application::getOptions();

		$viewParams = $view->getParams();
		$viewClass = XenForo_Application::resolveDynamicClassToRoot($view);
		$excludedViews = preg_split('/\r?\n/',
			$options->get('liam_postMacros_excluded_views'), -1, PREG_SPLIT_NO_EMPTY);

		switch ($viewClass)
		{
			case 'XenForo_ViewPublic_Thread_Create':
			case 'XenForo_ViewPublic_Thread_Reply':
				$enabledInForum = $this->macrosEnabledInForum($viewParams['forum']);

				$shouldDisplay = !$viewingUser['post_macros_hide_new_thread_reply'] && $enabledInForum;
				break;
			case 'XenForo_ViewPublic_Thread_View':
				$enabledInForum = $this->macrosEnabledInForum($viewParams['forum']);

				$shouldDisplay = !$viewingUser['post_macros_hide_thread_quick_reply'] && $enabledInForum;
				break;
			case 'XenForo_ViewPublic_Conversation_Add':
			case 'XenForo_ViewPublic_Conversation_Reply':
				$shouldDisplay = !$viewingUser['post_macros_hide_new_conversation_reply'];
				break;
			case 'XenForo_ViewPublic_Conversation_View':
				$shouldDisplay = !$viewingUser['post_macros_hide_conversation_quick_reply'];
				break;
			default:
				$shouldDisplay = $options->get('liam_postMacros_all_editors');
		}

		if (in_array($viewClass, $excludedViews))
		{
			$shouldDisplay = false;
		}

		return $shouldDisplay;
	}
}