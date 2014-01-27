<?php

class VideoTypeEnum{

		const _3G2		= 1;
		const _3GP		= 2;
		const _3GP2		= 3;
		const _3GPP		= 4;
		const ASF		= 5;
		const ASR		= 6;
		const ASX		= 7;
		const AVI		= 8;
		const DIF		= 9;
		const DV		= 10;
		const FLV		= 11;
		const IVF		= 12;
		const LSF		= 13;
		const LSX		= 14;
		const M1V		= 15;
		const M2T		= 16;
		const M2TS		= 17;
		const M2V		= 18;
		const M4V		= 19;
		const MOD		= 20;
		const MOV		= 21;
		const MOVIE		= 22;
		const MP2		= 23;
		const MP2V		= 24;
		const MP4		= 25;
		const MP4V		= 26;
		const MPA		= 27;
		const MPE		= 28;
		const MPEG		= 29;
		const MPG		= 30;
		const MPV2		= 31;
		const MQV		= 32;
		const MTS		= 33;
		const NSC		= 34;
		const QT		= 35;
		const TS		= 36;
		const TTS		= 37;
		const VBK		= 38;
		const WM		= 39;
		const WMP		= 40;
		const WMV		= 41;
		const WMX		= 42;
		const WVX		= 43;

    static public $names = array(
        0           => 'unknown',
        self::_3G2	=> '3g2',
        self::_3GP	=> '3gp',
        self::_3GP2	=> '3gp2',
        self::_3GPP	=> '3gpp',
        self::ASF	=> 'asf',
        self::ASR	=> 'asr',
        self::ASX	=> 'asx',
        self::AVI	=> 'avi',
        self::DIF	=> 'dif',
        self::DV	=> 'dv',
        self::FLV	=> 'flv',
        self::IVF	=> 'ivf',
        self::LSF	=> 'lsf',
        self::LSX	=> 'lsx',
        self::M1V	=> 'm1v',
        self::M2T	=> 'm2t',
        self::M2TS	=> 'm2ts',
        self::M2V	=> 'm2v',
        self::M4V	=> 'm4v',
        self::MOD	=> 'mod',
        self::MOV	=> 'mov',
        self::MOVIE	=> 'movie',
        self::MP2	=> 'mp2',
        self::MP2V	=> 'mp2v',
        self::MP4	=> 'mp4',
        self::MP4V	=> 'mp4v',
        self::MPA	=> 'mpa',
        self::MPE	=> 'mpe',
        self::MPEG	=> 'mpeg',
        self::MPG	=> 'mpg',
        self::MPV2	=> 'mpv2',
        self::MQV	=> 'mqv',
        self::MTS	=> 'mts',
        self::NSC	=> 'nsc',
        self::QT	=> 'qt',
        self::TS	=> 'ts',
        self::TTS	=> 'tts',
        self::VBK	=> 'vbk',
        self::WM	=> 'wm',
        self::WMP	=> 'wmp',
        self::WMV	=> 'wmv',
        self::WMX	=> 'wmx',
        self::WVX	=> 'wvx',
    );

    static public $mime = array(
        self::_3G2	=> 'video/3gpp2',
        self::_3GP	=> 'video/3gpp',
        self::_3GP2	=> 'video/3gpp2',
        self::_3GPP	=> 'video/3gpp',
        self::ASF	=> 'video/x-ms-asf',
        self::ASR	=> 'video/x-ms-asf',
        self::ASX	=> 'video/x-ms-asf',
        self::AVI	=> 'video/x-msvideo',
        self::DIF	=> 'video/x-dv',
        self::DV	=> 'video/x-dv',
        self::FLV	=> 'video/x-flv',
        self::IVF	=> 'video/x-ivf',
        self::LSF	=> 'video/x-la-asf',
        self::LSX	=> 'video/x-la-asf',
        self::M1V	=> 'video/mpeg',
        self::M2T	=> 'video/vnd.dlna.mpeg-tts',
        self::M2TS	=> 'video/vnd.dlna.mpeg-tts',
        self::M2V	=> 'video/mpeg',
        self::M4V	=> 'video/x-m4v',
        self::MOD	=> 'video/mpeg',
        self::MOV	=> 'video/quicktime',
        self::MOVIE	=> 'video/x-sgi-movie',
        self::MP2	=> 'video/mpeg',
        self::MP2V	=> 'video/mpeg',
        self::MP4	=> 'video/mp4',
        self::MP4V	=> 'video/mp4',
        self::MPA	=> 'video/mpeg',
        self::MPE	=> 'video/mpeg',
        self::MPEG	=> 'video/mpeg',
        self::MPG	=> 'video/mpeg',
        self::MPV2	=> 'video/mpeg',
        self::MQV	=> 'video/quicktime',
        self::MTS	=> 'video/vnd.dlna.mpeg-tts',
        self::NSC	=> 'video/x-ms-asf',
        self::QT	=> 'video/quicktime',
        self::TS	=> 'video/vnd.dlna.mpeg-tts',
        self::TTS	=> 'video/vnd.dlna.mpeg-tts',
        self::VBK	=> 'video/mpeg',
        self::WM	=> 'video/x-ms-wm',
        self::WMP	=> 'video/x-ms-wmp',
        self::WMV	=> 'video/x-ms-wmv',
        self::WMX	=> 'video/x-ms-wmx',
        self::WVX	=> 'video/x-ms-wvx',
    );
    
    public $id;

    public function __construct($id) {
        if (!isset(self::$names[$id]))
            throw new CException('Wront Video typeenum value');
        $this->id = $id;
    }

    public function __toString(){
        return self::$names[$this->id];
    }

    public function getMime()
    {
        return self::$mime[$this->id];
    }

    public static function mimeToExt($mime)
    {
        $type_id = array_search($mime,self::$mime);
        return self::$names[$type_id];
    }

    public static function mimeToTypeId($mime)
    {
        return array_search($mime,self::$mime);
    }
    public static function createByMime($mime)
    {
        return new VideoTypeEnum(self::mimeToTypeId($mime));
    }
}