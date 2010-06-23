<?php

return array(

	// db details
	'db' => array(
		'dbname' 	=> 'skajdo',
		'host' 		=> 'localhost',
		'port' 		=> '3306',
		'username' 	=> 'php',
		'password' 	=> '',
		'charset' 	=> 'UTF-8',
	),
	
	// generator options
	'generator' => array(
		'templates' => array(
			
		),
		// directory where results will be saved
		'dir' => array(
			'models' 	=> './models',
			'base' 		=> './models/Base',
			'tables' 	=> './models/DbTable'
		),
		// name patterns
		'pattern' => array(
			'model' => array(
				'classname' => 'Model_{table}',
				//'extends' => 'Zend_Db_Table_Row',
			),
			'table' => array(
				'classname' => 'Model_DbTable_{table}',
				'extends' => 'Model_DbTable_Abstract',
			),
			'base' => array(
				'classname' => 'Model_Base_{table}',
				'extends' => 'Model_Base_Abstract',
			),
		),
		// custom variables 
		'custom' => array(
			'author' 	=> 'Jacek Kobus',
			'email' 	=> 'kobus.jacek@gmail.com',
			'copyright' => 'http://jacekkobus.com',
			'license' 	=> 'Licensed under: http://sam.zoy.org/wtfpl/COPYING',
			'subPackage' 	=> 'Model',
			'package' 	=> 'Skajdo',
		),
	)
	
);