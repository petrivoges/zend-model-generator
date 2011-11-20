<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */

@ob_end_flush();
chdir('..');
date_default_timezone_set('Europe/Warsaw');
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(getcwd().'./library'),
	get_include_path()
)));

require_once 'Generator.php';

try {
	
	if(!class_exists('Zend_Loader_Autoloader', false))
		require_once 'Zend/Loader/Autoloader.php';
		
	Zend_Loader_Autoloader::getInstance()
		->registerNamespace('Generator');
		
	$config = new Zend_Config_Ini('./mg/config.ini');
	$zg = new Generator($config);
	$zg->generate();
	
}catch (Exception $e){
	echo 'Exception cought!';
	echo $e->getMessage();
	echo $e->getFile().':'.$e->getLine();
	
}