<?php

abstract class LiamW_Shared_DatabaseSchema_Abstract
{

	/**
	 * Version id of installed addon
	 */
	protected $version;

	/**
	 * If true, run remove SQL
	 */
	protected $drop;

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
	public function __construct($addonversion = 0, $drop = false, $throw = true)
	{
		$this->version = $addonversion;
		$this->drop = $drop;
		$this->db = XenForo_Application::getDb();
	}

	/**
	 * Run relevant Sql code.
	 *
	 * @return true string true on success, returns exception message if a Zend_Db_Exception occurs.
	 */
	final public function run()
	{
		if ($this->drop)
		{
			try
			{
				$this->db->query($this->_getDropSql());
			}
			catch (Zend_Db_Exception $e)
			{
				XenForo_Error::logException($e);
				return $e->getMessage();
			}
		}
		else
		{
			try
			{
				if (! $this->_tableExists($this->_getTableName()) && $this->version > 0)
				{
					$sqlarr = $this->getSql();
					
					$this->db->query($sqlarr[0]);
				}
				else
				{
					$sqlarr = $this->getSql();
					
					if ($this->version == 0)
					{
						$this->db->query($sqlarr[0]);
					}
					else
					{
						
						foreach ($sqlarr as $version => $sql)
						{
							if ($this->version <= $version)
							{
								if (is_array($sql))
								{
									foreach ($sql as $osql)
									{
										$this->db->query($osql);
									}
								}
								else
								{
									$this->db->query($sql);
								}
							}
						}
					}
				}
			}
			catch (Zend_Db_Exception $e)
			{
				XenForo_Error::logException($e);
				
				if ($this->drop)
				{
					print("<!--" . htmlspecialchars($e->getMessage()) . "-->");
					throw new XenForo_Exception("An error occured while installing tables. Contact dev.", true);
				}
				
				return $e->getMessage();
			}
			catch (XenForo_Exception $e)
			{
				XenForo_Error::logException($e);
				
				if ($this->drop)
				{
					print("<!--" . htmlspecialchars($e->getMessage()) . "-->");
					throw new XenForo_Exception("An error occured while installing tables. Contact dev.", true);
				}
				
				return $e->getMessage();
			}
		}
		
		return true;
	}

	/**
	 * Get the array of Sql code to be executed.
	 *
	 * @return array
	 */
	final protected function getSql()
	{
		return $this->_getSql();
	}

	final private function _tableExists($tablename)
	{
		try
		{
			$this->db->query("select * from `$tablename` limit 1");
		}
		catch (Zend_Db_Exception $e)
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Function designed to be overriden by classes to return the relevant Sql data.
	 *
	 * @return array
	 */
	abstract protected function _getSql();

	abstract protected function _getDropSql();

	abstract protected function _getTableName();

}