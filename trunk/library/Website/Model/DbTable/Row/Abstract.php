<?php

/**
 * Abstract row model
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: Abstract.php 51 2011-08-06 00:00:13Z jacek $
 */
class Website_Model_DbTable_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
	// events
	
	const EVENT_POST_SAVE = 'postSave';
	const EVENT_POST_INSERT = 'postInsert';
	const EVENT_POST_UPDATE = 'postUpdate';
	const EVENT_POST_DELETE = 'postDelete';
	const EVENT_SAVE = 'save';
	const EVENT_INSERT = 'insert';
	const EVENT_UPDATE = 'update';
	const EVENT_DELETE = 'delete';

	/**
	 * Registered events
	 * @var array
	 */
	private $_events = array(
		self::EVENT_POST_SAVE	=> true,
		self::EVENT_POST_INSERT	=> true,
		self::EVENT_POST_UPDATE	=> true,
		self::EVENT_POST_DELETE	=> true,
		self::EVENT_SAVE		=> true,
		self::EVENT_INSERT		=> true,
		self::EVENT_UPDATE		=> true,
		self::EVENT_DELETE		=> true
	);
	
	/**
	 * Watchers
	 * @var array
	 */
	private $_eventWatchers = array();
	
	/**
	 * Cache memory
	 * @var array
	 */
	private $cachedMethods = array();
	
	/**
	 * Is cache enabled ?
	 * @var bool
	 */
	private $cacheEnabled = false;
	
	public function __construct(array $config = null)
	{
		parent::__construct($config);
	}
	
	/**
	 * Tell if row is new or was saved
	 * @return bool
	 */
	public function isNewRow()
	{
		if(empty($this->_cleanData)){
			return true;
		}
		return false;
	}
	
	/**
	 * Tell if row data was changed
	 * @param string $property Optional; if ommited whole row is checked for changes
	 * @deprecated use wasChanged() instead
	 * @return bool
	 */
	public function isChangedRow($property = null)
	{
		if(empty($this->_modifiedFields)){
			return false;
		}
		if($property !== null){
			return isset($this->_modifiedFields[$property]);
		}
		return true;
	}
	
	/**
	 * Tell if row data was changed. This is an alias for isChangedRow()
	 * @param string $property Optional; if ommited whole row is checked for changes
	 * @return bool
	 */
	public function wasChanged($property = null)
	{
		if(empty($this->_modifiedFields)){
			return false;
		}
		if($property !== null){
			return isset($this->_modifiedFields[$property]);
		}
		return true;
	}
	
	/**
	 * Return array with names of modified fields: propertyName => bool
	 * @return array
	 */
	public function getModifiedFields(){
		return $this->_modifiedFields;
	}
	
	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getAdapter()
	{
		return $this->getTable()->getAdapter();
	}
	
	/**
	 * @return Model_Website
	 */
	protected function getModel()
	{
		return Model_Website::get();
	}

	/**
	 * Enable runtime cache
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	public function disableCache()
	{
		$this->cacheEnabled = false;
		return $this;
	}
	
	/**
	 * Clean runtime cache
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	public function cleanCache()
	{
		$this->cachedMethods = array();
		return $this;
	}
	
	/**
	 * Enable runtime cache
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	public function enableCache()
	{
		$this->cacheEnabled = true;
		return $this;
	}
	
	/**
	 * Find cache if present
	 * @return mixed data or false
	 */
	protected function findCache($key){
		if(isset($this->cachedMethods[$key]))
			return $this->cachedMethods[$key];
		return false;
	}
	
	/**
	 * Write data to cache
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	protected function writeCache($key, $data)
	{
		$this->cachedMethods[$key] = $data;
		return $this;
	}
	
	/**
	 * @return Service_Brooker
	 */
	protected function getService()
	{
		return Service_Brooker::getInstance();
	}
	
	/**
	 * Add new event
	 * @param string $name
	 * @return Model_Abstract
	 */
	public function addEvent($name)
	{
		if($this->eventExists($name)){
			throw new Website_Model_Exception
				('Event : '.$name.' already exists.');
		}
		$this->_events[$name] = true;
		return $this;
	}
	
	/**
	 * Checks if event exists within the stack
	 * @param string $name
	 * @return bool
	 */
	public function eventExists($name)
	{
		if(array_key_exists($name, $this->_events)){
			return true;
		}
		return false;
	}
	
	/**
	 * Trigger event.
	 *
	 * @param string $name
	 * @return
	 */
	public function triggerEvent($name)
	{
		if(!$this->eventExists($name)){
			throw new Website_Model_Exception
				('Cannot trigger event. Event : '.$name.' does not exists.');
		}
		if(isset($this->_eventWatchers[$name])){
			$eventWatchers =& $this->_eventWatchers[$name];
			try {
				foreach ( $eventWatchers as $id => $watcher){
					
					$obj = $watcher['object'];
					$method = $watcher['method'];
					$params = $watcher['params'];
					
					if($watcher['running'] == true){
						throw new Website_Model_Exception
							('Triggered event "'.$name.'" felt into an infinite '
							.'loop due to watcher #'.$id.' of type '.get_class($obj).'::'.$method.'.');
					}else{
						$eventWatchers[$id]['running'] = true; // set flag
					}
					
					/**
					 * This can possibly cause a infinite loop
					 * Make sure to NOT trigger the trigger inside
					 * triggered function :)
					 */
					if($watcher['onetimer'] == true){
						unset($eventWatchers[$id]);
					}
					
					if($obj instanceof Zend_Db_Table_Rowset){
						foreach ($obj as $id => $rowset){
							if(method_exists($obj, $method)){
								call_user_func_array(array($rowset, $method), $params);
							}
						}
					}else{
						if(is_object($obj) && method_exists($obj, $method)){
							call_user_func_array(array($obj, $method), $params);
						}else{
							call_user_func_array(array($obj, '::'.$method), $params);
						}
					}
				}
			}catch (Exception $e){
				throw new Website_Model_Exception
					('Exception cought while triggering event: '.$name
					.'. Message: '.$e->getMessage());
			}
		}
		return $this;
	}
	
	/**
	 * Add event watcher
	 * @param object $object
	 * @param string $method
	 * @param string $event
	 * @param array $params
	 * @param bool $oneTime Trigger event once and remove the watcher
	 * @return Model_Abstract
	 */
	public function addEventWatcher($object, $method, $event, array $params = null, $oneTime = true)
	{
		if($params == null)
			$params = array();
			
		if(!method_exists($object, $method) && !($object instanceof Zend_Db_Table_Rowset)){
			throw new Website_Model_Exception
			('Cannot add event watcher. Method: '.$method
			.' for class: '.get_class($object).' does not exists.');
		}
		
		if(!array_key_exists($event, $this->_events))
			throw new Website_Model_Exception('Event "'.$event.'" does not exists.');
		
		$this->_eventWatchers[$event][] = array(
			'object' => $object,
			'method' => $method,
			'onetimer' => $oneTime,
			'running' => false,
			'params' => $params,
		);
		return $this;
	}
	
	/**
	 * Add event watchers
	 *
	 * @param array $watchers
	 * @param string $method
	 * @param string $event
	 * @param bool $oneTime Trigger event once and remove the watcher
	 * @return Model_Abstract
	 */
	public function addEventWatchers(array $watchers, $method, $event, $oneTime = true)
	{
		foreach ($watchers as $id => $watcher){
			$this->addEventWatcher($watcher, $method, $event, null, $oneTime);
		}
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getEventWatchers()
	{
		return $this->_eventWatchers;
	}
	
	/**
	 * Events
	 */
	
	/**
	 * Pre-save event
	 */
	protected function _save()
	{
		$this->triggerEvent('save');
	}
	
	/**
	 * Post-save event
	 */
	protected function _postSave()
	{
		$this->triggerEvent('postSave');
	}
	
	/**
	 * Post-delete event
	 */
	protected function _postDelete()
	{
		$this->triggerEvent('postDelete');
	}
	
	/**
	 * Post-update
	 */
	protected function _postUpdate()
	{
		$this->triggerEvent('postUpdate');
		$this->_postSave();
	}
	
	/**
	 * Post-insert
	 * @todo remove post insert hack after ZF-9675 issue will be fixed
	 * @see http://framework.zend.com/issues/browse/ZF-9675
	 */
	protected function _postInsert()
	{
		// remove start
		$this->refresh();
		// remove end
		$this->triggerEvent('postInsert');
		$this->_postSave();
	}
	
	/**
	 * Pre-insert
	 */
	protected function _insert()
	{
		$this->triggerEvent('insert');
		$this->_save();
	}
	
	/**
	 * Pre-update
	 */
	protected function _update()
	{
		$this->triggerEvent('update');
		$this->_save();
	}
	
	/**
	 * Pre-delete
	 */
	protected function _delete()
	{
		$this->triggerEvent('delete');
	}
	
	/**
	 * Eof events
	 */
	/**
	 * Watch row and update it on parent's post-save
	 * @param Website_Db_Table_Row $row
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	public function watchNewRow(Website_Db_Table_Row $row)
	{
		$this->addEventWatcher($row, 'save', self::EVENT_POST_SAVE, null, false);
		return $this;
	}
	
	/**
	 * Watch child row and update it on parent's post-save
	 * @param Website_Db_Table_Row $row
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	public function watchChildRow(Website_Db_Table_Row $row)
	{
		$this->addEventWatcher($row, 'save', self::EVENT_POST_SAVE, null, false);
		return $this;
	}
		
	/**
	 * Watch children rowset and update it on parent's post-save
	 * @param Zend_Db_Table_Rowset_Abstract $rowset
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	public function watchChildrenRowset(Zend_Db_Table_Rowset_Abstract $rowset)
	{
		$this->addEventWatcher($rowset, 'save', self::EVENT_POST_SAVE, null, false);
		return $this;
	}
	
	/**
	 * Watch parent row and update it before current object's save
	 * @param Website_Db_Table_Row $row
	 * @return Website_Model_DbTable_Row_Abstract
	 */
	public function watchParentRow(Website_Db_Table_Row $row)
	{
		$this->addEventWatcher($row, 'save', self::EVENT_SAVE, null, false);
		return $this;
	}
	
	/**
	 * Find parent row from parent $table for the current row.
	 *
     * @param string|Zend_Db_Table_Abstract $parentTable
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @param string                        OPTIONAL $ruleKey
     * @return Zend_Db_Table_Row_Abstract   Query result from $parentTable
     * @throws Zend_Db_Table_Row_Exception If $parentTable is not a table or is not loadable.
	 */
	public function findParentRow(
		$parentTable, $where = null, $order = null, $count = null, $offset = null,
		$ruleKey = null)
	{
		$cacheKey = md5(@serialize(func_get_args()));
		
		if( ($result = $this->findCache($cacheKey)) === false ){
			if(! ($where instanceof Zend_Db_Table_Select)){
				$select = $this->select();
			}else {
				$select = $where;
			}
			
			if($where !== null){
				$this->_where($select, $where);
			}
			if($order !== null){
				$this->_order($select, $order);
			}
			if($count !== null || $offset !== null){
				$select->limit($count, $offset);
			}
			
			$result = parent::findParentRow($parentTable, $ruleKey, $select);
			
			$this->writeCache($cacheKey, $result);
		}
		return $result;
	}
	
	/**
	 * Find children in given $table that is in a child->parent relation with the current row.
	 *
     * @param string|Zend_Db_Table_Abstract  $dependentTable
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
	 * @param string                         OPTIONAL $ruleKey
     * @return Zend_Db_Table_Rowset_Abstract Query result from $dependentTable
     * @throws Zend_Db_Table_Row_Exception If $dependentTable is not a table or is not loadable.
     * @todo add cache
	 */
	public function findDependentRowset(
		$dependentTable, $where = null, $order = null, $count = null,
		$offset = null, $ruleKey = null)
	{

		if(! ($where instanceof Zend_Db_Table_Select)){
			$select = $this->select();
		}else {
			$select = $where;
		}
		
		if($where !== null){
			$this->_where($select, $where);
		}
		if($order !== null){
			$this->_order($select, $order);
		}
		if($count !== null || $offset !== null){
			$select->limit($count, $offset);
		}
		
		$result = parent::findDependentRowset($dependentTable, $ruleKey, $select);

		return $result;
	}
	
	/**
	 * findMany2many.
	 *
     * @param  string|Zend_Db_Table_Abstract  $matchTable
     * @param  string|Zend_Db_Table_Abstract  $intersectionTable
     * @param  string                         OPTIONAL $callerRefRule
     * @param  string                         OPTIONAL $matchRefRule
     * @param  Zend_Db_Table_Select           OPTIONAL $select
     * @return Zend_Db_Table_Rowset_Abstract Query result from $matchTable
     * @throws Zend_Db_Table_Row_Exception If $matchTable or $intersectionTable is not a table class or is not loadable.
     */
	public function _findManyToManyRowset(
		$matchTable, $intersectionTable, $callerRefRule = null,
        $matchRefRule = null, Zend_Db_Table_Select $select = null)
    {
    	$result = parent::findManyToManyRowset(
    		$matchTable,
    		$intersectionTable,
    		$callerRefRule,
    		$matchRefRule,
    		$select);

    	return $result;
	}
	
	/**
     * Fetches all rows using intersection table.
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
 	 * @param  string|Zend_Db_Table_Abstract  	$matchTable
     * @param  string|Zend_Db_Table_Abstract  	$intersectionTable
     * @param  string                         	OPTIONAL $callerRefRule
     * @param  string                         	OPTIONAL $matchRefRule
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
	public function findManyToManyRowset(
		$where = null, $order = null, $count = null, $offset = null, $matchTable,
		$intersectionTable, $callerRefRule = null, $matchRefRule = null
	){

		if(! ($where instanceof Zend_Db_Table_Select)){
			$select = $this->select();
		}else {
			$select = $where;
		}
		
		if($where !== null){
			$this->_where($select, $where);
		}
		if($order !== null){
			$this->_order($select, $order);
		}
		if($count !== null || $offset !== null){
			$select->limit($count, $offset);
		}
		
		$result = $this->_findManyToManyRowset
			($matchTable, $intersectionTable, $callerRefRule, $matchRefRule, $select);

		return $result;
	}
	
	/**
     * Generate WHERE clause from user-supplied string or array
     *
     * @param  string|array $where  OPTIONAL An SQL WHERE clause.
     * @return Zend_Db_Table_Select
     */
    protected function _where(Zend_Db_Table_Select $select, $where)
    {
        $where = (array) $where;
        foreach ($where as $key => $val) {
            // is $key an int?
            if (is_int($key)) {
                // $val is the full condition
                $select->where($val);
            } else {
                // $key is the condition with placeholder,
                // and $val is quoted into the condition
                $select->where($key, $val);
            }
        }
        return $select;
    }

    /**
     * Generate ORDER clause from user-supplied string or array
     *
     * @param  string|array $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Table_Select
     */
    protected function _order(Zend_Db_Table_Select $select, $order)
    {
        if (!is_array($order)) {
            $order = array($order);
        }
        foreach ($order as $val) {
            $select->order($val);
        }
        return $select;
    }
	
    /**
     * Saves the properties to the database.
     * Use events to interact with object saving/updating.
     * This performs an intelligent insert/update, and reloads the
     * properties with fresh data from the table on success.
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
	final public function save()
	{
		return parent::save();
	}
}