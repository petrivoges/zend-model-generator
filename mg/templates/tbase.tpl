<?

/**
 * Model Base Table template
 * 
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: tbase.tpl 57 2010-11-02 14:22:41Z jacek $
 */

/* @var $table Generator_Table */



$parents = $table->getParents();
if(!empty($parents)){
	
	$refMap = '';
	foreach ($parents as $pTable => $refs){
		
		$refMap .= '		\''.$refs['key'].'\' => array(' . PHP_EOL;
		$refMap .= '			\'columns\' => \''.$refs['col'].'\',' . PHP_EOL;
		$refMap .= '			\'refTableClass\' => \''.$table->getTableName($pTable).'\',' . PHP_EOL;
		$refMap .= '			\'refColumns\' => \''.$refs['parentCol'].'\'' . PHP_EOL;
		$refMap .= '		),' . PHP_EOL;
		
	}
	
	//$refMap = 'array('.$refMap.')';
	$parents = $refMap;
}

?>
<?='<?php' . PHP_EOL?>

/**
 * <?=$table->getTableBaseName() . PHP_EOL?>
 * 
 * This class has been generated automatically by Jack's generator.
 * More info can be found at: http://blog.jacekkobus.com
 * 
<?if(!empty($methods)):?>
<?foreach($methods as $mname => $mclass):?>
 * @method <?=$mclass?> <?=$mname?>()
<?endforeach;?>
 *
<?endif?>
 * @method <?=$table->getModelName()?> findOne()
 *
 * @package   	<?=$table->custom()->package . PHP_EOL?>
 * @subpackage  <?=$table->custom()->subPackage . PHP_EOL?>
 * @author   	<?=$table->custom()->author?> <<?=$table->custom()->email?>>
 * @copyright	<?=$table->custom()->copyright . PHP_EOL?>
 * @license  	<?=$table->custom()->license . PHP_EOL?>
 * @version  	$Id: tbase.tpl 57 2010-11-02 14:22:41Z jacek $
 */
class <?=$table->getTableBaseName()?> extends <?=$table->getTableBaseExtension() . PHP_EOL?>
{
	/**
	 * Table name
	 * @var string
	 */
	protected $_name = '<?=$table->getName()?>';
	
	/**
	 * Primary key
	 * @var string
	 */
	protected $_primary = <?=$table->getPrimaryAsString()?>;
	
	/**
	 * Row class
	 * @var string
	 */
	protected $_rowClass = '<?=$table->getModelName()?>';
<?if(!empty($parents)):?>

	/**
	 * Reference map
	 * @var string
	 */
	protected $_referenceMap = array(
<?=$parents?>
	);
<?endif?>
<?if($table->getDependentTables()):?>

	/**
	 * Dependant tables
	 * @var string
	 */
	protected $_dependentTables = <?=$table->getDependantAsString()?>;
<?endif?>
<?if($table->getUniqueKeys()):?>
<?foreach ($table->getUniqueKeys() as $id => $key):?>

	/**
	 * Find <?=$table->getModelName()?> by <?=$key . PHP_EOL?>
	 * @return <?=$table->getModelName() . PHP_EOL?>
	 */
	public function findBy<?=$table->formatFunctionName($key)?>($value)
	{
		return $this->findOne(array('<?=$key?> = ?' => $value));
	}
<?endforeach;?>
<?endif?>
}
