<?="<?";?>
 $form = $this->beginWidget('ModelForm',array('model'=>$model,'fromModel' => isset($fromModel)?$fromModel:null)); <?="?>";?>
<div>
    <?='<?=$form->errorSummary($form->model);?>';?>
</div>
<? if (array_key_exists('type_enum',$this->tableSchema->columns)):?>
<div>
    <?="<?=";?>$form->enum('type'); <?="?>\n";?>
</div>
<? endif;?>
<? if (array_key_exists('params',$this->tableSchema->columns)):?>
<div>
    <?="<?=";?>$form->params(); <?="?>\n";?>
</div>
<? endif;?>
<? foreach($this->tableSchema->columns as $column): ?>
<? if ($column->isPrimaryKey
        || $column->isForeignKey
        || in_array($column->name,array('created','modified','type_enum','params'))
) continue; ?>
<? if (substr($column->name,-5) == '_enum'):?>
<?
$words = explode('_',$column->name);
array_pop($words);
$words = array_map('ucfirst',$words);
$columnName = lcfirst(join('',$words));
?>
    <?="<?=";?>$form->enum('<?=$columnName;?>'); <?="?>\n";?>
<? continue;?>
<? endif; ?>
<? switch ($column->dbType):
    case 'text': ?>
    <?="<?=";?>$form->text('<?=$column->name;?>'); <?="?>\n";?>
<? break;?>
<? case 'time': ?>
    <?="<?=";?>$form->time('<?=$column->name;?>'); <?="?>\n";?>
<? break;?>
<? case 'date': ?>
    <?="<?=";?>$form->date('<?=$column->name;?>'); <?="?>\n";?>
<? break;?>
<? case 'timestamp':?>
<? case 'timestamp without time zone': ?>
<? case 'datetime': ?>
    <?="<?=";?>$form->datetime('<?=$column->name;?>'); <?="?>\n";?>
<? break;?>
<? case 'boolean': ?>
    <?="<?=";?>$form->check('<?=$column->name;?>'); <?="?>\n";?>
<? break;?>
<? default: ?>
    <?="<?=";?>$form->input('<?=$column->name;?>'); <?="?>\n";?>
<? break;?>
<? endswitch;?>
<? endforeach; ?><? foreach (array_keys(CActiveRecord::model($this->modelClass)->relations()) as $name): ?>
    <?="<?=";?>$form->relation('<?=$name;?>'); <?="?>\n";?>
<? endforeach; ?>
<?="<?=";?>$form->submitButton(); ?>
<?="<?";?> $this->endWidget(); ?>