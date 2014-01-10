<?
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?
$model = GiiyActiveRecord::model($this->modelClass);
echo "<?\n";
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	'Manage',
);\n";
?>

$this->menu=array(
	array('label'=>'List <?=$this->modelClass; ?>','url'=>array('index')),
	array('label'=>'Create <?=$this->modelClass; ?>','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('<?=$this->class2id($this->modelClass); ?>-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage <?=$this->pluralize($this->class2name($this->modelClass)); ?></h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?="<?=CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>"; ?>

<div class="search-form" style="display:none">
    <?="<? \$form=\$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl(\$this->route),
	'method'=>'get',
)); ?>\n"; ?>

    <? if (array_key_exists('type_enum',$this->tableSchema->columns)):?>
        <label for="<?=$this->modelClass;?>_type_enum">Тип</label>
        <?="<?";?> $this->widget('bootstrap.widgets.TbSelect2',array(
        'name'=>'<?=$this->modelClass;?>[type_enum]',
        'value'=> $model->type_enum,
        'data'=> <?=$this->modelClass;?>TypeEnum::$names,
        ));
        ?>
    <? endif; ?>

    <? foreach($this->tableSchema->columns as $column): ?>
        <?
        $field=$this->generateInputField($this->modelClass,$column);
        if(strpos($field,'password')!==false || substr($column->name,-5) == '_enum')
            continue;
        ?>
        <?="<?=".$this->generateActiveRow($this->modelClass,$column)."; ?>\n"; ?>
    <? endforeach; ?>
    <div class="form-actions">
        <?="<? \$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label'=>'Search',
		)); ?>\n"; ?>
    </div>
    <?="<? \$this->endWidget(); ?>\n"; ?>
</div><!-- search-form -->

<?="<?"; ?> $this->widget('bootstrap.widgets.TbExtendedGridView',array(
    'fixedHeader' => true,
    'headerOffset' => 80,
	'id'=>'<?=$this->class2id($this->modelClass); ?>-grid',
	'dataProvider'=>$dataProvider,
	'filter'=>$model,
	'columns'=>array(
    <? if ($model instanceof Iillustrated):?>
        array(
            'class'=>'bootstrap.widgets.TbImageColumn',
            'imagePathExpression'=>'$data->picture?$data->picture->resize(180,120):null',
            'placeKittenSize' => '180/120',
            'htmlOptions' => array(
                'width' => 200,
                'height' => 140,
            ),
        ),
    <? endif; ?>
<? foreach($this->tableSchema->columns as $column): ?>
<? if ($column->isPrimaryKey || $column->isForeignKey || $column->dbType == 'text' || $column->name == 'params')
        continue;
?>
    <? if (substr($column->name,-5) == '_enum'): ?>
        array(
            'name' => '<?=$column->name;?>',
            'value' => '<?=$this->modelClass;?><?=$this->generateClassName($column->name);?>::$names[$data-><?=$column->name;?>]',
        ),
    <? elseif ($column->name == 'name'): ?>
        array(
            'name' => '<?=$column->name;?>',
            'value' => '"<a href=\"/'.get_class($model).'/update/$data->id\">$data->name</a>"',
            'type' => 'raw',
        ),
    <? else: ?>
        array(
            'name' => '<?=$column->name;?>',
            'type' => 'raw',
        ),
    <? endif;?>
<? endforeach; ?>
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
));
