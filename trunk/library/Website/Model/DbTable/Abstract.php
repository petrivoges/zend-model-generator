<?php

/**
 * Abstract table model
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: Abstract.php 53 2011-09-30 06:09:00Z jacek $
 */
class Website_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * @return string Table name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * This function should not be used too often.
	 * @return string shema name
	 */
	public function getSchemaName()
	{
		$cfg = $this->getAdapter()->getConfig();
		$name = isset($cfg['dbname']) ? $cfg['dbname'] : null;
		if(! empty($name)){
			return $name;
		}else{
			return $this->getAdapter()->fetchOne("select DATABASE();");
		}
	}

	/**
	 * @return Service_Brooker
	 */
	public function getService()
	{
		return Service_Brooker::getInstance();
	}

	/**
	 * @return Model_Website
	 */
	public function getModel()
	{
		return Model_Website::get();
	}

	/**
	 * This funcion should not be used. You CAN NOT rely on its results.
	 * @deprecated
	 * @return int or NULL
	 */
	public function getNextAutoIncrement()
	{
		try{
			$id = $this->getAdapter()->fetchOne(
			'SELECT AUTO_INCREMENT FROM information_schema.TABLES'
			. ' WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?',
			array($this->getName(), $this->getSchemaName()));
		}catch(Exception $e){
			return null;
		}
		return $id;
	}

	/**
	 * Find row by primary key
	 * @param int|array $value
	 * @return Zend_Db_Table_Row_Abstract
	 */
	/*public function find($value)
	{
		return parent::find($value)->current();
	}*/
	
	/**
	 * Find one record
	 * @param string|array $where
	 * @param string $orderBy
	 * @return Zend_Db_Table_Row_Abstract or NULL
	 */
	public function findOne($where = null, $orderBy = null)
	{
		return $this->fetchRow($where, $orderBy);
	}

	/**
	 * Find all records by specified condition
	 * @param string|array $where
	 * @param string $orderBy
	 * @param int $limit
	 * @param int $offset
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function findAll($where = null, $orderBy = null, $limit = null, $offset = null)
	{
		return $this->fetchAll($where, $orderBy, $limit, $offset);
	}

	/**
	 * Count rows
	 * @param string|array $where
	 * @return int
	 */
	public function count($where = null)
	{
		$select = $this->select();
		$select->from($this->_name,'COUNT(*) AS num');
		$select = $this->_where($select, $where);
        return $this->fetchRow($select)->num;
	}
	
}