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

try {
			
	Zend_Loader_Autoloader::getInstance()
		->registerNamespace('Generator');
		
	$config = new Zend_Config_Ini('./mg/config.ini');
	$zg = new Generator($config);
	$zg->log('Generator initialized, message logging enabled, config file loaded.');
	
	if($config->options->getset == true)
		$zg->log('Getters and setters methods WILL be created according to configuration.', Zend_Log::NOTICE);
	if($config->options->testMode == true)
		$zg->log('Test mode enabled. Files WILL NOT be created nor modified.', Zend_Log::DEBUG);
	if($config->options->ignoreErrors == true)
		$zg->log('Errors will be ignored.', Zend_Log::DEBUG);

		
	$time = $zg->getContainer()->getMicrotime();
	$zg->generate();
	$time = $zg->getContainer()->getMicrotime() - $time;
	$zg->log(sprintf('Operation completed in %s sec.', round($time, 3)));
	
	if($config->options->testMode == true)
		$zg->log('If everything went fine you can disable test mode in config file.', Zend_Log::DEBUG);
	
}catch (Exception $e){
	echo 'Exception cought!';
	echo $e->getMessage();
	echo $e->getFile().':'.$e->getLine();
	
}