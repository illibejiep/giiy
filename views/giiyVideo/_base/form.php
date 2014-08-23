<? $form = $this->beginWidget('ModelForm',array('model'=>$model,'fromModel' => isset($fromModel)?$fromModel:null)); ?><div>
    <?=$form->errorSummary($form->model);?></div>
<div>
    <?=$form->enum('type'); ?>
</div>
    <?=$form->input('name'); ?>
    <?=$form->input('width'); ?>
    <?=$form->input('height'); ?>
    <?=$form->input('duration'); ?>
    <?=$form->input('bit_rate'); ?>
    <?=$form->input('codec'); ?>
    <?=$form->text('description'); ?>
    <?=$form->relation('picture'); ?>
<?=$form->submitButton(); ?>
<? $this->endWidget(); ?>