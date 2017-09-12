<?php

class LiamW_PostMacros_Importer_Macros extends XenForo_Importer_Abstract
{
	/** @var Zend_Db_Adapter_Abstract */
	protected $_sourceDb;

	protected $_defaultTables = array(
		'liam_macros',
		'liam_macros_admin'
	);

	public static function getName()
	{
		return 'Post Macros < 4.0.0';
	}

	public function configure(XenForo_ControllerAdmin_Abstract $controller, array &$config)
	{
		if ($config)
		{
			$errors = $this->validateConfiguration($config);
			if ($errors)
			{
				return $controller->responseError($errors);
			}

			return true;
		}
		else
		{
			$config = XenForo_Application::getConfig();
			$dbConfig = $config->get('db');

			$viewParams = array(
				'config' => array(
					'db' => array(
						'host' => $dbConfig->host,
						'port' => $dbConfig->port,
						'username' => $dbConfig->username,
						'password' => $dbConfig->password,
						'dbname' => $dbConfig->dbname
					)
				)
			);
		}

		return $controller->responseView('', 'liam_postMacros_import_macros', $viewParams);
	}

	public function validateConfiguration(array &$config)
	{
		$errors = array();

		try
		{
			$db = Zend_Db::factory('mysqli',
				array(
					'host' => $config['db']['host'],
					'port' => $config['db']['port'],
					'username' => $config['db']['username'],
					'password' => $config['db']['password'],
					'dbname' => $config['db']['dbname'],
					'charset' => 'utf-8'
				)
			);
			$db->getConnection();
		} catch (Zend_Db_Exception $e)
		{
			$errors[] = new XenForo_Phrase('source_database_connection_details_not_correct_x',
				array('error' => $e->getMessage()));
		}

		if ($errors)
		{
			return $errors;
		}

		foreach ($this->_defaultTables AS $table)
		{
			$exists = $db->fetchOne("SHOW TABLES LIKE '$table'");

			if (!$exists)
			{
				$errors[] = new XenForo_Phrase('liam_postMacros_table_x_does_not_exist', array('tablename' => $table));
			}
		}

		return $errors;
	}

	protected function _bootstrap(array $config)
	{
		if ($this->_sourceDb)
		{
			// already run
			return;
		}

		@set_time_limit(0);

		$this->_config = $config;

		$this->_sourceDb = Zend_Db::factory('mysqli',
			array(
				'host' => $config['db']['host'],
				'port' => $config['db']['port'],
				'username' => $config['db']['username'],
				'password' => $config['db']['password'],
				'dbname' => $config['db']['dbname'],
				'charset' => 'utf8'
			)
		);
	}

	public function getSteps()
	{
		return array(
			'usermacros' => array(
				'title' => new XenForo_Phrase('liam_postMacros_user_macros')
			),
			'adminmacros' => array(
				'title' => new XenForo_Phrase('liam_postMacros_admin_macros')
			)
		);
	}

	public function stepUserMacros($start, array $options)
	{
		$options = array_merge(array(
			'limit' => 100,
			'max' => false
		), $options);

		$sDb = $this->_sourceDb;

		if ($options['max'] === false)
		{
			$options['max'] = $sDb->fetchOne("
				SELECT MAX(macro_id)
				FROM liam_macros
			");
		}

		$macros = $sDb->fetchAll($sDb->limit("
			SELECT *
			FROM liam_macros
			WHERE macro_id > ?
			ORDER BY macro_id ASC
		", $options['limit']), $start);

		if (!$macros)
		{
			return true;
		}

		$next = 0;
		$total = 0;

		foreach ($macros AS $macro)
		{
			$next = $macro['macro_id'];

			$imported = $this->_importMacro($macro, $options);
			if ($imported)
			{
				$total++;
			}
		}

		$this->_session->incrementStepImportTotal($total);

		return array(
			$next,
			$options,
			$this->_getProgressOutput($next, $options['max'])
		);
	}

	protected function _importMacro(array $macro, array $options)
	{
		/** @var LiamW_PostMacros_Model_Import $importerModel */
		$importerModel = XenForo_Model::create('LiamW_PostMacros_Model_Import');

		$macro['content'] = isset($macro['content']) ? $macro['content'] : $macro['macro'];

		if (!isset($macro['thread_title']))
		{
			$macro['thread_title'] = '';
		}

		if (!isset($macro['apply_prefix']))
		{
			$macro['apply_prefix'] = 0;
		}

		if (!isset($macro['lock_thread']))
		{
			$macro['lock_thread'] = 0;
		}

		if (!isset($macro['staff_macro']))
		{
			$macro['staff_macro'] = 0;
		}

		$inputMacro = array(
			'macro_id' => 0,
			'user_id' => $macro['user_id'],
			'title' => $macro['name'],
			'thread_title' => $macro['thread_title'],
			'thread_prefix' => $macro['apply_prefix'],
			'content' => $macro['content'],
			'lock_thread' => $macro['lock_thread'],
			'staff_macro' => $macro['staff_macro']
		);

		return $importerModel->importUserMacro($macro['macro_id'], $inputMacro);
	}

	public function stepAdminMacros($start, array $options)
	{
		$options = array_merge(array(
			'limit' => 100,
			'max' => false
		), $options);

		$sDb = $this->_sourceDb;

		if ($options['max'] === false)
		{
			$options['max'] = $sDb->fetchOne("
				SELECT MAX(macro_id)
				FROM liam_macros_admin
			");
		}

		$macros = $sDb->fetchAll($sDb->limit("
			SELECT *
			FROM liam_macros_admin
			WHERE macro_id > ?
			ORDER BY macro_id ASC
		", $options['limit']), $start);

		if (!$macros)
		{
			return true;
		}

		$next = 0;
		$total = 0;

		foreach ($macros AS $macro)
		{
			$next = $macro['macro_id'];

			$imported = $this->_importAdminMacro($macro, $options);
			if ($imported)
			{
				$total++;
			}
		}

		$this->_session->incrementStepImportTotal($total);

		return array(
			$next,
			$options,
			$this->_getProgressOutput($next, $options['max'])
		);
	}

	protected function _importAdminMacro(array $macro, array $options)
	{
		/** @var LiamW_PostMacros_Model_Import $importerModel */
		$importerModel = XenForo_Model::create('LiamW_PostMacros_Model_Import');

		if (!isset($macro['thread_title']))
		{
			$macro['thread_title'] = '';
		}

		if (!isset($macro['apply_prefix']))
		{
			$macro['apply_prefix'] = 0;
		}

		if (!isset($macro['lock_thread']))
		{
			$macro['lock_thread'] = 0;
		}

		$inputMacro = array(
			'admin_macro_id' => 0,
			'title' => $macro['name'],
			'thread_title' => $macro['thread_title'],
			'thread_prefix' => $macro['apply_prefix'],
			'content' => $macro['content'],
			'lock_thread' => $macro['lock_thread'],
			'authorized_usergroups' => serialize(explode(',', $macro['usergroups']))
		);

		return $importerModel->importAdminMacro($macro['macro_id'], $inputMacro);
	}
}