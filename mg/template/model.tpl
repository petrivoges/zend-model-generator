<?

/**
 * Model template
 * 
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 * @version $Id: model.tpl 57 2010-11-02 14:22:41Z jacek $
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
 * @version  	$Id: model.tpl 57 2010-11-02 14:22:41Z jacek $
 */
class <?=$table->getModelName()?> extends <?=$table->getBaseName() . PHP_EOL?>
{
	// create your own methods here (remove this comment)
}
