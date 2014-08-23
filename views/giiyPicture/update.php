<?
$this->breadcrumbs=array(
	'Pictures'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Picture','url'=>array('index')),
	array('label'=>'Create Picture','url'=>array('create')),
	array('label'=>'View Picture','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Picture','url'=>array('admin')),
);
?>

<h1>Update Picture <?=$model->id; ?></h1>

<? require(__DIR__ . '/form.php');?>