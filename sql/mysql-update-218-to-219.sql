ALTER TABLE global
    ADD runasdaemon varchar(255) NOT NULL,
    ADD intervals varchar(255) NOT NULL,
    ADD nodetach varchar(255) NOT NULL;

ALTER TABLE global_err
    ADD runasdaemon varchar(255) NOT NULL,
    ADD intervals varchar(255) NOT NULL,
    ADD nodetach varchar(255) NOT NULL;

CREATE TABLE config (
	config_name varchar(255) NOT NULL,
	config_value varchar(255) NOT NULL,
	PRIMARY KEY (config_name)
) TYPE=INNODB;

INSERT INTO config (config_name, config_value) VALUES ('config_id','1');
INSERT INTO config (config_name, config_value) VALUES ('default_style','1');
INSERT INTO config (config_name, config_value) VALUES ('script_path','/mrtgwebcfg/');
INSERT INTO config (config_name, config_value) VALUES ('sitename','MRTGWebCfg');
INSERT INTO config (config_name, config_value) VALUES ('system_disable','0');
INSERT INTO config (config_name, config_value) VALUES ('version','2.19');
INSERT INTO config (config_name, config_value) VALUES ('default_dateformat','D M d, Y g:i a');
INSERT INTO config (config_name, config_value) VALUES ('timezone','0');
