<?php

class m140823_162748_giiyBase extends CDbMigration
{

	public function safeUp()
	{
        $tablePrefix = Yii::app()->getModule('giiy')->tablePrefix;

        list($dbType) = explode(':',Yii::app()->db->connectionString);
        $sqlPath = Yii::getPathOfAlias('giiy.data').DIRECTORY_SEPARATOR.$dbType.'.sql';

        if (file_exists($sqlPath)) {
            $sql = file_get_contents($sqlPath);

            $sql = str_replace('giiy_picture',$tablePrefix.'giiy_picture',$sql);
            $sql = str_replace('giiy_video',$tablePrefix.'giiy_video',$sql);

            $sqlCommands = explode(';',$sql);
            array_pop($sqlCommands);
            foreach($sqlCommands as $sqlCommand)
                $this->execute($sqlCommand);

        } else {
            return false;
        }

        return true;
	}

	public function safeDown()
	{
        $tablePrefix = Yii::app()->getModule('giiy')->tablePrefix;

        $this->dropTable($tablePrefix.'giiy_video');
        $this->dropTable($tablePrefix.'giiy_picture');
	}
}