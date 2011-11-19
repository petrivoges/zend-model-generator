<?php

/**
 * Configuration file for Model Generator
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */

return array(

/**
 * PDO adapter settings
 */

'db' => array(
	'dbname' 	=> 'mgtest',
	'host' 		=> 'localhost',
	'port' 		=> '3306',
	'username' 	=> 'php',
	'password' 	=> '',
	'charset' 	=> 'UTF-8',
),
	
/**
 * Generator options
 */
'generator' => array(
	'templates' => array(
		
	),
	// directory where results will be saved
	'target' => array(
		'models' 	=> './models',
		'tables' 	=> './models/DbTable',
		'base' 		=> './models/Base',
		'btables' 	=> './models/Base/DbTable'
	),
	// name patterns
	'pattern' => array(
		'model' => array(
			'classname' => 'Model_{table}',
			//'extends' => 'Zend_Db_Table_Row',
		),
		'table' => array(
			'classname' => 'Model_DbTable_{table}',
			
		),
		'base' => array(
			'classname' => 'Model_Base_{table}',
			'extends' => 'Model_Base_Abstract',
		),
		
		'tbase' => array(
			'classname' => 'Model_Base_DbTable_{table}',
			'extends' => 'Model_DbTable_Abstract',
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