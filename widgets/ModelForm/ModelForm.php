<?php
Yii::import('bootstrap.widgets.*');
class ModelForm extends TbActiveForm
{
    /** @var GiiyActiveRecord */
    public $model;
    public $fromModel;

    public function init()
    {
        $assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets');
        Yii::app()->clientScript->registerScriptFile($assets . '/js/modelForm.js');

        Yii::app()->clientScript->registerPackage('modelForm');
        Yii::app()->clientScript->registerPackage('jquery.ui');
        Yii::app()->clientScript->registerPackage('jquery');
        Yii::app()->clientScript->registerPackage('cookie');
        Yii::app()->clientScript->registerScript('yiiParams','
            var yiiParams = '.json_encode(Yii::app()->params->toArray()).';
        ',CClientScript::POS_HEAD);

        if (!$this->fromModel && $this->model instanceof IFileBased)
            echo $this->upload('id');

        if (!$this->fromModel)
            $this->htmlOptions['onsubmit'] = 'return modelFormSubmit();';

        $this->htmlOptions['isNew'] = (int)!$this->model->id;

        parent::init();
    }

    public function input($attribute)
    {
        $widget = $this->createWidget($this->getInputClassName(),array(
            'form' => $this,
            'model' => $this->model,
            'attribute' => $attribute,
            'type' => TbInput::TYPE_TEXT
        ));

        return $this->renderWidget($widget);
    }

    public function text($attribute)
    {
        $widget = $this->createWidget($this->getInputClassName(),array(
            'form' => $this,
            'model' => $this->model,
            'attribute' => $attribute,
            'type' => TbInput::TYPE_TEXTAREA
        ));

        return $this->renderWidget($widget);
    }

    public function check($attribute)
    {
        $widget = $this->createWidget($this->getInputClassName(),array(
            'form' => $this,
            'model' => $this->model,
            'attribute' => $attribute,
            'type' => TbInput::TYPE_CHECKBOX
        ));

        return $this->renderWidget($widget);
    }


    public function upload($attribute)
    {
        $widget = $this->createWidget('ModelFileUpload',array(
            'model'=>$this->model,
            'attribute' => $attribute,
        ));

        return $this->renderWidget($widget);
    }

    public function submitButton()
    {
        $widget = $this->createWidget('TbButton',array(
            'buttonType' => 'submit',
            'label' => $this->model->id?'Сохранить':'Создать',
        ));

        ob_start();
        $widget->run();
        return '<div>'.ob_get_clean().'</div>';
    }

    public function enum($attribute)
    {
        $enumClass = get_class($this->model);

        foreach (explode('_',$attribute) as $name)
            $enumClass .= ucfirst($name);

        $enumClass .= 'Enum';

        $widget = $this->createWidget('TbSelect2',
        array(
            'model' => $this->model,
            'attribute' => $attribute,
            'data' => $enumClass::$names,
            'htmlOptions' => array(
                'key' => 'id',
            ),
            'options' => array(
                'width' => '300px'
            ),
        ));

        if ($attribute == 'type') {
            $widget->htmlOptions['onChange'] = '$(this).closest("form").find(".typeEnumerable").hide();'.
                '$(this).closest("form").find(".typeEnumerable").closest("fieldset").hide();'.
                '$(this).closest("form").find(".typeEnumerable_" + $(this).val()).show();'.
                '$(this).closest("form").find(".typeEnumerable_" + $(this).val()).closest("fieldset").show();';

            Yii::app()->clientScript->registerScript('initTypeInput','
                $("[name*=type]").each(function(){
                    $(this).change();
                });
            ',CClientScript::POS_LOAD);
            ob_start();
            $widget->run();
            return ob_get_clean();
        }

        return $this->renderWidget($widget);
    }

    public function params()
    {
        $output = '';
        $typeClass = get_class($this->model).'TypeEnum';

        foreach ($typeClass::getParams() as $name=>$param) {

            $classes = array();
            $hidden = false;
            if (isset($param['forTypes'])){
                $classes = array('typeEnumerable');
                foreach ($param['forTypes'] as $typeId)
                    $classes[] = 'typeEnumerable_'.$typeId;

                if (
                    (!$this->model->id && !in_array(1,$param['forTypes']))
                    ||
                    ($this->model->id && !in_array($this->model->type->id,$param['forTypes']))
                ) $hidden = true;

                unset($param['forTypes']);
            }

            $widget = $this->createWidget($this->getInputClassName(), array(
                    'form' => $this,
                    'model' => $this->model,
                    'attribute' => 'params['.$name.']',
                    'type' => isset($param['type'])?$param['type']:TbInput::TYPE_TEXT,
                    'htmlOptions' => $param,
            ));

            ob_start();
            $widget->run();
            $html = ob_get_clean();

            $html = '<div class="'.join(' ',$classes).'" ' .($hidden?'style="display:none" ':'').'>'.$html.'</div>';

            $output .= $html;
        }

        return $output;
    }

    public function date($attribute)
    {
        $widget = $this->createWidget('TbDatePicker',array(
            'model' => $this->model,
            'attribute' => $attribute,
            'options' => array(
                'format' => 'yyyy-mm-dd',
            )
        ));

        return $this->renderWidget($widget);
    }

    public function time($attribute)
    {
        $widget = $this->createWidget('TbTimePicker',array(
            'model' => $this->model,
            'attribute' => $attribute,
            'options' => array(
                'format' => 'yyyy-mm-dd',
            )
        ));

        return $this->renderWidget($widget);
    }

    public function datetime($attribute)
    {
        $widget = $this->createWidget('CJuiDateTimePicker',array(
            'model' => $this->model,
            'attribute' => $attribute,
            'options' => array(
                'dateFormat' => 'yy-mm-dd',
                'timeFormat' => 'hh:mm:ss',
            )
        ));

        return $this->renderWidget($widget);
    }

    public function elementTypes($attribute)
    {
        $typeName = str_replace(array(strtolower(get_class($this->model)),'Types'),'',$attribute);
        $typeClass = $typeName.'TypeEnum';

        $widget = $this->createWidget('TbSelect2',
            array(
                'model' => $this->model,
                'attribute' => $attribute,
                'data' => $typeClass::$names,
                'htmlOptions' => array(
                    'multiple'=> true,
                    'key' => 'type_enum',
                ),
                'options' => array(

                    'width' => '665px'
                ),
            ));

        return $this->renderWidget($widget);
    }

    public function tags($attribute)
    {
        $value = $this->model->$attribute;

        if (is_array($value))
            $value = join(';',$value);

        $widget = $this->createWidget('TbSelect2',
            array(
                'model' => $this->model,
                'attribute' => $attribute,
                'htmlOptions' => array(
                    'value' => $value,
                ),
                'asDropDownList' => false,
                'options' => array(
                    'separator' => ';',
                    'tags' => array(),
                    'width' => '250px',
                ),
            ));

        return $this->renderWidget($widget);
    }

    public function editor($attribute,$toolbar = 'Full')
    {
        $frontendCss = CMap::mergeArray(
            Yii::app()->clientScript->packages['main']['css'],
            Yii::app()->clientScript->packages['card']['css']
        );

        $basePath = Yii::getPathOfAlias(Yii::app()->clientScript->packages['main']['basePath']);
        $css = array();

        foreach ($frontendCss as $cssPath)
            $css[] = Yii::app()->assetManager->publish($basePath.DIRECTORY_SEPARATOR.$cssPath);

        $css = array_unique($css);
        sort($css);
        $toolbarFull = array(
            array(
                'name' => 'document',
                'items' => array('Source'),
            ),
            array(
                'name' => 'basicstyles',
                'items' => array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'),
            ),
            array(
                'name' => 'paragraph',
                'items' => array('NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl'),
            ),
            array(
                'name' => 'links',
                'items' => array('Link','Unlink','Anchor'),
            ),
            array(
                'name' => 'insert',
                'items' => array('Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'),
            ),
            array(
                'name' => 'styles',
                'items' => array('Styles','Format','Font','FontSize' ),
            ),
            array(
                'name' => 'colors',
                'items' => array('TextColor','BGColor'),
            ),
            array(
                'name' => 'tools',
                'items' => array('ShowBlocks'),
            ),
        );

        $toolbarBasic = array(
            array(
                'name' => 'document',
                'items' => array('Source'),
            ),
            array(
                'name' => 'basicstyles',
                'items' => array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'),
            ),
            array(
                'name' => 'links',
                'items' => array('Link','Unlink','Anchor'),
            ),
            array(
                'name' => 'insert',
                'items' => array('Image'),
            ),
            array(
                'name' => 'styles',
                'items' => array('Format','Font','FontSize' ),
            ),
            array(
                'name' => 'colors',
                'items' => array('TextColor','BGColor'),
            ),
        );

        $widget = $this->createWidget('TbCKEditor', array(
            'model' => $this->model,
            'attribute' => $attribute,
            'editorOptions' => array(
                'contentsCss' => $css,
                'width' => 526,
                'bodyClass'    => 'b-post-card__text',
                'toolbar' => $toolbar=='Full'?$toolbarFull:$toolbarBasic,
            )
        ));

        return $this->renderWidget($widget);
    }

    public function relation($relationName)
    {
        $relations = $this->model->relations();

        if (!isset($relations[$relationName]))
            throw new CException('whrong relation attribute name');

        $relation = $relations[$relationName];
        $relationModel = $relation[1];
        $isMultiple = $relation[0] == CActiveRecord::HAS_MANY || $relation[0] == CActiveRecord::MANY_MANY;
        $isFileBased = GiiyActiveRecord::model($relationModel) instanceof IFileBased;

        if ($this->fromModel == $relationModel)
            return '';
        $value = join(',', $this->model->getRelationIds($relationName));

        $formatSelectionFn = $isFileBased?'js:formatSelectionUpload':'js:formatSelection';
        if ($this->fromModel)
            $formatSelectionFn = 'js:formatSelectionSubform';

        $widget = $this->createWidget('TbSelect2', array(
            'form' => $this,
            'model' =>$this->model,
            'attribute' => $relationName,
            'value' => ' ',
            'htmlOptions' => array(
                'value' => $value,
                'relationName' => $relationName,
                'isMultiple' => $isMultiple,
            ),
            'asDropDownList' => false,
            'options' => array(
                'width' => $this->fromModel?'300px':'665px',
                'multiple' => true,
                'placeholder' => 'Выберите ' . $this->model->getAttributeLabel($relationName),
                'minimumInputLength' => 1,
                'ajax' => array(
                    'url' => '/' . strtolower($relationModel),
                    'dataType' => 'json',
                    'type' => 'POST',
                    'data' => 'js: function(term,page) { return {q: term}; }',
                    'results' => 'js: function(data,page) { return {results: data}; }',
                ),
                'formatSelection' => $formatSelectionFn,
                'formatResult' => 'js:formatResult',
                'initSelection' => 'js:initSelection',
            ),
        ));

        if ($isFileBased)
            Yii::app()->bootstrap->registerAssetJs('fileupload/jquery.fileupload.js');

        Yii::app()->clientScript->registerScript('initModelData','
            var modelData = Array();
        ',CClientScript::POS_HEAD);

        Yii::app()->clientScript->registerScript($relationName.'ModelData','
            modelData["'.$relationName.'"] = '.json_encode($this->model->getRelationNames($relationName,true)).';
        ',CClientScript::POS_HEAD);

        Yii::app()->clientScript->registerScript('ModelData','
            modelData["_modelName"] = "'.get_class($this->model).'";
        ',CClientScript::POS_HEAD);

        return $this->renderWidget($widget);
    }

    public function relationForm($relationName)
    {
        $relations = $this->model->relations();

        if (!isset($relations[$relationName]))
            throw new CException('whrong relation attribute name');

        $relation = $relations[$relationName];
        $relationModel = $relation[1];
        $isMultiple = $relation[0] == CActiveRecord::HAS_MANY || $relation[0] == CActiveRecord::MANY_MANY;
        $isFileBased = GiiyActiveRecord::model($relationModel) instanceof IFileBased;

        if ($this->fromModel == $relationModel)
            return '';

        $value = join(',', $this->model->getRelationIds($relationName));

        $widget = $this->createWidget('TbSelect2', array(
            'form' => $this,
            'model' =>$this->model,
            'attribute' => $relationName,
            'htmlOptions' => array(
                'value' => $value,
                'relationName' => $relationName,
                'isMultiple' => $isMultiple,
                'label' => '<a name="'.$relationName.'"></a>'.
                    $this->model->getAttributeLabel($relationName).
                    ' <button name="add" class="btn btn-success btn-mini" onclick="addForm($(this).parent().next(),\''.
                    $relationModel.'\');return false;" >+</button>',

            ),
            'asDropDownList' => false,
            'options' => array(

                'width' => '755px',
                'multiple' => true,
                'placeholder' => 'Выберите ' . $this->model->getAttributeLabel($relationName),
                'minimumInputLength' => 1,
                'ajax' => array(
                    'url' => '/' . strtolower($relationModel),
                    'dataType' => 'json',
                    'type' => 'POST',
                    'data' => 'js: function(term,page) { return {q: term}; }',
                    'results' => 'js: function(data,page) { return {results: data}; }',
                ),
                'formatSelection' => $isFileBased?'js:formatSelectionUpload':'js:formatSelectionForm',
                'formatResult' => 'js:formatResult',
                'initSelection' => 'js:initSelection',
            ),
        ));

        if ($isFileBased)
            Yii::app()->bootstrap->registerAssetJs('fileupload/jquery.fileupload.js');

        Yii::app()->clientScript->registerScript('initModelData','
            var modelData = Array();
        ',CClientScript::POS_HEAD);

        Yii::app()->clientScript->registerScript($relationName.'ModelData','
            modelData["'.$relationName.'"] = '.json_encode($this->model->getRelationNames($relationName,true)).';
        ',CClientScript::POS_HEAD);

        Yii::app()->clientScript->registerScript('ModelData','
            modelData["_modelName"] = "'.get_class($this->model).'";
        ',CClientScript::POS_HEAD);

        return $this->renderWidget($widget);
    }

    public function renderWidget($widget)
    {
        ob_start();
        if ($widget instanceof TbSelect2
            OR $widget instanceof TbDatePicker
            OR $widget instanceof TbTimePicker
            OR $widget instanceof CJuiDateTimePicker
        )
            echo $this->label($this->model,$widget->attribute,$widget->htmlOptions);

        $widget->run();
        $html = ob_get_clean();

        if ($this->model instanceof ITypeEnumerable
            && $widget->attribute !== $this->model->tableSchema->primaryKey) {
            $classes = array('typeEnumerable');
            $hidden = true;
            foreach ($this->model->getTypesFields() as $type_id => $fields) {
                if (in_array($widget->attribute,$fields)) {
                    $classes[] = 'typeEnumerable_'.$type_id;
                    if ($this->model->getType() && $this->model->getType()->id == $type_id)
                        $hidden = false;
                }
            }

            $html = '<div name="'.$widget->attribute.'" class="'.join(' ',$classes).'" ' .($hidden?'style="display:none" ':'').'>'.$html.'</div>';
        }
        return $html;
    }
}