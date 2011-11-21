<?php

/**
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id$
 */
class Generator_Template
{
	/**
	 * @var Generator_Container
	 */
	protected $container;
	
	public function __construct(Generator_Container $container)
	{
		$this->container = $container;
	}
	
	public function make(Generator_Table $table)
	{
		$templates = array(
			'longClassDescription' =>
				'This class has been generated automatically by Jack\'s Model Generator.' . PHP_EOL
				. 'More info can be found at: http://blog.jacekkobus.com' . PHP_EOL
				. 'Source code & issue tracker: http://zend-model-generator.code.google.com',
			'tags' => array(),
		);
		
		foreach ($this->container->getConfig()->custom as $tag => $value)
			$templates['tags'][] = array('name' => $tag, 'description' => $value);
		
		////////////////////////////////////////////
		// create model
		////////////////////////////////////////////
		
		$modelClass = new Zend_CodeGenerator_Php_Class(array(
			'name' => $table->getModelName(),
			'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
				'shortDescription' => $table->getModelName() . PHP_EOL . 'Put your custom methods in this file.',
				'longDescription' => $templates['longClassDescription'],
				'tags' => $templates['tags'],
			)),
			'extendedClass' => $table->getBaseName(),
		));
		
		$modelFile = new Zend_CodeGenerator_Php_File(array(
			'classes' => array($modelClass),
		));
		
		// save
		if(!file_exists($table->getModelFilePath()))
			file_put_contents($table->getModelFilePath(), $modelFile->generate());
				
		////////////////////////////////////////////
		// create model base
		////////////////////////////////////////////
		
		$methods = array();
		
		$tmp = array();
		foreach ($table->getProperties() as $property)
			$tmp[] = array('name' => 'property', 'description' => $property['type'] . ' $'.$property['name'].' '.$property['desc']);
		
		$tmp[] = array('name' => 'method', 'description' => $table->getTableName() . ' getTable()');
			
		// create methods
		foreach ($table->getChildren() as $child){
			$methodName = $child['child'];
			$pattern = '#^'.$table->getName().'_(.*)$#i';
			if(preg_match($pattern, $methodName)){
				$methodName = preg_replace($pattern, '\\1', $methodName);
			}
			$methodName = $table->formatFunctionName($methodName);
			
			// method 1 create
			
			$methods[] = new Zend_CodeGenerator_Php_Method(array(
				'name' => 'create'.$methodName,
				'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
					'shortDescription' => 'Create new '.$table->getModelName($child['child']),
					'tags' => array(
						 new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
						 	'paramName' => 'data', 'dataType' => 'array'
						)),
						array('name' => 'return', 'description' => $table->getModelName($child['child'])),
					),
				)),
				'parameters' => array(
					array('name' => 'data'),
				),
				'body' =>
					'$data[\''.$child['childCol'].'\'] = $this->'.$child['col'] . ';' . PHP_EOL
					.	'$table = new '.$table->getTableName($child['child']) . '();' . PHP_EOL
					.	'return $table->createRow($data);',
			));
			
			// method 2 find
			
			$methods[] = new Zend_CodeGenerator_Php_Method(array(
				'name' => 'find'.$methodName,
				'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
					'shortDescription' => 'Find dependent '.$table->getModelName($child['child']),
					'tags' => array(
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'where', 'dataType' => 'string|array|Zend_Db_Table_Select',
						)),
						
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'order', 'dataType' => 'string|array',
						)),
						
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'count', 'dataType' => 'int',
						)),
						
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'offset', 'dataType' => 'int',
						)),
						
						array('name' => 'return', 'description' => 'Zend_Db_Table_Rowset_Abstract'),
					),
				)),
				'parameters' => array(
					array('name' => 'where = null'), // @todo must be null
					array('name' => 'order = null'),
					array('name' => 'count = null'),
					array('name' => 'offset = null'),
				),
				'body' => 'return $this->findDependentRowset(\''.$table->getTableName($child['child']).'\', $where, $order, $count, $offset);',
			));
			
			// method 3 delete
			
			$methods[] = new Zend_CodeGenerator_Php_Method(array(
				'name' => 'delete'.$methodName,
				'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
					'shortDescription' => 'Delete all dependant '.$table->getModelName($child['child']).' matching condition.',
					'tags' => array(
						 new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
						 	'paramName' => 'where', 'dataType' => 'array'
						)),
						array('name' => 'return', 'description' => 'int Number of deleted rows'),
					),
				)),
				'parameters' => array(
					array('name' => 'where = array()'),
				),
				'body' =>
					'$where[\''.$child['childCol'].'\'] = $this->'.$child['col'] . ';' . PHP_EOL
					.	'$table = new '.$table->getTableName($child['child']) . '();' . PHP_EOL
					.	'return $table->delete($where);',
			));
		}
		
		// parents
		
		foreach ($table->getParents() as $parent){
			$methodName = $parent['parent'];
			
			$children = $table->getChildren();
			
			// if there is a child table with the same name add "parent" prefix
			if(isset($children[$methodName])){
					$methodName = 'parent_'.$methodName;
			}
			
			$pattern = '#^'.$table->getName().'_(.*)$#i';
			if(preg_match($pattern, $methodName)){
				$methodName = preg_replace($pattern, '\\1', $methodName);
			}
			
			$methodName = $table->formatFunctionName($methodName);
			
			$methods[] = new Zend_CodeGenerator_Php_Method(array(
				'name' => 'find'.$methodName,
				'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
					'shortDescription' => 'Find parent '.$table->getModelName($parent['parent']),
					'tags' => array(
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'where', 'dataType' => 'string|array|Zend_Db_Table_Select',
						)),
						
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'order', 'dataType' => 'string|array',
						)),
						
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'count', 'dataType' => 'int',
						)),
						
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'offset', 'dataType' => 'int',
						)),
						
						array('name' => 'return', 'description' => 'Zend_Db_Table_Rowset_Abstract'),
					),
				)),
				'parameters' => array(
					array('name' => 'where = null'), // @todo must be null
					array('name' => 'order = null'),
					array('name' => 'count = null'),
					array('name' => 'offset = null'),
				),
				'body' => 'return $this->findDependentRowset(\''.$table->getTableName($parent['parent']).'\', $where, $order, $count, $offset);',
			));
		}
		
		$modelBase = new Zend_CodeGenerator_Php_Class(array(
			'name' => $table->getBaseName(),
			'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
				'shortDescription' => $table->getBaseName() . PHP_EOL . '*DO NOT* edit this file.',
				'longDescription' => $templates['longClassDescription'],
				'tags' => array_merge($tmp, $templates['tags']),
			)),
			'extendedClass' => $table->getBaseExtension(),
			'methods' => $methods,
		));
		
		$modelBaseFile = new Zend_CodeGenerator_Php_File(array(
			'classes' => array($modelBase),
		));
		
		// save
		file_put_contents($table->getBaseFilePath(), $modelBaseFile->generate());
		
		////////////////////////////////////////////
		// create table
		////////////////////////////////////////////
		
		$modelTable = new Zend_CodeGenerator_Php_Class(array(
			'name' => $table->getTableName(),
			'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
				'shortDescription' => $table->getTableName() . PHP_EOL . 'Put your custom methods in this file.',
				'longDescription' => $templates['longClassDescription'],
				'tags' => array_merge($templates['tags']),
			)),
			'extendedClass' => $table->getTableBaseName(),
		));
		
		$modelTableFile = new Zend_CodeGenerator_Php_File(array(
			'classes' => array($modelTable),
		));
		
		// save
		if(!file_exists($table->getTableFilePath()))
			file_put_contents($table->getTableFilePath(), $modelTableFile->generate());
		
		////////////////////////////////////////////
		// create table base
		////////////////////////////////////////////
		
		
		$methods = array();
		$tableReferences = array();
		
		foreach ($table->getUniqueKeys() as $key){

			$methods[] = new Zend_CodeGenerator_Php_Method(array(
				'name' => 'findBy'.$table->formatFunctionName($key),
				'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
					//'shortDescription' => 'Find row by '.$table->getModelName($parent['parent']),
					'tags' => array(
						new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
							'paramName' => 'value', 'dataType' => 'mix',
						)),
						array('name' => 'return', 'description' => 'Zend_Db_Table_Row'),
					),
				)),
				'parameters' => array(
					array('name' => 'value'),
				),
				'body' => 'return $this->findOne(array(\''.$key.' = ?\' => $value));',
			));
		}
		
		foreach ($table->getParents() as $parent){
			$tableReferences[$parent['key']] = array(
				'columns' => $parent['col'],
				'refTableClass' => $table->getTableName($parent['parent']),
				'refColumns' => $parent['parentCol'],
			);
		}
		
		$modelTableBase = new Zend_CodeGenerator_Php_Class(array(
			'name' => $table->getTableBaseName(),
			'docblock' => new Zend_CodeGenerator_Php_Docblock(array(
				'shortDescription' => $table->getTableName() . PHP_EOL . '*DO NOT* edit this file.',
				'longDescription' => $templates['longClassDescription'],
				'tags' => array_merge($templates['tags']),
			)),
			'extendedClass' => $table->getTableBaseExtension(),
			'properties' => array(
			
				array('name' => '_name', 'visiblity' => 'protected', 'defaultValue' => $table->getName()),
				array('name' => '_primary', 'visiblity' => 'protected', 'defaultValue' => $table->getPrimary()),
				array('name' => '_rowClass', 'visiblity' => 'protected', 'defaultValue' => $table->getModelName()),
				array('name' => '_referenceMap', 'visiblity' => 'protected', 'defaultValue' => $tableReferences),
				array('name' => '_dependantTables', 'visiblity' => 'protected', 'defaultValue' => $table->getDependantTables()),
			),
			'methods' => $methods,
		));
		$modelTableBaseFile = new Zend_CodeGenerator_Php_File(array(
			'classes' => array($modelTableBase),
		));
		
		// save
		file_put_contents($table->getTableBaseFilePath(), $modelTableBaseFile->generate());
		return;
	}
}