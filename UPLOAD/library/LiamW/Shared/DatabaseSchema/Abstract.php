<?php

abstract class LiamW_Shared_DatabaseSchema_Abstract
{

	/**
	 * Version id of installed addon
	 */
	protected $version;

	/**
	 * The database object
	 */
	protected $db;

	/**
	 * If true, throw on error
	 */
	protected $throw;

	/**
	 *
	 * @param number $addonversion
	 *        	Version id of the addon being installed. 0 if new install.
	 * @param string $drop
	 *        	If true, drops tables. Use in uninstall.
	 * @param boolean $throw
	 *        	If true, throws exception when an error occured installing tables.
	 */
	public function __construct($addonversion = 0, $throw = true)
	{
		$this->version = $addonversion;
		$this->throw = $throw;
		$this->db = XenForo_Application::getDb();
	}

	/**
	 * Run all sql code since the last update.
	 *
	 * @return true string true on success, returns exception message if a Zend_Db_Exception occurs.
	 */
	final public function install()
	{
		$db = $this->db;
		XenForo_Db::beginTransaction($db);
		
		try
		{
			if (! $this->_tableExists($this->_getTableName()) && $this->version > 0)
			{
				$sqlarr = $this->getSql();
				
				if (is_array($sqlarr[0]))
				{
					foreach ($sqlarr[0] as $sql)
					{
						$db->query($sql);
					}
				}
				else
				{
					$db->query($sqlarr[0]);
				}
			}
			else
			{
				$sqlarr = $this->getSql();
				
				if (! is_array($sqlarr) && $this->version == 0)
				{
					$db->query($sqlarr);
				}
				else
				{
					if ($this->version == 0)
					{
						if (is_array($sqlarr[0]))
						{
							if (isset($sqlarr[0]['ignoreerror']))
							{
								$ignoreError = $sqlarr[0]['ignoreerror'];
								unset($sqlarr[0]['ignoreerror']);
							}
							else
							{
								$ignoreError = '';
							}
							
							foreach ($sqlarr[0] as $sql)
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
								}
								catch (Exception $e)
								{
								}
							}
							else
							{
								try
								{
									$db->query($ignoreError);
								}
								catch (Exception $e)
								{
								}
							}
						}
						else
						{
							$db->query($sqlarr[0]);
						}
					}
					else
					{
						foreach ($sqlarr as $version => $sql)
						{
							if ($this->version <= $version)
							{
								if (is_array($sql))
								{
									if (isset($sql['ignoreerror']))
									{
										$ignoreError = $sql['ignoreerror'];
										unset($sql['ignoreerror']);
									}
									else
									{
										$ignoreError = '';
									}
									
									foreach ($sql as $osql)
									{
										$db->query($osql);
									}
									
									if (is_array($ignoreError))
									{
										try
										{
											foreach ($ignoreError as $osql)
											{
												$db->query($osql);
											}
										}
										catch (Exception $e)
										{
										}
									}
									else
									{
										try
										{
											$db->query($ignoreError);
										}
										catch (Exception $e)
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
		}
		catch (Zend_Db_Exception $e)
		{
			XenForo_Db::rollback($db);
			XenForo_Error::logException($e);
			
			if ($this->throw)
			{
				print("<!--" . htmlspecialchars($e->getMessage()) . "-->");
				throw new XenForo_Exception("<!--" . htmlspecialchars($e->getMessage()) . "--> An error occured while installing table " . $this->_getTableName() . ". Contact dev.", true);
			}
			
			return $e->getMessage();
		}
		catch (XenForo_Exception $e)
		{
			XenForo_Db::rollback($db);
			XenForo_Error::logException($e);
			
			if ($this->throw)
			{
				print("<!--" . htmlspecialchars($e->getMessage()) . "-->");
				throw new XenForo_Exception("<!--" . htmlspecialchars($e->getMessage()) . "--> An error occured while installing table " . $this->_getTableName() . ". Contact dev.", true);
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
		if (! $this->_tableExists($this->_getTableName()))
		{
			return;
		}
		
		$db = $this->db;
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
		}
		catch (Zend_Db_Exception $e)
		{
			XenForo_Db::rollback($db);
			XenForo_Error::logException($e);
			
			return $e->getMessage();
		}
		catch (XenForo_Exception $e)
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

	final protected function getUninstallSql()
	{
		return $this->_getUninstallSql();
	}

	final private function _tableExists($tablename)
	{
		try
		{
			$this->db->query("SELECT * FROM `$tablename` LIMIT 1");
		}
		catch (Zend_Db_Exception $e)
		{
			return false;
		}
		
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