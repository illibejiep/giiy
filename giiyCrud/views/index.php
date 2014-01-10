<?php
$class=get_class($model);
Yii::app()->clientScript->registerScript('gii.crud',"
$('#{$class}_controller').change(function(){
	$(this).data('changed',$(this).val()!='');
});
$('#{$class}_model').bind('keyup change', function(){
	var controller=$('#{$class}_controller');
	if(!controller.data('changed')) {
		var id=new String($(this).val().match(/\\w*$/));
		if(id.length>0)
			id=id.substring(0,1).toLowerCase()+id.substring(1);
		controller.val(id);
	}
});
");
?>
<h1>Bootstrap Generator</h1>

<p>This generator generates a controller and views that implement CRUD operations for the specified data model.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'model'); ?>
        <select name="GiiyCrudCode[models][]" multiple="multiple" style="height: 300px;">
            <? foreach($this->getModels() as $modelName):?>
            <option value="<?=$modelName;?>"><?=$modelName;?></option>
            <? endforeach;?>
        </select>
        <div class="tooltip"></div>
		<?php echo $form->error($model,'model'); ?>
	</div>

<?php $this->endWidget(); ?>
