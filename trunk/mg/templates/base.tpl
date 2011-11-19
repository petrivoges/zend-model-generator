<?

/**
 * Row model template
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: base.tpl 73 2010-11-21 18:59:30Z jacek $
 */

/* @var $table Generator_Table */

?>
<?='<?php' . PHP_EOL?>

/**
 * <?=$table->getBaseName() . PHP_EOL?>
 *
 * This class has been generated automatically by Jack's generator.
 * More info can't be found at: http://blog.jacekkobus.com
 *
<?foreach($table->getProperties() as $pname => $property):?>
 * @property <?=$property['type']?> 	$<?=$pname?> 	<?=$property['desc'] . PHP_EOL?>
<?endforeach;?>
 *
<?if(!empty($data['methods']['doc'])):?>
<?foreach($data['methods']['doc'] as $mname => $mclass):?>
 * @method <?=$mclass?> <?=$mname?>()
<?endforeach;?>
 *
<?endif?>
 * @package   	<?=$table->custom()->package . PHP_EOL?>
 * @subpackage  <?=$table->custom()->subPackage . PHP_EOL?>
 * @author   	<?=$table->custom()->author?> <<?=$table->custom()->email?>>
 * @copyright	<?=$table->custom()->copyright . PHP_EOL?>
 * @license  	<?=$table->custom()->license . PHP_EOL?>
 * @version  	$Id: base.tpl 73 2010-11-21 18:59:30Z jacek $
 */
class <?=$table->getBaseName()?> extends <?=$table->getBaseExtension() . PHP_EOL?>
{

<?if(!empty($data['methods'])):?>
<?if(isset($data['methods']['children'])):?>
<?foreach($data['methods']['children'] as $id => $function):?>
	/**
	 * Find dependent <?=$function['class'] . PHP_EOL?>
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
	 * @return Zend_Db_Table_Rowset_Abstract or NULL
	 */
	public function find<?=$function['function']?>($where = null, $order = null, $count = null, $offset = null)
	{
		return $this->findDependentRowset('<?=$function['table']?>', $where, $order, $count, $offset);
	}

	/**
	 * Create new <?=$function['class']?> row for <?=$table->getName() . PHP_EOL?>
	 *
	 * @param array $data
	 * @return <?=$function['class'] . PHP_EOL?>
	 */
	public function create<?=$function['function']?>(array $data = null)
	{
		$data['<?=$function['childCol']?>'] = $this-><?=$function['col']?>;
		$table = new <?=$function['table']?>();
		return $table->createRow()->setFromArray($data);
	}

	/**
	 * Delete all dependent <?=$function['class']?> rows for <?=$table->getName() . PHP_EOL?>
	 *
	 * @param array $where
	 * @return int Number of deleted rows
	 */
	public function delete<?=$function['function']?>(array $where = array())
	{
		$where['<?=$function['childCol']?> = ?'] = $this-><?=$function['col']?>;
		$table = new <?=$function['table']?>();
		return $table->delete($where);
	}

<?endforeach?>
<?endif?>
<?if(isset($data['methods']['parents'])):?>
<?foreach($data['methods']['parents'] as $id => $function):?>
	/**
	 * Find parent <?=$function['class'] . PHP_EOL?>
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
	 * @return <?=$function['class']?> or NULL
	 */
	public function find<?=$function['function']?>($where = null, $order = null, $count = null, $offset = null)
	{
		return $this->findParentRow('<?=$function['table']?>', $where, $order, $count, $offset);
	}

<?endforeach?>
<?endif?>
<?endif?>
}
