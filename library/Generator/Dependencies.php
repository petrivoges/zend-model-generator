<?php

/**
 * Dependency checker
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */
class Generator_Dependencies
{
	public $dependencies = array();

	/**
	 * Set current $table as a child for $fTable
	 *
	 * @param string $table Table
	 * @param string $col Table key
	 * @param string $keyName	Table fkey name
	 * @param string $fTable Parent table
	 * @param string $fkey Parent key
	 */
	public function isChild($table, $col, $key, $fTable, $fkey)
	{
		$this->addParent($table, $col, $key, $fTable, $fkey);
		$this->addChild($fTable, $fkey, $table, $col, $key);
	}
	
	/**
	 * Add child
	 * @param unknown_type $table
	 * @param unknown_type $col
	 * @param unknown_type $childTable
	 * @param unknown_type $childCol
	 * @param unknown_type $childKey
	 */
	protected function addChild($table, $col, $childTable, $childCol, $childKey)
	{
		$this->dependencies[$table]['children'][] = array(
			'col' => $col,
			'childKey' => $childKey,
			'childCol' => $childCol,
			'child' => $childTable,
		);
	}
	
	/**
	 * Add parent
	 * @param unknown_type $table
	 * @param unknown_type $col
	 * @param unknown_type $key
	 * @param unknown_type $parentTable
	 * @param unknown_type $parentKey
	 */
	protected function addParent($table, $col, $key, $parentTable, $parentCol)
	{
		$this->dependencies[$table]['parents'][] = array(
			'parent' => $parentTable,
			'key' => $key,
			'col' => $col,
			'parentCol' => $parentCol
		);
	}
	
	/**
	 * Get table children
	 * @param string $tableName
	 * @return array
	 */
	public function getChildrenOf($tableName)
	{
		if(isset($this->dependencies[$tableName]['children'])){
			return $this->dependencies[$tableName]['children'];
		}else{
			return array();
		}
	}
	
	/**
	 * Get table parents
	 * @param string $tableName
	 * @return array
	 */
	public function getParentsOf($tableName)
	{
		if(isset($this->dependencies[$tableName]['parents'])){
			return $this->dependencies[$tableName]['parents'];
		}else{
			return array();
		}
	}
	
	/**
	 * Get dependencies for specified table
	 * @param string $tableName
	 * @return array
	 */
	public function getDependenciesFor($tableName)
	{
		if(!empty($this->dependencies[$tableName])){
			return $this->dependencies[$tableName];
		}
		return null;
	}
	
	/**
	 * Return dependencies as an array
	 * @return array
	 */
	public function toArray()
	{
		return $this->dependencies;
	}
}