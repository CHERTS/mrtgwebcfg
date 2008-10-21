ALTER TABLE global ADD COLUMN runasdaemon varchar(3);
ALTER TABLE global ADD COLUMN intervals varchar(10);
ALTER TABLE global ADD COLUMN nodetach varchar(3);

ALTER TABLE global_err ADD COLUMN runasdaemon varchar(3);
ALTER TABLE global_err ADD COLUMN intervals varchar(10);
ALTER TABLE global_err ADD COLUMN nodetach varchar(3);

CREATE TABLE "config" (
   config_name varchar(255) NOT NULL,
   config_value varchar(255) NOT NULL,
   CONSTRAINT mrtgwebcfg_config_pkey PRIMARY KEY (config_name)
);

INSERT INTO "config" (config_name, config_value) VALUES ('config_id','1');
INSERT INTO "config" (config_name, config_value) VALUES ('default_style','1');
INSERT INTO "config" (config_name, config_value) VALUES ('script_path','/mrtgwebcfg/');
INSERT INTO "config" (config_name, config_value) VALUES ('sitename','MRTGWebCfg');
INSERT INTO "config" (config_name, config_value) VALUES ('system_disable','0');
INSERT INTO "config" (config_name, config_value) VALUES ('version','2.19');
INSERT INTO "config" (config_name, config_value) VALUES ('default_dateformat','D M d, Y g:i a');
INSERT INTO "config" (config_name, config_value) VALUES ('timezone','0');
