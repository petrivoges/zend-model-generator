<?php

@ob_end_flush();
date_default_timezone_set('Europe/Warsaw');

set_include_path(implode(PATH_SEPARATOR, array(
	realpath(getcwd().'/mg/library'),
	get_include_path()
)));

require_once 'Generator.php';
try {
	$config = include 'config.php';
	$zg = new Generator($config);
	$zg->generate();
}catch (Exception $e){

	echo 'Exception cought!';
	echo $e->getMessage();
	echo $e->getFile().':'.$e->getLine();
	
}