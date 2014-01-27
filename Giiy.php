<?php

class Giiy extends CModule
{
    public static $pixPath;
    public static $pixUrl = '/';

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

        if (!file_exists(self::$pixPath))
            mkdir(self::$pixPath);

        $this->setAliases(array(
            'giiy.interfaces.*',
            'giiy.models.*',
            'giiy.models._base.*',
            'giiy.models.enum.*',
            'giiy.*',
            'application.models._base.*',
            'application.models.enum.*',
        ));
    }
}