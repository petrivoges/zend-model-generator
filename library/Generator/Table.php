<?php

/**
 * This is a model
 *
 * @author Jacek Kobus <jacekkobus.com>
 */
class Generator_Table
{
	/**
	 * @var Zend_Db_Table
	 */
	private $table;
	
	/**
	 * @var Generator_Container
	 */
	private $container;
	
	/**
	 * @var array
	 */
	private $info;
	
	/**
	 * Create new table instance
	 * @param string $table
	 * @param Generator_Container $container
	 */
	public function __construct($table, Generator_Container $container)
	{
		if(is_string($table)){
			$this->table = new Zend_Db_Table($table);
		}else
			throw new Generator_Exception('Table parameter must be a string.');
		
		$this->container = $container;
		$this->info = $this->container->getAnalyzer()->analyze($this->table);
	}
	
	/**
	 * @return Zend_Db_Table
	 */
	protected function getTable()
	{
		return $this->table;
	}
	
	/**
	 * @return array
	 */
	public function getParents()
	{
		return $this->container->getDependencyChecker()->getParentsOf($this->getName());
	}
	
	/**
	 * @return array
	 */
	public function getChildren()
	{
		return $this->container->getDependencyChecker()->getChildrenOf($this->getName());
	}
	
	/**
	 * @return array
	 */
	public function getDependentTables()
	{
		return $this->container->getDependencyChecker()->getDependenciesFor($this->getName());
	}
	
	/**
	 * Get table name
	 * @return string
	 */
	public function getName()
	{
		return $this->info['name'];
	}
	
	/**
	 * Get filename for new model
	 * @return string
	 */
	public function getFilename()
	{
		return $this->formatFilename($this->getName());
	}
	
	/**
	 * Get model name
	 * @param string $tableName OPTIONAL
	 * @return string
	 */
	public function getModelName($tableName = null)
	{
		if(!$tableName)
			$tableName = $this->getName();
		return $this->formatClassName($tableName, 'model');
	}
	
	/**
	 * Get table model name (Model_DbTable_{name})
	 * @param string $name
	 * @return string
	 */
	public function getTableName($name = null)
	{
		if(!$name)
			$name = $this->getName();
		return $this->formatClassName($name, 'table');
	}
	
	/**
	 * Get table model base name
	 * @param string $name
	 * @return string
	 */
	public function getTableBaseName($name = null)
	{
		if(!$name)
			$name = $this->getName();
		return $this->formatClassName($name, 'tbase');
	}
	
	/**
	 * Get model base name
	 * @param string $name
	 * @return string
	 */
	public function getBaseName($name = null)
	{
		if(!$name)
			$name = $this->getName();
		return $this->formatClassName($name, 'base');
	}
	
	/**
	 * Get name of a class extending base model
	 * @return string
	 */
	public function getBaseExtension()
	{
		return $this->container->getConfig()->pattern->base->extends;
	}
	
	/**
	 * Get name of a class extending table model base
	 * @return string
	 */
	public function getTableBaseExtension()
	{
		return $this->container->getConfig()->pattern->tbase->extends;
	}
	
	/**
	 * Get destination for model
	 */
	public function getModelFilePath()
	{
		$dir = $this->container->getConfig()->destination->model;
		if(!is_dir($dir)){
			mkdir($dir, null, true);
		}
		return $dir.DIRECTORY_SEPARATOR.$this->getFilename();
	}
	
	public function getBaseFilePath()
	{
		$dir = $this->container->getConfig()->destination->base;
		if(!is_dir($dir)){
			mkdir($dir, null, true);
		}
		return $dir.DIRECTORY_SEPARATOR.$this->getFilename();
	}
	
	public function getTableFilePath()
	{
		$dir = $this->container->getConfig()->destination->table;
		if(!is_dir($dir)){
			mkdir($dir, null, true);
		}
		return $dir.DIRECTORY_SEPARATOR.$this->getFilename();
	}
	
	public function getTableBaseFilePath()
	{
		$dir = $this->container->getConfig()->destination->tbase;
		if(!is_dir($dir)){
			mkdir($dir, null, true);
		}
		return $dir.DIRECTORY_SEPARATOR.$this->getFilename();
	}
	
	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->info['metadata'];
	}
	
	/**
	 * @param array $column
	 */
	public function getColumnMetadata($column)
	{
		return $this->info['metadata'][$column];
	}
	
	/**
	 * @return bool
	 */
	public function hasForeignKeys()
	{
		if(!empty($this->info['foreign_keys'])){
			return true;
		}
		return false;
	}
	
	/**
	 * @return array
	 */
	public function getForeignKeys()
	{
		if($this->hasForeignKeys()){
			return $this->info['foreign_keys'];
		}
		return null;
	}
	
	/**
	 * @return array
	 */
	public function getColumns()
	{
		return $this->info['cols'];
	}
	
	/**
	 * Get all avilable properties
	 * @return array
	 */
	public function getProperties()
	{
		$tmp = array();
		foreach ($this->getColumns() as $id => $name){
			$tmp[$name]['name'] = $name;
			$tmp[$name]['type'] = $this->getTypeFor($name);
			$tmp[$name]['desc'] = $this->getMysqlDatatype($name);
		}
		return $tmp;
	}
	
	/**
	 * Get mysql datatype for specified key
	 * @param string $key
	 * @return string
	 */
	public function getMysqlDatatype($key)
	{
		$meta = $this->getMetadata();
		return $meta[$key]['DATA_TYPE'];
	}
	
	/**
	 * @return bool
	 */
	public function hasUniqueKeys()
	{
		if(!empty($this->info['uniques'])){
			return true;
		}
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function isUniqueKey($column)
	{
		if(isset($this->info['uniques'][$column])){
			return true;
		}
		return false;
	}
	
	/**
	 * @return array|null
	 */
	public function getUniqueKeys()
	{
		if($this->hasUniqueKeys()){
			return $this->info['uniques'];
		}
		return null;
	}
	
	/**
	 * Get all primary keys
	 * @return array
	 */
	public function getPrimary()
	{
		return array_merge($this->info['primary'], array());
	}
	
	/**
	 * Get all primary keys in array notation
	 * @return string
	 */
	public function getPrimaryAsString()
	{
		$primary = $this->getPrimary();
		return 'array(\''.implode('\', \'', $primary).'\')';
	}
	
	/**
	 * Get all dependants
	 * @return string
	 */
	public function getDependantTables()
	{
		$tmp = array();
		$tables = $this->container->getDependencyChecker()->getChildrenOf($this->getName());
		if(is_array($tables)){
			foreach ($tables as $name => $smth){
				$tmp[] = $this->getTableName($name);
			}
			return $tmp;
		}else{
			return array();
		}
	}
	
	/**
	 * Get all dependants in array notation
	 * @return string
	 */
	public function getDependantAsString()
	{
		$tables = $this->getDependantTables();
		if(empty($tables)){ return 'array()'; }
		$impl = implode('\', ' . PHP_EOL . '		\'', $tables);
		return 'array('. PHP_EOL .'		\''
			.$impl.'\''.PHP_EOL.'	)';
	}
	
	/**
	 * Get column type
	 * @return string|null
	 */
	public function getTypeFor($column)
	{
		if(isset($this->info['phptypes'][$column])){
			return $this->info['phptypes'][$column];
		}
		return null;
	}
	
	public function getCustomVar()
	{
		
	}
	
	public function getCustomVars()
	{

	}
	
	/**
	 * Format class name
	 * @param string $name
	 * @param string $type model, table, base
	 * @return string camel cased underscored class name
	 */
	protected function formatClassName($name, $type)
	{
		switch ($type){
			case 'model':
				$pattern = $this->container->getConfig()->pattern->model->classname;
				break;
			case 'table':
				$pattern = $this->container->getConfig()->pattern->table->classname;
				break;
			case 'base':
				$pattern = $this->container->getConfig()->pattern->base->classname;
				break;
			case 'tbase':
				$pattern = $this->container->getConfig()->pattern->tbase->classname;
				break;
			
			default:
				throw new Exception
					('Cannot generate class name. Unkown type given: '.$type);
				break;
		}
		$className = str_ireplace('{table}', $this->formatUnderscoreToCamel($name), $pattern);
		return $className;
	}
	
	/**
	 * Format underscored string to CamelCased string
	 * @param string $string
	 * @return string
	 */
	protected static function formatUnderscoreToCamel($string)
	{
		$tmp = explode('_', $string);
		foreach ($tmp as &$id){$id = ucfirst($id);}$string = implode('',$tmp);
		return $string;
	}
	
	/**
	 * Format method name
	 * @param string $name
	 * @return string
	 */
	public function formatFunctionName($name)
	{
		return ucfirst($this->formatUnderscoreToCamel($name));
	}
	
	/**
	 * Format filename
	 * @param string $name
	 * @return string
	 */
	protected function formatFilename($name)
	{
		return $this->formatUnderscoreToCamel($name).'.php';
	}
}