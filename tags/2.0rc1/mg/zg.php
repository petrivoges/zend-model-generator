<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */

header('Content-type: text/plain; charset=utf-8');

@ob_end_flush();
chdir('..');
date_default_timezone_set('Europe/Warsaw');
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(getcwd().'./library'),
	get_include_path()
)));

require_once 'Generator.php';

if(!class_exists('Zend_Loader_Autoloader', false))
		require_once 'Zend/Loader/Autoloader.php';
	
Zend_Loader_Autoloader::getInstance()
	->registerNamespace('Generator');
	
$config = new Zend_Config_Ini('./mg/config.ini');
$zg = new Generator($config);
$zg->log('Generator initialized, message logging enabled, config file loaded.');
$zg->log('Starting ...');

try {
	
	$time = time();
	$zg->generate();
	$time = time() - $time;
	$zg->log(sprintf('Operation completed in %s sec.', $time));
	
}catch (Exception $e){
	echo 'Exception cought!';
	echo $e->getMessage();
	echo $e->getFile().':'.$e->getLine();
	
}