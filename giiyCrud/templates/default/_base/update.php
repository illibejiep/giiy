<?
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?
echo "<?\n";
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	\$model->{$nameColumn}=>array('view','id'=>\$model->{$this->tableSchema->primaryKey}),
	'Update',
);\n";
?>

$this->menu=array(
	array('label'=>'List <?=$this->modelClass; ?>','url'=>array('index')),
	array('label'=>'Create <?=$this->modelClass; ?>','url'=>array('create')),
	array('label'=>'View <?=$this->modelClass; ?>','url'=>array('view','id'=>$model-><?=$this->tableSchema->primaryKey; ?>)),
	array('label'=>'Manage <?=$this->modelClass; ?>','url'=>array('admin')),
);
?>

<h1>Update <?=$this->modelClass." <?=\$model->{$this->tableSchema->primaryKey}; ?>"; ?></h1>

<?='<? require(__DIR__.\'/../form.php\');?>'; ?>