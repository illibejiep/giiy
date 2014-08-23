<? $form = $this->beginWidget('ModelForm',array('model'=>$model,'fromModel' => isset($fromModel)?$fromModel:null)); ?>

<div>
    <?=$form->errorSummary($form->model);?>
</div>

<? if ($model->id): ?>
    <div>
        <?=$form->enum('type'); ?>
    </div>
        <?=$form->text('name'); ?>
        <?=$form->input('width'); ?>
        <?=$form->input('height'); ?>
        <?=$form->text('description'); ?>
        <?=$form->text('announce'); ?>
        <?=$form->relation('videos'); ?>
<?=$form->submitButton(); ?>
<? endif;?>
<? $this->endWidget(); ?>
