<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */
class Generator
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
	 * Create new instance of Model Generator
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		if(!class_exists('Zend_Loader_Autoloader', false))
			require_once 'Zend/Loader/Autoloader.php';
		Zend_Loader_Autoloader::getInstance()
			->registerNamespace('Generator');
		self::log('Zend_Autoloader started. Starting Generator.');
		$this->config = new Zend_Config($config);
	}
	
	/**
	 * @return Zend_Config
	 */
	protected function getConfig()
	{
		return $this->config;
	}
	
	/**
	 * @return Zend_Db_Adapter_Pdo_Mysql
	 */
	protected function getAdapter()
	{
		if(!$this->adapter){
			self::log('Starting DB adapter ...');
			$this->adapter = new Zend_Db_Adapter_Pdo_Mysql($this->getConfig()->db);
			Zend_Db_Table::setDefaultAdapter($this->adapter);
		}
		self::log('DB adapter started.');
		return $this->adapter;
	}
	
	/**
	 * Generate models
	 * @param array $tables OPTIONAL - names of tables that should be analyzed
	 * @return void
	 */
	public function generate(array $tables = null)
	{
		$time = time();
		self::log('Starting generation procedure ...');
		if(!$tables){
			self::log('Tables array was not provided; will read table list from adapter.', Zend_Log::NOTICE);
			$tables = $this->getAdapter()->listTables();
		}else{
			self::log('Using user-defined tables list.');
		}
		
		self::log('Starting renderer ...');
		$view = new Generator_Renderer($this->getConfig());
		
		self::log('All of '.count($tables).' tables will now be analyzed.');
		$tmp = array();
		foreach ($tables as $id => $name){
			Generator::log('Analyzing '.$name.'...');
			$tmp[$name] = new Generator_Table($name, $this->getConfig()->generator);
		}
		
		foreach ($tmp as $name => $model){
			self::log('Rendering '.$name.'.');
			$view->make($model);
		}

		self::log('Completed in '.(time()-$time).' seconds.');
		self::log('Enjoy!');
		return;
	}
	
	/**
	 * @return Zend_Log
	 */
	public static function log($message = null, $priority = Zend_Log::INFO)
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







