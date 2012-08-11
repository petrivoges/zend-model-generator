<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */
class Website_Model_DbTable_Cache
{
	/**
	 * @var array
	 */
	protected static $storage = array();

	/**
	 * @var bool
	 */
	protected static $isEnabled = true;

	/**
	 * @var int
	 */
	protected static $hits = 0;

	/**
	 * @var string
	 */
	protected $prefix = 'default_';

	public function __construct()
	{}

	/**
	 * @static
	 * @return array
	 */
	public static function getStorage()
	{
		return self::$storage;
	}

	/**
	 * Find and return cache entry
	 * @param string $key
	 * @return false|mixed|Zend_Db_Table_Rowset_Abstract|Website_Model_DbTable_Row_Abstract
	 */
	public function get($key)
	{
		if(!$this->isEnabled())
			return false;

		if(isset(self::$storage[$key])){
			self::$hits++;
			$object = self::$storage[$key];
			return $object;
		}
		return false;
	}

	/**
	 * @param $key
	 * @param $data
	 */
	public function set($key, $data)
	{
		self::$storage[$key] = $data;
	}

	/**
	 * @return int
	 */
	public function getHits()
	{
		return self::$hits;
	}

	/**
	 * @static
	 * @return bool
	 */
	public function isEnabled()
	{
		return self::$isEnabled;
	}

	/**
	 * @static
	 * @param $bool
	 * @return bool
	 */
	public function setIsEnabled($bool = true)
	{
		return self::$isEnabled = (bool) $bool;
	}

	/**
	 * @param Website_Model_DbTable_Row_Abstract $row
	 * @return bool
	 */
	public function isRowModified(Website_Model_DbTable_Row_Abstract $row)
	{
		return $row->wasChanged();
	}

	/**
	 * @param Traversable|Website_Model_DbTable_Row_Abstract[] $rowset
	 * @return bool
	 */
	public function isRowsetModified(Zend_Db_Table_Rowset_Abstract $rowset)
	{
		$flag = false;
		foreach($rowset as $row){
			if($row->wasChanged()){
				$flag = true;
			}
		}
		$rowset->rewind();
		return $flag;
	}

	/**
	 * Generate cache key from given param(s)
	 * @param $mixedArguments
	 * @param null $methodName
	 * @return string
	 */
	public function generateKey($mixedArguments, $methodName = null)
	{
		if(!is_array($mixedArguments)){
			$mixedArguments = array($mixedArguments);
		}

		$strings = array();

		foreach($mixedArguments as $object){
			if($object instanceof Zend_Db_Select){
				$strings[] = $object->__toString();
			}else{
				$strings[] = serialize($object);
			}
		}
		return $methodName . '_' . md5(implode('',$strings));
	}
}