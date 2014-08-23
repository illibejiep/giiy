<?
$this->breadcrumbs=array(
	'Videos'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Video','url'=>array('index')),
	array('label'=>'Create Video','url'=>array('create')),
	array('label'=>'View Video','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Video','url'=>array('admin')),
);
?>

<h1>Update Video <?=$model->id; ?></h1>

<? require(__DIR__ . '/../form.php');?>