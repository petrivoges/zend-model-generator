<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */
class Generator
{
	/**
	 * @var Generator_Container
	 */
	private $container = null;
	
	/**
	 * Create new instance of Model Generator
	 * @param Zend_Config $config
	 */
	public function __construct(Zend_Config $config)
	{
		$this->container = new Generator_Container($config);
	}
	
	/**
	 * @return Generator_Container
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
	/**
	 * Log message
	 * @param string $message
	 * @param int $priority
	 * @param mix $mixed
	 * @return Generator
	 */
	public function log($message, $priority = Zend_Log::INFO, $mixed = null)
	{
		$this->getContainer()->getLogger()->log($message, $priority, $mixed);
		return $this;
	}
	
	/**
	 * Generate models
	 * @param array $tables OPTIONAL - names of tables that should be analyzed
	 * @return void
	 */
	public function generate(array $tables = null)
	{
		$time = time();
		
		$tables = $this->container->getAdapter()->listTables();
		$renderer = $this->container->getRenderer();

		$tmp = array();
		foreach ($tables as $id => $name){
			$tmp[$name] = $table = new Generator_Table($name, $this->container);
			$template = new Generator_Template($this->container);
			$template->make($table);
		}
		return true;
	}
}