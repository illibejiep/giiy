<?php

class ModelFileUpload extends CWidget
{
    public $value;
    public $attribute;
    /** @var  GiiyActiveRecord */
    public $model;
    public $multiple = false;

    public $modelController;
	/**
	 * @var string name of the form view to be rendered
	 */
	public $viewDir = 'giiy.widgets.ModelFileUpload.views';

    public function init()
    {
        if ($this->attribute === $this->model->tableSchema->primaryKey)
            $this->value = $this->model;
        elseif(!$this->value)
            $this->value = $this->model->{$this->attribute};

        if (is_array($this->value)) {
            $this->multiple = true;
        }
        else{
            $this->value = array($this->value);
        }

    }


    /**
	 * Generates the required HTML and Javascript
	 */
	public function run()
	{
        $view = $this->viewDir.'.file';

        if (!$this->modelController)
            $this->modelController = '/'.get_class($this->model);

        if ($this->model instanceof GiiyPicture || $this->model instanceof GiiyVideo)
            $this->modelController = '/giiy/'.get_class($this->model);

	    $this->render($view, array(
            'value' => $this->value,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'multiple'  => $this->multiple,
            'modelController' => $this->modelController,
        ));

		$this->registerClientScript();
	}

	/**
	 * Registers and publishes required scripts
	 * @param string $id
	 */
	public function registerClientScript()
	{
		// Upgrade widget factory
		Yii::app()->bootstrap->registerAssetJs('fileupload/vendor/jquery.ui.widget.js');
		Yii::app()->bootstrap->registerAssetJs('fileupload/jquery.iframe-transport.js');
		Yii::app()->bootstrap->registerAssetJs('fileupload/jquery.fileupload.js');
	}
}