CREATE TABLE config (
   config_name varchar(255) NOT NULL,
   config_value varchar(255) NOT NULL,
   CONSTRAINT mrtgwebcfg_config_pkey PRIMARY KEY (config_name)
);

CREATE TABLE "global" (
    workdir varchar NOT NULL PRIMARY KEY,
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

CREATE TABLE global_err (
    workdir varchar NOT NULL PRIMARY KEY,
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

CREATE TABLE agent (
    id int2 NOT NULL PRIMARY KEY,
    ip int2 NOT NULL,
    title varchar NOT NULL,
    ver_snmp integer NOT NULL,
    trash integer NOT NULL,
    errors integer NOT NULL
);

CREATE TABLE agent_ip (
    id int2 NOT NULL PRIMARY KEY,
    ip cidr NOT NULL,
    title varchar NOT NULL,
    community varchar NOT NULL
);

CREATE TABLE mrtg (
    id int2 NOT NULL PRIMARY KEY,
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
    pagetop varchar(255)
);

CREATE TABLE templates (
    id int2 NOT NULL PRIMARY KEY,
    agent_id int2 NOT NULL,
    group_id int2 NOT NULL,
    subgroup_id int2,
    row_set integer NOT NULL,
    column_set integer NOT NULL,
    hide_set int2 NOT NULL
);

CREATE TABLE mrtg_group (
    id int2 NOT NULL PRIMARY KEY,
    title varchar NOT NULL
);

CREATE TABLE mrtg_subgroup (
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
