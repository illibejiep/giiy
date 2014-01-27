<?php

class Giiy extends CModule
{
    public static $pixPath;
    public static $pixUrl = '/pix';

    public static $videoPath;
    public static $videoUrl = '/video';

    public $controllersPath;
    public $modelsPath;

    protected function init()
    {
        if (!$this->controllersPath)
            $this->controllersPath = Yii::app()->getControllerPath();

        if (!$this->modelsPath)
            $this->modelsPath = Yii::getPathOfAlias('application.models');

        if (!self::$pixPath)
            self::$pixPath = Yii::getPathOfAlias('webroot').'/pix';

        if (!self::$videoPath)
            self::$videoPath = Yii::getPathOfAlias('webroot').'/video';

        if (!file_exists(self::$pixPath))
            mkdir(self::$pixPath);

        $this->setAliases(array(
            'giiy.*',
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