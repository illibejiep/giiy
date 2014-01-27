<?

class PictureTypeEnum {

    static public $names = array(
            0                       => 'unknown',
            IMAGETYPE_GIF			=> 'gif',
            IMAGETYPE_JPEG			=> 'jpeg',
            IMAGETYPE_PNG			=> 'png',
            IMAGETYPE_SWF			=> 'swf',
            IMAGETYPE_PSD			=> 'psd',
            IMAGETYPE_BMP			=> 'bmp',
            IMAGETYPE_TIFF_II		=> 'tif',
            IMAGETYPE_TIFF_MM		=> 'tif',
            IMAGETYPE_JPC			=> 'jpc',
            IMAGETYPE_JP2			=> 'jp2',
            IMAGETYPE_JPX			=> 'jpx',
            IMAGETYPE_JB2			=> 'jb2',
            IMAGETYPE_SWC			=> 'swc',
            IMAGETYPE_IFF			=> 'iff',
            IMAGETYPE_WBMP			=> 'bmp',
            IMAGETYPE_JPEG2000		=> 'jpc',
            IMAGETYPE_XBM			=> 'xbm',
    );

    public $id;

    public function __construct($id)
    {
        if (!isset(self::$names[$id]))
            throw new CException('Wrong image type');
        $this->id = $id;
    }

    public function __toString()
    {
        return self::$names[$this->id];
    }

    public function getMime()
    {
        return image_type_to_mime_type($this->id);
    }
}