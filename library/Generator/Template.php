<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */
class Generator_Template
{
	public function __construct()
	{
		
	}
	
	public function getRowBaseClass()
	{
		
	}
	
	public function getRowClass()
	{
		
	}
	
	protected function createMethod($name, $body)
	{
		$method = new Zend_CodeGenerator_Php_Method();
		$method->setName($name);
		return $method;
	}
	
	protected function createClass($name)
	{
		$class = new Zend_CodeGenerator_Php_Class();
		$class->setName($name);
		return $class;
	}
	
	protected function createFile()
	{
		
	}
	
}