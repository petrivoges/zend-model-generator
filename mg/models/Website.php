<?php

/**
 * Website model
 *
 * This file IS NOT automatically generated. You should edit it by yourself, adding needed methods.
 * You can use it to gain access to tables using getTable() method or to get an adapter
 * and perform transactions:
 *
 * Website_Model::getInstance()->getAdapter()->startTransaction();
 *
 * etc...
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */
class Model_Website extends Website_Model_Abstract
{

	/**
	 * Place any methods here.
	 * For example you have table "customer", and want to select the customer that is currently logged in.
	 * Create a method "customer()", inside this method gain access to "auth" plugin,
	 * get user id and do:
	 *
	 * return $this->getTable('customer')->findById($id);
	 *
	 * In controller:
	 *
	 * Website_Model::get()->customer();
	 *
	 * ... or extend Zend_Controller_Action:
	 *
	 * public function getModel(){ return Model_Website:get(); }
	 *
	 * ... and then in controller action simply do: $this->getModel()->customer();
	 *
	 *
	 */
	
}