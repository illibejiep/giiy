<?php

/**
 * @property $alt string
 */
class GiiyPicture extends BaseGiiyPicture implements IFileBased, Iillustrated
{
    private $_tmpPath = null;
    /** @return GiiyPicture */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function getPath()
    {
        if ($this->id)
            return GiiyModule::$pixPath. DIRECTORY_SEPARATOR .$this->id.'.'.$this->getType();

        return null;
    }

    public function getResizePath($width,$height)
    {
        if ($this->id)
            return $this->getResizeDir().DIRECTORY_SEPARATOR.$width.'x'.$height.'.'.$this->getType();

        return null;
    }

    public function getResizeDir()
    {
        if ($this->id) {
            $dir = (int)($this->id/1000);
            $dir .= DIRECTORY_SEPARATOR . ($this->id - 1000*(int)($this->id/1000));
            return GiiyModule::$pixPath.DIRECTORY_SEPARATOR.'resize' .DIRECTORY_SEPARATOR. $dir;
        }

        return null;
    }

    public function getUrl()
    {
        return GiiyModule::$pixUrl . '/' . $this->id.'.'.$this->getType();
    }

    public function getResizeUrl($width,$height)
    {
        if ($this->id) {
            $dir = (int)($this->id/1000);
            $dir .= '/' . ($this->id -1000*(int)($this->id/1000));
            return GiiyModule::$pixUrl . '/resize/' . $dir . '/' . $width.'x'.$height.'.'.$this->getType();
        }

        return null;
    }

    public function resize($width,$height)
    {
        if (!$this->id)
            return null;

        $path = $this->getResizePath($width,$height);
        if (
            (!file_exists($path) || filemtime($path) <= strtotime($this->modified))
            && file_exists($this->getPath())
        ) {
            $image = new Image($this->getPath());

            if (!$width)
                $width = round($height*$this->width/$this->height);
            if (!$height)
                $height = round($width*$this->height/$this->width);

            $image->centeredpreview($width,$height);
            if (!file_exists(dirname($path)))
                mkdir(dirname($path),0777,true);
            $image->save($path);
        }

        return $this->getResizeUrl($width,$height);
    }

    public function cropResize($x1,$x2,$y1,$y2,$w,$h)
    {

        if (!$this->id )
            return null;

        $path = $this->getResizePath($w,$h);
        if (file_exists($this->getPath())) {
            $image = new Image($this->getPath());
            $image->crop($x2-$x1,$y2-$y1,$y1,$x1);
            $image->resize($w,$h);
            if (!file_exists(dirname($path)))
                mkdir(dirname($path),0777,true);
            $image->save($path);
        }

        return $this->getResizeUrl($w,$h);
    }

    public function getAlt()
    {
        return $this->name;
    }

    public function setFile($path)
    {
        /** @var Image $image*/
        $image = Yii::app()->image->load($path);
        if(!$image)
            throw new CException('whront image file');
        $this->type_enum = exif_imagetype($path);
        $this->height = $image->height;
        $this->width = $image->width;

        if (!$this->id)
            $this->_tmpPath = $path;
        else
            copy($path,$this->getPath());
    }

    protected function afterSave()
    {
        if ($this->_tmpPath) {
            copy($this->_tmpPath,$this->getPath());
            $this->_tmpPath = null;
        }

        $resizeDir = dirname($this->getResizePath(1,1));

        if(file_exists($resizeDir))
            foreach(scandir($resizeDir) as $file)
                if(preg_match('/(\d+)x(\d+)\..+/',$file,$matches))
                    $this->resize((int)$matches[1],(int)$matches[2]);


        parent::afterSave();
    }

    /** @return GiiyPicture|null */
    public function getPicture()
    {
        return $this;
    }
}