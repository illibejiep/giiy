<?
$this->breadcrumbs=array(
	'Pictures',
);

$this->menu=array(
	array('label'=>'Create Picture','url'=>array('create')),
	array('label'=>'Manage Picture','url'=>array('admin')),
);
?>

<h1>Pictures</h1>

<div class="view">

    	<b><?=CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?=CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('type_enum')); ?>:</b>
	<?=CHtml::encode($data->type_enum); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?=CHtml::encode($data->name); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('width')); ?>:</b>
	<?=CHtml::encode($data->width); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('height')); ?>:</b>
	<?=CHtml::encode($data->height); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?=CHtml::encode($data->created); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('modified')); ?>:</b>
	<?=CHtml::encode($data->modified); ?>
	<br />

	<? /*
	<b><?=CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?=CHtml::encode($data->description); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('announce')); ?>:</b>
	<?=CHtml::encode($data->announce); ?>
	<br />

	*/ ?>

</div>
