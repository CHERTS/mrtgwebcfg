UPDATE config SET config_value='2.20' WHERE config_name='version';

ALTER TABLE mrtg
    DROP interface_ip,
    DROP interface_name,
    DROP iftype,
    DROP title_ip,
    DROP pagefoot,
    DROP addhead,
    DROP bodytag,
    DROP extension,
    DROP directory,
    DROP pngtitle,
    DROP background;

ALTER TABLE mrtg
    ADD kilo varchar(255),
    ADD rrdrowcount30m varchar(255),
    ADD rrdrowcount2h varchar(255),
    ADD rrdrowcount1d varchar(255),
    ADD rrdhwrras varchar(255),
    ADD sfilename varchar(255),
    ADD setenv varchar(255),
    ADD pagetop varchar(255);

ALTER TABLE mrtg
    MODIFY filename varchar(255),
    MODIFY maxbytes varchar(255),
    MODIFY target varchar(255),
    MODIFY xsize varchar(255),
    MODIFY ysize varchar(255),
    MODIFY xzoom varchar(255),
    MODIFY yzoom varchar(255),
    MODIFY xscale varchar(255),
    MODIFY yscale varchar(255),
    MODIFY ytics varchar(255),
    MODIFY yticsfactor varchar(255),
    MODIFY factor varchar(255),
    MODIFY step varchar(255),
    MODIFY weekformat varchar(255),
    MODIFY rrdrowcount varchar(255),
    MODIFY timestrpos varchar(255),
    MODIFY unscaled varchar(4),
    MODIFY withpeak varchar(4),
    MODIFY suppress char(1),
    MODIFY routeruptime varchar(255),
    MODIFY routername varchar(255),
    MODIFY ipv4only varchar(255),
    MODIFY absmax varchar(255),
    MODIFY options varchar(255),
    MODIFY kmg varchar(255),
    MODIFY colours varchar(255),
    MODIFY ylegend varchar(255),
    MODIFY shortlegend varchar(255),
    MODIFY legend1 varchar(255),
    MODIFY legend2 varchar(255),
    MODIFY legend3 varchar(255),
    MODIFY legend4 varchar(255),
    MODIFY legendi varchar(255),
    MODIFY legendo varchar(255),
    MODIFY timezone varchar(255),
    MODIFY timestrfmt varchar(255);

ALTER TABLE global
    ADD PRIMARY KEY (workdir);

ALTER TABLE global_err
    ADD PRIMARY KEY (workdir);

