
-- pictures

CREATE TABLE giiy_picture (
	id                   int(11) NOT NULL AUTO_INCREMENT,
	type_enum            int(11)  ,
	"name"               varchar(1024)  ,
	width                int(11)  ,
	height               int(11)  ,
	created              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  ,
	modified             TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'  ,
	description          text  ,
	announce             text  ,
	PRIMARY KEY (id),
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- videos
CREATE TABLE giiy_video (
	id                   int(11) NOT NULL AUTO_INCREMENT  ,
	type_enum            int(11)  ,
	"name"               varchar(1024)  ,
	width                int(11)  ,
	height               int(11)  ,
	picture_id           int(11)  ,
	created              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  ,
	modified             TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'  ,
	duration             float  ,
	bit_rate             int(11)  ,
	codec                varchar(1024)  ,
	description          text  ,
	PRIMARY KEY (id),
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
