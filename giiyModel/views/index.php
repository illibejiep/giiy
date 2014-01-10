<?php
$class=get_class($model);
?>
<h1>giix Model Generator</h1>

<p>This generator generates a model class for the specified database table.</p>

<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'tables'); ?>
        <select name="GiiyModelCode[tables][]" multiple="multiple" style="height: 300px;">
        <? foreach($this->getTables() as $table):?>
            <option value="<?=$table;?>"><?=$table;?></option>
        <? endforeach;?>
        </select>
        <div class="tooltip"></div>
		<?php echo $form->error($model,'tables'); ?>
	</div>

<?php $this->endWidget(); ?>