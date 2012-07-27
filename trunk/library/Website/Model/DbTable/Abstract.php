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
	 * @var Website_Model_DbTable_Cache
	 */
	protected $_cache;

	public function init()
	{
		parent::init();
		$this->_cache = new Website_Model_DbTable_Cache();
	}

	/**
	 * @return Website_Model_DbTable_Cache
	 */
	public function getRuntimeCache()
	{
		return $this->_cache;
	}

	/**
	 * @return string Table name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * This function should not be used too often.
	 * @todo optimize
	 * @deprecated
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
		$serviceBrookerClass = 'Service_Brooker';
		return $serviceBrookerClass::getInstance();
	}

	/**
	 * @return Model_Website
	 */
	public function getModel()
	{
		$modelClass = 'Model_Website';
		return $modelClass::get();
	}

	/**
	 * @deprecated
	 */
	public function getNextAutoIncrement()
	{
		throw new Exception('Method '.__CLASS__.'::'.__METHOD__.' is not supported anymore.');
	}
	
	/**
	 * Find exactly one record
	 * @param string|array $where
	 * @param string $orderBy
	 * @return Zend_Db_Table_Row_Abstract|NULL
	 */
	public function findOne($where = null, $orderBy = null)
	{
		return $this->fetchRow($where, $orderBy);
	}

	/**
	 * Find all records by specified condition
	 * @param string|array|Zend_Db_Table_Select $where
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
	 * Fetches all rows.
	 * Honors the Zend_Db_Adapter fetch mode.
	 * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
	 * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
	 * @param int                               $count  OPTIONAL An SQL LIMIT count.
	 * @param int                               $offset OPTIONAL An SQL LIMIT offset.
	 * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
	 */
	public function fetchAll($where = null, $order = null, $count = null, $offset = null)
	{
		if (!($where instanceof Zend_Db_Table_Select)) {
			$select = $this->select();
			if ($where !== null)
				$this->_where($select, $where);
			if ($order !== null)
				$this->_order($select, $order);
			if ($count !== null || $offset !== null)
				$select->limit($count, $offset);
		} else {
			$select = $where;
		}

		$cacheKey = md5($select->assemble());
		if( ($rowset = $this->getRuntimeCache()->get( $cacheKey )) === false){
			$rows = $this->_fetch($select);
			$data = array(
				'table' => $this,
				'data' => $rows,
				'readOnly' => $select->isReadOnly(),
				'rowClass' => $this->getRowClass(),
				'stored' => true
			);

			$rowsetClass = $this->getRowsetClass();
			if (!class_exists($rowsetClass)) {
				require_once 'Zend/Loader.php';
				Zend_Loader::loadClass($rowsetClass);
			}

			$rowset = new $rowsetClass($data);
			$this->getRuntimeCache()->set($cacheKey, $rowset);
		}
		return $rowset;
	}

	/**
	 * Fetches one row in an object of type Zend_Db_Table_Row_Abstract,
	 * or returns null if no row matches the specified criteria.
	 * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
	 * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
	 * @param int                               $offset OPTIONAL An SQL OFFSET value.
	 * @return Zend_Db_Table_Row_Abstract|null The row results per the
	 *     Zend_Db_Adapter fetch mode, or null if no row found.
	 */
	public function fetchRow($where = null, $order = null, $offset = null)
	{
		if (!($where instanceof Zend_Db_Table_Select)) {
			$select = $this->select();
			if ($where !== null)
				$this->_where($select, $where);
			if ($order !== null)
				$this->_order($select, $order);
			$select->limit(1, ((is_numeric($offset)) ? (int)$offset : null));
		} else {
			$select = $where->limit(1, $where->getPart(Zend_Db_Select::LIMIT_OFFSET));
		}

		$cacheKey = md5($select->assemble());
		if( ($row = $this->getRuntimeCache()->get( $cacheKey )) === false){

			$rows = $this->_fetch($select);
			if (count($rows) == 0) return null;

			$data = array(
				'table' => $this,
				'data' => $rows[0],
				'readOnly' => $select->isReadOnly(),
				'stored' => true
			);

			$rowClass = $this->getRowClass();
			if (!class_exists($rowClass)) {
				require_once 'Zend/Loader.php';
				Zend_Loader::loadClass($rowClass);
			}
			$row = new $rowClass($data);
			$this->getRuntimeCache()->set($cacheKey, $row);
		}
		return $row;
	}

	/**
	 * Count rows
	 * @param string|array $where
	 * @return int
	 */
	public function count($where = null)
	{
		return $this->getAdapter()
			->fetchOne( $this->_where($this->select()->from($this->_name,'COUNT(*) AS num'), $where) );
	}
}