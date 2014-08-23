-- pictures

CREATE TABLE giiy_picture (
	id                   serial  NOT NULL,
	type_enum            integer  ,
	"name"               text  ,
	width                integer  ,
	height               integer  ,
	created              timestamp  NOT NULL,
	modified             timestamp DEFAULT now() NOT NULL,
	description          text  ,
	announce             text  ,
	CONSTRAINT pk_giiy_picture PRIMARY KEY ( id )
 );

-- videos
CREATE TABLE giiy_video (
	id                   serial  NOT NULL,
	type_enum            integer  ,
	"name"               varchar(1024)  ,
	width                integer  ,
	height               integer  ,
	picture_id           integer  ,
	created              timestamp  NOT NULL,
	modified             timestamp  NOT NULL,
	duration             float8  ,
	bit_rate             integer  ,
	codec                varchar(1024)  ,
	description          text  ,
	CONSTRAINT pk_giiy_video PRIMARY KEY ( id )
 );

ALTER TABLE giiy_video ADD CONSTRAINT giiy_video_giiy_picture_id_fkey FOREIGN KEY ( picture_id ) REFERENCES giiy_picture( id ) ON DELETE SET NULL ON UPDATE CASCADE;