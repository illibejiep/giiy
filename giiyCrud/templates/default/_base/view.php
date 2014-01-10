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
	\$model->{$nameColumn},
);\n";
?>

$this->menu=array(
	array('label'=>'List <?=$this->modelClass; ?>','url'=>array('index')),
	array('label'=>'Create <?=$this->modelClass; ?>','url'=>array('create')),
	array('label'=>'Update <?=$this->modelClass; ?>','url'=>array('update','id'=>$model-><?=$this->tableSchema->primaryKey; ?>)),
	array('label'=>'Delete <?=$this->modelClass; ?>','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model-><?=$this->tableSchema->primaryKey; ?>),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage <?=$this->modelClass; ?>','url'=>array('admin')),
);
?>

<h1>View <?=$this->modelClass." #<?=\$model->{$this->tableSchema->primaryKey}; ?>"; ?></h1>

<?="<?"; ?> $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
<?
foreach($this->tableSchema->columns as $column)
    if (!$column->isPrimaryKey && !$column->isForeignKey && $column->name != 'params')
	    echo "\t\t'".$column->name."',\n";
?>
	),
)); ?>
