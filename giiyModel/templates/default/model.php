<?="<?php\n"; ?>

class <?=$modelClass; ?> extends <?=$this->baseModelClass."\n"; ?>
{
    /** @return <?=$modelClass; ?> */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}