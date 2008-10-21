CREATE TABLE "config" (
   config_name varchar(255) NOT NULL,
   config_value varchar(255) NOT NULL,
   CONSTRAINT mrtgwebcfg_config_pkey PRIMARY KEY (config_name)
);

CREATE TABLE "global" (
	workdir varchar NOT NULL,
	language varchar NOT NULL,
	options varchar NOT NULL,
	enableipv6 varchar(3) NOT NULL,
	logformat varchar NOT NULL,
	pathadd varchar NOT NULL,
	libadd varchar NOT NULL,
	imagedir varchar NOT NULL,
	runasdaemon varchar(3) NOT NULL,
	intervals varchar(10) NOT NULL,
	nodetach varchar(3) NOT NULL
);

CREATE TABLE "global_err" (
	workdir varchar NOT NULL,
	language varchar NOT NULL,
	options varchar NOT NULL,
	enableipv6 varchar(3) NOT NULL,
	logformat varchar NOT NULL,
	pathadd varchar NOT NULL,
	libadd varchar NOT NULL,
	imagedir varchar NOT NULL,
	runasdaemon varchar(3) NOT NULL,
	intervals varchar(10) NOT NULL,
	nodetach varchar(3) NOT NULL
);

CREATE TABLE "agent" (
	id int2 NOT NULL PRIMARY KEY,
	ip int2 NOT NULL,
	title varchar NOT NULL,
	ver_snmp integer NOT NULL,
	trash integer NOT NULL,
	errors integer NOT NULL
);

CREATE TABLE "agent_ip" (
	id int2 NOT NULL PRIMARY KEY,
	ip cidr NOT NULL,
	title varchar NOT NULL,
	community varchar NOT NULL
);

CREATE TABLE "mrtg" (
	id int2 NOT NULL,
	filename varchar NOT NULL,
	target varchar NOT NULL,
	interface_ip varchar(15),
	interface_name varchar,
	maxbytes integer NOT NULL,
	iftype varchar,
	title_ip varchar,
	routeruptime varchar,
	routername varchar,
	ipv4only varchar,
	pagefoot varchar,
	addhead varchar,
	bodytag varchar,
	absmax varchar,
	unscaled varchar(4),
	withpeak varchar(4),
	suppress char(1),
	extension varchar(5),
	directory varchar,
	xsize smallint,
	ysize smallint,
	xzoom numeric,
	yzoom numeric,
	xscale numeric,
	yscale numeric,
	ytics smallint,
	yticsfactor numeric,
	factor smallint,
	step smallint,
	pngtitle varchar,
	options varchar,
	kmg varchar,
	colours varchar,
	background char(7),
	ylegend varchar,
	shortlegend varchar,
	legend1 varchar,
	legend2 varchar,
	legend3 varchar,
	legend4 varchar,
	legendi varchar,
	legendo varchar,
	timezone varchar,
	weekformat char(1),
	rrdrowcount integer,
	timestrpos varchar(3),
	timestrfmt varchar
);

CREATE TABLE "templates" (
	id int2 NOT NULL,
	agent_id int2 NOT NULL,
	group_id int2 NOT NULL,
	subgroup_id int2,
	row_set integer NOT NULL,
	column_set integer NOT NULL,
	hide_set int2 NOT NULL
);

CREATE TABLE "mrtg_group" (
	id int2 NOT NULL PRIMARY KEY,
	title varchar NOT NULL
);

CREATE TABLE "mrtg_subgroup" (
	id int2 NOT NULL PRIMARY KEY,
	group_id int2 NOT NULL,
	levels int2,
	title varchar NOT NULL
);

ALTER TABLE mrtg
   ADD FOREIGN KEY (id)
   REFERENCES agent (id) ON DELETE CASCADE;

ALTER TABLE agent
   ADD FOREIGN KEY (ip)
   REFERENCES agent_ip (id) ON DELETE CASCADE;

ALTER TABLE templates
   ADD FOREIGN KEY (agent_id)
   REFERENCES agent (id) ON DELETE CASCADE;

ALTER TABLE templates
   ADD FOREIGN KEY (group_id)
   REFERENCES mrtg_group (id) ON DELETE CASCADE;

ALTER TABLE mrtg_subgroup
   ADD FOREIGN KEY (group_id)
   REFERENCES mrtg_group (id) ON DELETE CASCADE;

ALTER TABLE templates
   ADD FOREIGN KEY (subgroup_id)
   REFERENCES mrtg_subgroup (id) ON DELETE CASCADE;
