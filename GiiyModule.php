<?php

class GiiyModule extends CWebModule
{
    public static $pixPath;
    public static $pixUrl = '/pix';

    public static $videoPath;
    public static $videoUrl = '/video';

    public $controllersPath;
    public $modelsPath;

    public $tablePrefix = '';

    protected function init()
    {
        if (!$this->controllersPath && Yii::app() instanceof CWebApplication)
            $this->controllersPath = Yii::app()->getControllerPath();

        if (!$this->modelsPath)
            $this->modelsPath = Yii::getPathOfAlias('application.models');

        if (!self::$pixPath &&  Yii::getPathOfAlias('webroot'))
            self::$pixPath = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.'pix';

        if (!self::$videoPath && Yii::getPathOfAlias('webroot'))
            self::$videoPath = Yii::getPathOfAlias('webroot') . DIRECTORY_SEPARATOR.'video';

        if (self::$pixPath && !file_exists(self::$pixPath))
            mkdir(self::$pixPath);

        $this->setImport(array(
            'giiy.components.*',
            'giiy.interfaces.*',
            'giiy.models.*',
            'giiy.models._base.*',
            'giiy.models.enum.*',
            'giiy.widgets.ModelForm.*',
            'giiy.widgets.ModelFileUpload.*',

            'application.models._base.*',
            'application.models.enum.*',
        ));
    }
}