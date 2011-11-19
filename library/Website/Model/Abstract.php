<?php

/**
 * Abstract model
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: Abstract.php 16 2011-02-21 14:00:23Z jacek $
 * @uses Model_Website
 */
abstract class Website_Model_Abstract
{
	/**
	 * @var Website_Model
	 */
	private static $_instance = null;
	
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $adapter = null;
	
	/**
	 * Get instance of Model_Website (alias for getInstance for compatybility)
	 * @return Model_Website
	 */
	public static function get()
	{
		return self::getInstance();
	}
	
	/**
	 * Get instance of Model_Website
	 * @return Model_Website
	 */
	public function getInstance()
	{
		if(self::$_instance === null){
			self::$_instance = new Model_Website();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor - not used
	 */
	private function __construct(){}
	
	/**
	 * Set adapter to use
	 * @param Zend_Db_Adapter_Abstract $adapter
	 * @return Model_Website
	 */
	public function setAdapter(Zend_Db_Adapter_Abstract $adapter)
	{
		$this->adapter = $adapter;
		return $this;
	}
	
	/**
	 * Get db adapter
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getAdapter()
	{
		if($this->adapter === null){
			$this->adapter = Zend_Db_Table::getDefaultAdapter();
		}
		return $this->adapter;
	}
	
	/**
	 * Get client's remote address (not checking proxy)
	 * @deprecated
	 * @return string ip address
	 */
	public function getClientRemoteAddress()
	{
		return $this->getFront()->getRequest()->getClientIp();
	}
	
	/**
	 * Get table
	 * @param string $name
	 * @return Website_Model_DbTable_Abstract
	 */
	public static function getTable($name)
	{
		$parts = explode('_', $name);
		foreach ($parts as &$part)
			$part = ucfirst($part);
		$name = implode($parts);
		$class = 'Model_DbTable_'.ucfirst($name);
		return new $class;
	}

	/**
	 * Get front controller
	 * @deprecated replace it with getFrontController
	 * @return Zend_Controller_Front
	 */
	protected function getFront()
	{
		return Zend_Controller_Front::getInstance();
	}
	
	/**
	 * @return Zend_Controller_Front
	 */
	protected function getFrontController()
	{
		return Zend_Controller_Front::getInstance();
	}
	
	/**
	 * Only if you are using services
	 * @return Service_Brooker
	 */
	protected function getService()
	{
		return Service_Brooker::getInstance();
	}
	
	/**
	 * Magic alias for getTable()
	 * @param string $table
	 * @return Website_Model_DbTable_Abstract
	 */
	public function __call($table, $paramNotUsed = null)
	{
		return $this->getTable($table);
	}
}