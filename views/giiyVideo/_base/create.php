<?
$this->breadcrumbs=array(
	'Videos'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Video','url'=>array('index')),
	array('label'=>'Manage Video','url'=>array('admin')),
);
?>

<h1>Create Video</h1>

<? require(__DIR__ . '/../form.php');?>