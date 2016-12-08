CREATE TABLE config (
	config_name varchar(255) NOT NULL,
	config_value varchar(255) NOT NULL,
	PRIMARY KEY (config_name)
) ENGINE=InnoDB;

CREATE TABLE global (
	workdir varchar(255) NOT NULL,
	language varchar(255) NOT NULL,
	options varchar(255) NOT NULL,
	enableipv6 varchar(3) NOT NULL,
	logformat varchar(255) NOT NULL,
	pathadd varchar(255) NOT NULL,
	libadd varchar(255) NOT NULL,
	imagedir varchar(255) NOT NULL,
	runasdaemon varchar(255) NOT NULL,
	intervals varchar(255) NOT NULL,
	nodetach varchar(255) NOT NULL,
	PRIMARY KEY (workdir)
) ENGINE=InnoDB;

CREATE TABLE global_err (
	workdir varchar(255) NOT NULL,
	language varchar(255) NOT NULL,
	options varchar(255) NOT NULL,
	enableipv6 varchar(3) NOT NULL,
	logformat varchar(255) NOT NULL,
	pathadd varchar(255) NOT NULL,
	libadd varchar(255) NOT NULL,
	imagedir varchar(255) NOT NULL,
	runasdaemon varchar(255) NOT NULL,
	intervals varchar(255) NOT NULL,
	nodetach varchar(255) NOT NULL,
	PRIMARY KEY (workdir)
) ENGINE=InnoDB;

CREATE TABLE agent_ip (
	id mediumint(8) NOT NULL,
	ip char(15) NOT NULL,
	title varchar(255) NOT NULL,
	community varchar(255) NOT NULL,
	PRIMARY KEY (id),
	KEY id (id)
) ENGINE=InnoDB;

CREATE TABLE agent (
	id mediumint(8) NOT NULL,
	ip mediumint(8) NOT NULL,
	title varchar(255) NOT NULL,
	ver_snmp integer NOT NULL,
	trash integer NOT NULL,
	errors integer NOT NULL,
	PRIMARY KEY  (id),
	KEY id (id),
	FOREIGN KEY (`ip`) REFERENCES `agent_ip` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
 	
CREATE TABLE mrtg (
    id mediumint(8) not null,
    filename varchar(255) NOT NULL,
    target varchar(255) NOT NULL,
    maxbytes varchar(255) NOT NULL,
    routeruptime varchar(255),
    routername varchar(255),
    ipv4only varchar(255),
    absmax varchar(255),
    unscaled varchar(4),
    withpeak varchar(4),
    suppress char(1),
    xsize varchar(255),
    ysize varchar(255),
    xzoom varchar(255),
    yzoom varchar(255),
    xscale varchar(255),
    yscale varchar(255),
    ytics varchar(255),
    yticsfactor varchar(255),
    factor varchar(255),
    step varchar(255),
    options varchar(255),
    kmg varchar(255),
    colours varchar(255),
    ylegend varchar(255),
    shortlegend varchar(255),
    legend1 varchar(255),
    legend2 varchar(255),
    legend3 varchar(255),
    legend4 varchar(255),
    legendi varchar(255),
    legendo varchar(255),
    timezone varchar(255),
    weekformat varchar(255),
    rrdrowcount varchar(255),
    timestrpos varchar(255),
    timestrfmt varchar(255),
    kilo varchar(255),
    rrdrowcount30m varchar(255),
    rrdrowcount2h varchar(255),
    rrdrowcount1d varchar(255),
    rrdhwrras varchar(255),
    sfilename varchar(255),
    setenv varchar(255),
    pagetop varchar(255),
    PRIMARY KEY  (id),
    KEY id (id),
    FOREIGN KEY (id) REFERENCES agent (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mrtg_group (
	id mediumint(8) NOT NULL,
	title varchar(255) NOT NULL,
	PRIMARY KEY (id),
	KEY id (id)
) ENGINE=InnoDB;

CREATE TABLE mrtg_subgroup (
	id mediumint(8) NOT NULL,
	group_id mediumint(8) NOT NULL,
	levels mediumint(8),
	title varchar(255) NOT NULL,
	PRIMARY KEY (id),
	KEY group_id (id),
	FOREIGN KEY (`group_id`) REFERENCES `mrtg_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE templates (
	id mediumint(8) NOT NULL,
	agent_id mediumint(8) NOT NULL,
	group_id mediumint(8) NOT NULL,
	subgroup_id mediumint(8),
	row_set integer NOT NULL,
	column_set integer NOT NULL,
	hide_set integer NOT NULL,
	PRIMARY KEY (id),
	KEY id (id),
	FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`) ON DELETE CASCADE,
	FOREIGN KEY (`group_id`) REFERENCES `mrtg_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

