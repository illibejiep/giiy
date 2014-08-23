<?php

class GiiyVideo extends BaseGiiyVideo implements IFileBased, Iillustrated
{
    private $_tmpPath;
    /** @return GiiyVideo */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function getPath()
    {
        if ($this->id)
            return GiiyModule::$videoPath.DIRECTORY_SEPARATOR.$this->id.'.'.$this->getType();

        return null;
    }
    public function getUrl()
    {
        return GiiyModule::$videoUrl.DIRECTORY_SEPARATOR.$this->id.'.'.$this->getType();
    }

    public function setFile($path)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);
        if(!in_array($mime,GiiyVideoTypeEnum::$mime))
            throw new CException('wrong video file');

        $movie = new FFmpegMovie($path);

        $this->type = GiiyVideoTypeEnum::createByMime($mime);;
        if ($this->type->id != GiiyVideoTypeEnum::FLV) {
            $movie->convertToFLV();
            $this->type = new GiiyVideoTypeEnum(GiiyVideoTypeEnum::FLV);
            $movie = new FFmpegMovie($path);
        }

        $this->width = $movie->getFrameWidth();
        $this->height = $movie->getFrameHeight();
        $this->duration = $movie->getDuration();
        $this->bit_rate = $movie->getVideoBitRate();
        $this->codec = $movie->getVideoCodec();
        try {
            $gd = $movie->getFrameAtTime($this->duration/2)->toGDImage();
            $tmpFile = TMP_PATH.DIRECTORY_SEPARATOR.uniqid('video_');
            imagepng($gd,$tmpFile);

            $preview = new GiiyPicture();

            $preview->name = 'video preview for '.$this->name;
            $preview->setFile($tmpFile);
            $preview->save();
            $this->picture = $preview;

            unlink($tmpFile);
        } catch (Exception $e) {

        }
        if (!$this->id)
            $this->_tmpPath = $path;
        elseif ($path != $this->getPath())
            copy($path,$this->getPath());
    }

    protected function afterSave()
    {
        if ($this->_tmpPath) {
            copy($this->_tmpPath,$this->getPath());
            $this->_tmpPath = null;
        }

        parent::afterSave();
    }

    /** @return GiiyPicture|null */
    public function getPicture()
    {
        return $this->getRelated('picture');
    }
}