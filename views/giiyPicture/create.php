<?
$this->breadcrumbs=array(
	'Pictures'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Picture','url'=>array('index')),
	array('label'=>'Manage Picture','url'=>array('admin')),
);
?>

<h1>Create Picture</h1>

<? require(__DIR__ . '/form.php');?>