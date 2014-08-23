<?
$this->breadcrumbs=array(
	'Videos'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Video','url'=>array('index')),
	array('label'=>'Create Video','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('video-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Videos</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?=CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
    <? $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

            <label for="Video_type_enum">Тип</label>
        <? $this->widget('bootstrap.widgets.TbSelect2',array(
        'name'=>'Video[type_enum]',
        'value'=> $model->type_enum,
        'data'=> GiiyVideoTypeEnum::$names,
        ));
        ?>
    
                    <?=$form->textFieldRow($model,'id',array('class'=>'span5')); ?>
                            <?=$form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>1024)); ?>
                    <?=$form->textFieldRow($model,'width',array('class'=>'span5')); ?>
                    <?=$form->textFieldRow($model,'height',array('class'=>'span5')); ?>
                    <?=$form->textFieldRow($model,'picture_id',array('class'=>'span5')); ?>
                    <?=$form->textFieldRow($model,'created',array('class'=>'span5')); ?>
                    <?=$form->textFieldRow($model,'modified',array('class'=>'span5')); ?>
                    <?=$form->textFieldRow($model,'duration',array('class'=>'span5')); ?>
                    <?=$form->textFieldRow($model,'bit_rate',array('class'=>'span5')); ?>
                    <?=$form->textFieldRow($model,'codec',array('class'=>'span5','maxlength'=>1024)); ?>
                    <?=$form->textAreaRow($model,'description',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>
        <div class="form-actions">
        <? $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>
    </div>
    <? $this->endWidget(); ?>
</div><!-- search-form -->

<? $this->widget('bootstrap.widgets.TbExtendedGridView',array(
    'fixedHeader' => true,
    'headerOffset' => 80,
	'id'=>'video-grid',
	'dataProvider'=>$dataProvider,
	'filter'=>$model,
	'columns'=>array(
            array(
            'class'=>'bootstrap.widgets.TbImageColumn',
            'imagePathExpression'=>'$data->picture?$data->picture->resize(180,120):null',
            'placeKittenSize' => '180/120',
            'htmlOptions' => array(
                'width' => 200,
                'height' => 140,
            ),
        ),
                array(
            'name' => 'type_enum',
            'value' => 'VideoTypeEnum::$names[$data->type_enum]',
        ),
                array(
            'name' => 'name',
            'value' => '"<a href=\"/'.get_class($model).'/update/$data->id\">$data->name</a>"',
            'type' => 'raw',
        ),
                array(
            'name' => 'width',
            'type' => 'raw',
        ),
                array(
            'name' => 'height',
            'type' => 'raw',
        ),
                array(
            'name' => 'created',
            'type' => 'raw',
        ),
                array(
            'name' => 'modified',
            'type' => 'raw',
        ),
                array(
            'name' => 'duration',
            'type' => 'raw',
        ),
                array(
            'name' => 'bit_rate',
            'type' => 'raw',
        ),
                array(
            'name' => 'codec',
            'type' => 'raw',
        ),
    		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
));
