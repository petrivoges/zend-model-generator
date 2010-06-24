<?php

class Generator_Renderer 
{
	const TEMPLATE_MODELS 	= './template/model.tpl';
	const TEMPLATE_TABLES 	= './template/table.tpl';
	const TEMPLATE_TBASE 	= './template/tbase.tpl';
	const TEMPLATE_BASE 	= './template/base.tpl';
	
	private $_options = array();
	private $_storage = array();
	
	
	public function __construct(array $options = null)
	{
		$this->_options = $options;
	}
	
	protected function store($tableName, $type, $renderedTemplate)
	{
		$this->_storage[$type][$tableName] = $renderedTemplate;
	}
	
	public function makeDirectory ($dir)
	{
		if (! is_dir($dir)) {
			mkdir($dir, null, true);
		}
	}
	
	protected function getOptions()
	{
		return $this->_options;
	}
	
	public function make(Generator_Table $table)
	{
		// render model
		$result = $this->render($table, 'model', self::TEMPLATE_MODELS);
		$this->save($result, $table->getModelFilePath());
		Generator::log('Saving '.$table->getModelFilePath().'...');
		// render base
		
		// functions
		
		$methods = array(
			'doc' => array('getTable' => $table->getTableName()),
			'children' => array(),
			'parents' => array(),
		);
		
		if($children = $table->getChildren()){
			foreach ($children as $child => $rel){
				// format children find and create functions
				$function = $child;
				// remove table_ prefix if any
				$pattern = '#^'.$table->getName().'_(.*)$#i';
				if(preg_match($pattern, $function)){
					$function = preg_replace($pattern, '\\1', $function);
				}
				$function = $table->formatFunctionName($function);

				$methods['children'][] = array(
					'name' => $child,
					'function' => $function,
					'class' => $table->getModelName($child),
					'table' => $table->getTableName($child),
					'col' => $rel['col'],
					'childCol' => $rel['childCol'],
				);
			}
		}
		
		if($parents = $table->getParents()){
			foreach ($parents as $parent => $rel){
				
				// remove prefixes
				$function = $parent;
				$pattern = '#^'.$table->getName().'_(.*)$#i';
				if(preg_match($pattern, $function)){
					$function = preg_replace($pattern, '\\1', $function);
				}
				
				$methods['parents'][] = array(
					'name' => $parent,
					'function' => $table->formatFunctionName($function),
					'class' => $table->getModelName($parent),
					'table' => $table->getTableName($parent)
				);
			}
		}
		
		$data['methods'] = $methods;
		$result = $this->render($table, 'base', self::TEMPLATE_BASE, $data);
		Generator::log('Saving '.$table->getBaseFilePath().'...');
		$this->save($result, $table->getBaseFilePath());
		
		
		unset($methods, $data);
		
		// render table base
		
		$result = $this->render($table, 'base', self::TEMPLATE_TBASE);
		Generator::log('Saving '.$table->getTableBaseFilePath().'...');
		$this->save($result, $table->getTableBaseFilePath());
		
		// render table
		
		$result = $this->render($table, 'base', self::TEMPLATE_TABLES);
		Generator::log('Saving '.$table->getTableFilePath().'...');
		$this->save($result, $table->getTableFilePath());
	}
	
	/**
	 * 
	 * @param Generator_Table $table
	 * @param string $type model|base|table
	 * @param string $template file
	 * @param mix $data optional data passed to view
	 */
	public function render($table, $type, $template, $data = null)
	{
		ob_start();
		include $template;
		$result = ob_get_clean();
		return $result;
	}
	
	public function save($data, $directory)
	{
		file_put_contents($directory, $data);
	}

}












