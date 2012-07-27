<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: Container.php 60 2012-05-27 12:20:44Z jacek $
 */
class Generator_Container
{
	/**
	 * @var Zend_Config
	 */
	private $config = null;
	
	/**
	 * @var Zend_Db_Adapter_Pdo_Mysql
	 */
	private $adapter = null;
	
	/**
	 * @var Zend_Log
	 */
	private $logger;
	
	private $dependencyChecker = null;
	
	private $analyzer = null;
	
	/**
	 * @param Zend_Config $config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}
	
	/**
	 * @return Zend_Config
	 */
	public function getConfig()
	{
		return $this->config;
	}
	
	/**
	 * @return Zend_Db_Adapter_Pdo_Mysql
	 */
	public function getAdapter()
	{
		if(!$this->adapter){
			//self::log('Starting DB adapter ...');
			$this->adapter = new Zend_Db_Adapter_Pdo_Mysql($this->getConfig()->db);
			Zend_Db_Table::setDefaultAdapter($this->adapter);
		}
		//self::log('DB adapter started.');
		return $this->adapter;
	}
	
	public function getRenderer()
	{
		return new Generator_Template($this);
	}
	
	/**
	 * @return Generator_Dependencies
	 */
	public function getDependencyChecker()
	{
		if(!$this->dependencyChecker)
			$this->dependencyChecker = new Generator_Dependencies();
		return $this->dependencyChecker;
	}
	
	/**
	 * @param Zend_Db_Table_Abstract $table
	 * @return Generator_Analyzer
	 */
	public function getAnalyzer()
	{
		if(!$this->analyzer)
			$this->analyzer = new Generator_Analyzer($this);
		return $this->analyzer;
	}
	
	/**
	 * @return Zend_Log
	 */
	public function getLogger()
	{
		if(!$this->logger){
			$this->logger = new Zend_Log(new Zend_Log_Writer_Stream('php://output'));
		}
		return $this->logger;
	}
	
	/**
	 * @return int
	 */
	public function getMicrotime()
	{
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}







