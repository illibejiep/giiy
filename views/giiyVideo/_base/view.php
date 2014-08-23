<?
$this->breadcrumbs=array(
	'Videos'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Video','url'=>array('index')),
	array('label'=>'Create Video','url'=>array('create')),
	array('label'=>'Update Video','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete Video','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Video','url'=>array('admin')),
);
?>

<h1>View Video #<?=$model->id; ?></h1>

<? $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'type_enum',
		'name',
		'width',
		'height',
		'created',
		'modified',
		'duration',
		'bit_rate',
		'codec',
		'description',
	),
)); ?>
