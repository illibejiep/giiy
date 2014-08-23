<?
$this->breadcrumbs=array(
	'Videos',
);

$this->menu=array(
	array('label'=>'Create Video','url'=>array('create')),
	array('label'=>'Manage Video','url'=>array('admin')),
);
?>

<h1>Videos</h1>

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

	<b><?=CHtml::encode($data->getAttributeLabel('picture_id')); ?>:</b>
	<?=CHtml::encode($data->picture_id); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?=CHtml::encode($data->created); ?>
	<br />

	<? /*
	<b><?=CHtml::encode($data->getAttributeLabel('modified')); ?>:</b>
	<?=CHtml::encode($data->modified); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('duration')); ?>:</b>
	<?=CHtml::encode($data->duration); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('bit_rate')); ?>:</b>
	<?=CHtml::encode($data->bit_rate); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('codec')); ?>:</b>
	<?=CHtml::encode($data->codec); ?>
	<br />

	<b><?=CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?=CHtml::encode($data->description); ?>
	<br />

	*/ ?>

</div>
