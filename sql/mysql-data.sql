INSERT INTO config (config_name, config_value) VALUES ('config_id','1');
INSERT INTO config (config_name, config_value) VALUES ('default_style','1');
INSERT INTO config (config_name, config_value) VALUES ('script_path','/mrtgwebcfg/');
INSERT INTO config (config_name, config_value) VALUES ('sitename','MRTGWebCfg');
INSERT INTO config (config_name, config_value) VALUES ('system_disable','0');
INSERT INTO config (config_name, config_value) VALUES ('version','2.20');
INSERT INTO config (config_name, config_value) VALUES ('default_dateformat','D M d, Y g:i a');
INSERT INTO config (config_name, config_value) VALUES ('timezone','0');

INSERT INTO global (workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach) VALUES ('/home/www/mrtgwebcfg/rrd','russian1251','growright, bits','No','rrdtool','/usr/local/bin/','/usr/local/lib/perl5/','/home/www/mrtgwebcfg/images','No','5','No');
INSERT INTO global_err (workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach) VALUES ('/home/www/mrtgwebcfg/rrd-err','russian1251','growright, bits','No','rrdtool','/usr/local/bin/','/usr/local/lib/perl5/','/home/www/mrtgwebcfg/images-err','No','5','No');

INSERT INTO mrtg_group (id, title) VALUES ('0','Default');
INSERT INTO mrtg_group (id, title) VALUES ('100','Trash');

INSERT INTO agent_ip (id, ip, title, community) VALUES ('0','127.0.0.1/32','localhost', 'public');

INSERT INTO agent (id, ip, title, ver_snmp, trash, errors) VALUES ('0','0','Traffic Analysis for em0 on localhost','1','0','1');
INSERT INTO agent (id, ip, title, ver_snmp, trash, errors) VALUES ('1','0','Free Memory on localhost','1','0','0');
INSERT INTO agent (id, ip, title, ver_snmp, trash, errors) VALUES ('2','0','CPU Usage on localhost','1','0','0');
INSERT INTO agent (id, ip, title, ver_snmp, trash, errors) VALUES ('3','0','ROOT disk space on localhost','1','0','0');

INSERT INTO mrtg (id, filename, target, maxbytes, routeruptime, routername, ipv4only, absmax, unscaled, withpeak, suppress, xsize, ysize, xzoom, yzoom, xscale, yscale, ytics, yticsfactor, factor, step, options, kmg, colours, ylegend, shortlegend, legend1, legend2, legend3, legend4, legendi, legendo, timezone, weekformat, rrdrowcount, timestrpos, timestrfmt, kilo, rrdrowcount30m, rrdrowcount2h, rrdrowcount1d, rrdhwrras, sfilename, setenv, pagetop) VALUES (0,'traffic_em0_localhost','1','1250000','','','','','','','','','','','','','','','','','','','','','Traffic Analysis','Traffic Analysis','','','','','','','','','','','','','','','','','','MRTG_INT_IP="127.0.0.1" MRTG_INT_DESCR="em0"','');
INSERT INTO mrtg (id, filename, target, maxbytes, routeruptime, routername, ipv4only, absmax, unscaled, withpeak, suppress, xsize, ysize, xzoom, yzoom, xscale, yscale, ytics, yticsfactor, factor, step, options, kmg, colours, ylegend, shortlegend, legend1, legend2, legend3, legend4, legendi, legendo, timezone, weekformat, rrdrowcount, timestrpos, timestrfmt, kilo, rrdrowcount30m, rrdrowcount2h, rrdrowcount1d, rrdhwrras, sfilename, setenv, pagetop) VALUES (1,'freemem_localhost','1.3.6.1.4.1.2021.4.11.0&.1.3.6.1.4.1.2021.4.11.0','1000000','','','','','','','','','','','','','','','','','','nopercent,growright,gauge,noinfo','k,M,G,T,P,X','','Free memory','Free memory','Free memory, not including swap, in bytes','','','','Free Memory:','','','','','','','','','','','','','','');
INSERT INTO mrtg (id, filename, target, maxbytes, routeruptime, routername, ipv4only, absmax, unscaled, withpeak, suppress, xsize, ysize, xzoom, yzoom, xscale, yscale, ytics, yticsfactor, factor, step, options, kmg, colours, ylegend, shortlegend, legend1, legend2, legend3, legend4, legendi, legendo, timezone, weekformat, rrdrowcount, timestrpos, timestrfmt, kilo, rrdrowcount30m, rrdrowcount2h, rrdrowcount1d, rrdhwrras, sfilename, setenv, pagetop) VALUES (2,'cpu_localhost','1.3.6.1.2.1.25.3.3.1.2.768&1.3.6.1.2.1.25.3.3.1.2.768','100','1','','','','ymwd','','','','','','','','','','','','','growright,gauge,nopercent,noinfo','','','CPU Utilization','CPU Utilization','CPU Utilization','','','','','','','','','','','','','','','','','','');
INSERT INTO mrtg (id, filename, target, maxbytes, routeruptime, routername, ipv4only, absmax, unscaled, withpeak, suppress, xsize, ysize, xzoom, yzoom, xscale, yscale, ytics, yticsfactor, factor, step, options, kmg, colours, ylegend, shortlegend, legend1, legend2, legend3, legend4, legendi, legendo, timezone, weekformat, rrdrowcount, timestrpos, timestrfmt, kilo, rrdrowcount30m, rrdrowcount2h, rrdrowcount1d, rrdhwrras, sfilename, setenv, pagetop) VALUES (3,'root_localhost','1.3.6.1.4.1.2021.9.1.9.1&1.3.6.1.4.1.2021.9.1.9.1','100','1','','','','ymwd','','','','','','','','','','','','','growright,gauge,nopercent,noinfo','','','/ disk space','/ disk space','ROOT disk space','ROOT disk space','','','','','','','','','','','','','','','','','');

INSERT INTO templates (id, agent_id, group_id, subgroup_id, row_set, column_set, hide_set) VALUES ('0','0','0',NULL,'1','1','1');
INSERT INTO templates (id, agent_id, group_id, subgroup_id, row_set, column_set, hide_set) VALUES ('1','1','0',NULL,'1','2','1');
INSERT INTO templates (id, agent_id, group_id, subgroup_id, row_set, column_set, hide_set) VALUES ('2','2','0',NULL,'2','1','1');
INSERT INTO templates (id, agent_id, group_id, subgroup_id, row_set, column_set, hide_set) VALUES ('3','3','0',NULL,'2','2','1');
