<?

/**
 * Model Base Table template
 */

/* @var $table Generator_Table */


?>
<?='<?php' . PHP_EOL?>

/**
 * <?=$table->getTableName() . PHP_EOL?>
 * 
 * This class has been generated automatically by Jack's generator.
 * More info can be found at: http://blog.jacekkobus.com
 * 
 * @package   	<?=$table->custom()->package . PHP_EOL?>
 * @subpackage  <?=$table->custom()->subPackage . PHP_EOL?>
 * @author   	<?=$table->custom()->author?> <<?=$table->custom()->email?>>
 * @copyright	<?=$table->custom()->copyright . PHP_EOL?>
 * @license  	<?=$table->custom()->license . PHP_EOL?>
 * @version  	SVN: $Id: <?=$table->getFilename()?> <?=date('d-m-Y H:i:s')?> $
 */
class <?=$table->getTableName()?> extends <?=$table->getTableBaseName() . PHP_EOL?>
{
	
}
