<?

/**
 * Model template
 */

/* @var $table Generator_Table */

?>
<?='<?php' . PHP_EOL?>

/**
 * <?=$table->getModelName() . PHP_EOL?>
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
class <?=$table->getModelName()?> extends <?=$table->getBaseName() . PHP_EOL?>
{
	
}
