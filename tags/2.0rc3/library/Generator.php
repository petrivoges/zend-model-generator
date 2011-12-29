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
		
		$this->log('Listing tables ...');
		$tables = $this->container->getAdapter()->listTables();
		$this->log(count($tables) . ' tables total. Initializing renderer ...');
		$renderer = $this->container->getRenderer();

		$tmp = array();
		foreach ($tables as $id => $name){
			$this->log('Analyzing table: '.$name);
			
			try {
				$tmp[$name] = $table = new Generator_Table($name, $this->container);
			}catch(Exception $e){
				
				$this->log('Error cought: '.$e->getMessage());
				
				if($this->getContainer()->getConfig()->options->ignoreErrors == true){
					$this->log('Table: "'.$name.'" will be skipped. Some dependencies in generated files may not work properly.');
				}else{
					$this->log('No files were changed. Script will exit now.');
					exit;
				}
			}
		}
		
		foreach ($tmp as $name => $table){
			$this->log('Rendering: '.$name);
			try {
				$renderer->make($table);
			}catch (Exception $e){
				$this->log('Error cought: '.$e->getMessage());
				if($this->getContainer()->getConfig()->options->ignoreErrors == true){
					$this->log('Rendering process for table: "'.$name.'" will be skipped.');
				}else{
					$this->log('Script will exit now.');
					exit;
				}
			}
		}
		
		return true;
	}
}