<?php

abstract class LiamW_Shared_DatabaseSchema_Abstract
{

	/**
	 * Version id of installed addon
	 */
	protected $_version;

	/**
	 * The database object
	 */
	protected $_db;

	/**
	 * If true, throw on error
	 */
	protected $_throw;


	final public function __construct($addonVersion = 0, $throw = true)
	{
		$this->_version = $addonVersion;
		$this->_throw = $throw;
		$this->_db = XenForo_Application::getDb();
	}

	/**
	 * Installs addon, running relevant SQL code.
	 *
	 * @return bool|string
	 * @throws XenForo_Exception
	 */
	final public function install()
	{
		if (!$this->_shouldRun())
		{
			return false;
		}

		$db = $this->_db;
		XenForo_Db::beginTransaction($db);

		try
		{
			if (!$this->_tableExists($this->_getTableName()) && $this->_version > 0)
			{
				$sqlArray = $this->getSql();

				if (is_array($sqlArray[0]))
				{
					foreach ($sqlArray[0] as $sql)
					{
						$db->query($sql);
					}
				}
				else
				{
					$db->query($sqlArray[0]);
				}
			}
			else
			{
				$sqlArray = $this->getSql();

				if (!is_array($sqlArray) && $this->_version == 0)
				{
					$db->query($sqlArray);
				}
				else if (is_array($sqlArray))
				{
					if ($this->_version == 0)
					{
						if (is_array($sqlArray[0]))
						{
							if (isset($sqlArray[0]['ignoreError']))
							{
								$ignoreError = $sqlArray[0]['ignoreError'];
								unset($sqlArray[0]['ignoreError']);
							}
							else
							{
								$ignoreError = '';
							}

							foreach ($sqlArray[0] as $sql)
							{
								$db->query($sql);
							}

							if (is_array($ignoreError))
							{
								try
								{
									foreach ($ignoreError as $sql)
									{
										$db->query($sql);
									}
								} catch (Exception $e)
								{
								}
							}
							else
							{
								try
								{
									$db->query($ignoreError);
								} catch (Exception $e)
								{
								}
							}
						}
						else
						{
							$db->query($sqlArray[0]);
						}
					}
					else
					{
						foreach ($sqlArray as $version => $sql)
						{
							if ($this->_version == $version)
							{
								if (is_array($sql))
								{
									if (isset($sql['ignoreError']))
									{
										$ignoreError = $sql['ignoreError'];
										unset($sql['ignoreError']);
									}
									else
									{
										$ignoreError = '';
									}

									foreach ($sql as $oSql)
									{
										$db->query($oSql);
									}

									if (is_array($ignoreError))
									{
										try
										{
											foreach ($ignoreError as $oSql)
											{
												$db->query($oSql);
											}
										} catch (Exception $e)
										{
										}
									}
									else
									{
										try
										{
											$db->query($ignoreError);
										} catch (Exception $e)
										{
										}
									}
								}
								else
								{
									$db->query($sql);
								}
							}
						}
					}
				}
			}
		} catch (Zend_Db_Exception $e)
		{
			XenForo_Db::rollback($db);
			XenForo_Error::logException($e);

			if ($this->_throw)
			{
				print("<!--" . htmlspecialchars($e->getMessage()) . "-->");
				throw new XenForo_Exception("<!--" . htmlspecialchars($e->getMessage()) . "--> An error occurred while installing table " . $this->_getTableName() . ". Contact dev.", true);
			}

			return $e->getMessage();
		} catch (XenForo_Exception $e)
		{
			XenForo_Db::rollback($db);
			XenForo_Error::logException($e);

			if ($this->_throw)
			{
				print("<!--" . htmlspecialchars($e->getMessage()) . "-->");
				throw new XenForo_Exception("<!--" . htmlspecialchars($e->getMessage()) . "--> An error occurred while installing table " . $this->_getTableName() . ". Contact dev.", true);
			}

			return $e->getMessage();
		}

		XenForo_Db::commit($db);

		return true;
	}

	/**
	 * Runs code to uninstall program
	 *
	 * @return boolean string true if SQL was executed fine, returns the exception message if an exception was thrown.
	 */
	final public function uninstall()
	{
		if (!$this->_shouldRun())
		{
			return false;
		}

		if (!$this->_tableExists($this->_getTableName()))
		{
			return true;
		}

		$db = $this->_db;
		XenForo_Db::beginTransaction($db);

		try
		{
			$uninstallSql = $this->getUninstallSql();

			if (is_array($uninstallSql))
			{
				foreach ($uninstallSql as $sql)
				{
					$db->query($sql);
				}
			}
			else
			{
				$db->query($uninstallSql);
			}
		} catch (Zend_Db_Exception $e)
		{
			XenForo_Db::rollback($db);
			XenForo_Error::logException($e);

			return $e->getMessage();
		} catch (XenForo_Exception $e)
		{
			XenForo_Db::rollback($db);
			XenForo_Error::logException($e);

			return $e->getMessage();
		}

		XenForo_Db::commit($db);

		return true;
	}

	/**
	 * Get the array of Sql code to be executed.
	 *
	 * @return array
	 */
	final protected function getSql()
	{
		return $this->_getInstallSql();
	}

	/**
	 * Returns the uninstall SQL from the abstract function.
	 *
	 * @return array
	 */
	final protected function getUninstallSql()
	{
		return $this->_getUninstallSql();
	}

	/**
	 * Confirms table exists.
	 *
	 * @param bool $tableName
	 *
	 * @return bool
	 */
	final protected function _tableExists($tableName = false)
	{
		if (!$tableName)
		{
			$tableName = $this->_getTableName();
		}

		try
		{
			$this->_db->query("SELECT * FROM " . $tableName . " LIMIT 1");
		} catch (Zend_Db_Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Run checks to see if installer should run.
	 *
	 * Returns true by default.
	 *
	 * @return bool
	 */
	protected function _shouldRun()
	{
		return true;
	}

	/**
	 * Return the string or array of SQL required for installation.
	 *
	 * @return array string
	 */
	abstract protected function _getInstallSql();

	/**
	 * Return the string or array of the SQL required for uninstallation.
	 *
	 * @return array string
	 */
	abstract protected function _getUninstallSql();

	/**
	 * Return the table name
	 *
	 * @return string
	 */
	abstract protected function _getTableName();

}