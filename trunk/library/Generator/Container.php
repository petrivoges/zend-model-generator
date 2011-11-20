<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: Generator.php 15 2011-11-19 17:25:01Z kobus.jacek@gmail.com $
 */
class Generator_Container
{
	/**
	 * @var Zend_Config
	 */
	private $config = null;
	
	/**
	 *
	 * @var Zend_Db_Adapter_Pdo_Mysql
	 */
	private $adapter = null;
	
	/**
	 *
	 * @var Zend_Log
	 */
	private static $logger;
	
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
		
	}
	
	public function getTemplate()
	{
		
	}
	
	/**
	 * @return Generator_Dependencies
	 */
	public function getDependencyChecker()
	{
		return new Generator_Dependencies();
	}
	
	/**
	 * @param Zend_Db_Table_Abstract $table
	 * @return Generator_Analyzer
	 */
	public function getAnalyzer(Zend_Db_Table_Abstract $table)
	{
		return new Generator_Analyzer($table, $this);
	}
	
	/**
	 * @return Zend_Log
	 */
	public function log($message = null, $priority = Zend_Log::INFO)
	{
		if(!self::$logger){
			self::$logger = new Zend_Log(new Zend_Log_Writer_Stream('php://output'));
			self::log('Message logging enabled.');
		}
		if($message){
			self::$logger->log($message, $priority);
		}
		return self::$logger;
	}
}







