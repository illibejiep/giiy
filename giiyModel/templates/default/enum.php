<?="<?php\n"; ?>

class <?=$modelClass.$enumName; ?>
{

    const SIMPLE = 1;

    static public $names = array(
        self::SIMPLE    => 'простой',
    );

<? if (array_key_exists('params',$columns)):?>
    static public $params = array();

<? endif;?>
    public $id;

    public function __construct($id)
    {
        if (!isset(self::$names[$id]))
            throw new CException('Wront <?=$modelClass;?><?=$enumName;?> value');
        $this->id = $id;
    }

    public function __toString()
    {
        return self::$names[$this->id];
    }
<? if (array_key_exists('params',$columns)):?>

    public static function getParams()
    {
        return self::$params;
    }
<? endif;?>

}