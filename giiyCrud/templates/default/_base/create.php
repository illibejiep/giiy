<?
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?
echo "<?\n";
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	'Create',
);\n";
?>

$this->menu=array(
	array('label'=>'List <?=$this->modelClass; ?>','url'=>array('index')),
	array('label'=>'Manage <?=$this->modelClass; ?>','url'=>array('admin')),
);
?>

<h1>Create <?=$this->modelClass; ?></h1>

<?='<? require(__DIR__.\'/../form.php\');?>'; ?>
