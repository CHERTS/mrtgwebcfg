<?php

define('IN_ADMIN', true);

require "./../config.php";
require "./../function.php";

$MRTG_Config_Err = dirname($_SERVER['SCRIPT_FILENAME'])."/config-err.php";
$MRTG_Config_Err = preg_replace("/admin\//", "", $MRTG_Config_Err);
if(file_exists($MRTG_Config_Err)) require "./../config-err.php";

$MRTGLang = ($MRTGAutoLanguage == '1') ? Get_Language() : $MRTGLanguage;
require "./../lang/$MRTGLang.php";

if (Check_Access() != "Allow") MRTGErrors(6);

if($SQL_Type == "mysql") {
	$db = @mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or MRTGErrors(3);
	$sdb = @mysql_select_db($SQL_Base, $db) or MRTGErrors(3);
} else $db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or MRTGErrors(3);

HTMLTopPrint($MRTGMsg[18]);

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[18]</b></td></tr></table><br>";

if ( $p == '') $p = 1;

if( $Deny_ReBuild_MRTG_File == "1" ) {
	print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[142]</b><br>";
	print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
	print "</td></tr></table></div>";
	HTMLBottomPrint();
	exit;
}

if (isset($make) && !(isset($rebuild))) {
	print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[20]</b><br><br>";
	print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
	print "<form method=post ACTION='make.php'><input type=hidden name=p value='$p'><input type=hidden name=make value='set'><input type=hidden name=rebuild value='set'><input type=submit class='submit_button' value='$MRTGMsg[21]'></form>";
	print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[22]'></form>";
	print "</td></tr></table></td></tr></table></div>";
} 

if (isset($make) && isset($rebuild) && $rebuild != '') {

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id, agent.ip, agent.title, agent.ver_snmp, agent_ip.community, mrtg.filename, mrtg.target, mrtg.maxbytes from agent, mrtg, agent_ip where agent.id=mrtg.id");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent.id,agent.ip,agent.title,agent.ver_snmp,agent_ip.community,mrtg.filename,mrtg.target,mrtg.maxbytes from agent, mrtg, agent_ip where agent.id=mrtg.id");
		$rows = pg_num_rows($result);
	}

	if ($rows == 0) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[33]</b><br>";
		print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
		print "</td></tr></table></div>";
		exit;
	}

	$total = fopen($TMP_MRTG_CFG_File, "w+");
	// Получаем настройки MRTGCfg
	if($SQL_Type == "mysql") {
		$result = mysql_query("select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global");
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global");
		$row = pg_fetch_row($result);
	}
	fputs ($total, "WorkDir: ".$row[0]."\n");
	fputs ($total, "Language: ".$row[1]."\n");
	fputs ($total, "Options[_]: ".$row[2]."\n");
	fputs ($total, "EnableIPv6: ".$row[3]."\n");
	fputs ($total, "LogFormat: ".$row[4]."\n");
	fputs ($total, "PathAdd: ".$row[5]."\n");
	fputs ($total, "LibAdd: ".$row[6]."\n");
	fputs ($total, "ImageDir: ".$row[7]."\n");
	fputs ($total, "RunAsDaemon: ".$row[8]."\n");
	fputs ($total, "Interval: ".$row[9]."\n");
	fputs ($total, "NoDetach: ".$row[10]."\n\n");
	// Конец

	if(file_exists($MRTG_Config_Err)) {
		// Получаем настройки MRTGCfg 2
		$total_err = fopen($TMP_MRTG_CFG_File_Err, "w+");
		if($SQL_Type == "mysql") {
			$result_err = mysql_query("select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global_err");
			$row_err = mysql_fetch_row($result_err);
		} else {
			$result_err = pg_query($db, "select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global_err");
			$row_err = pg_fetch_row($result_err);
		}
		fputs ($total_err, "WorkDir: ".$row_err[0]."\n");
		fputs ($total_err, "Language: ".$row_err[1]."\n");
		fputs ($total_err, "Options[_]: ".$row_err[2]."\n");
		fputs ($total_err, "EnableIPv6: ".$row_err[3]."\n");
		fputs ($total_err, "LogFormat: ".$row_err[4]."\n");
		fputs ($total_err, "PathAdd: ".$row_err[5]."\n");
		fputs ($total_err, "LibAdd: ".$row_err[6]."\n");
		fputs ($total_err, "ImageDir: ".$row_err[7]."\n");
		fputs ($total_err, "RunAsDaemon: ".$row[8]."\n");
		fputs ($total_err, "Interval: ".$row[9]."\n");
		fputs ($total_err, "NoDetach: ".$row[10]."\n\n");
		// Конец
	}

	if($SQL_Type == "mysql") {
		$result = mysql_query("select distinct agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community, mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop
					from agent,agent_ip,mrtg
					where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0
					order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select distinct agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community, mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop
					from agent,agent_ip,mrtg
					where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0
					order by id asc");
		$rows = pg_num_rows($result);
	}

	$MRTG_Settings = array('Target','MaxBytes','RouterUptime','RouterName','IPv4Only','AbsMax','Unscaled','WithPeak','Suppress','XSize','YSize','XZoom','YZoom','XScale','YScale','YTics','YTicsFactor','Factor','Step','Options','kMG','Colours','YLegend','ShortLegend','Legend1','Legend2','Legend3','Legend4','LegendI','LegendO','Timezone','Weekformat','RRDRowCount','TimeStrPos','TimeStrFmt','kilo','RRDRowCount30m','RRDRowCount2h','RRDRowCount1d','RRDHWRRAs','STarget','SetEnv','PageTop');

	for ($i=0; $i<$rows; $i++ ) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		$ip = split("/", $row[1]);
		$row[1] = $ip[0];
		if($row[3] == "0") $full_target = $row[46];
		else if($row[3] == "1") $full_target = $row[5].":".$row[4]."@".$row[1];
		else $full_target = $row[5].":".$row[4]."@".$row[1].":::::2";
		fputs ($total, "Title[".$row[6]."]: ".$row[2]."\n");
		for ($z=6; $z<count($row); $z++ ) {
			if ( $row[$z] != '' ) {
				if($MRTG_Settings[$z-6] == "STarget") echo '';
				else if($MRTG_Settings[$z-6] == "Target") fputs ($total, $MRTG_Settings[$z-6]."[".$row[6]."]: ".$full_target."\n");
				else if($MRTG_Settings[$z-6] == "RouterUptime" && $row[$z] == "1") fputs ($total, "RouterUptime[".$row[6]."]: ".$row[4]."@".$row[1]."\n");
				else fputs ($total, $MRTG_Settings[$z-6]."[".$row[6]."]: ".$row[$z]."\n");
			}
		}
		fputs ($total, "\n");
	}

	if(file_exists($MRTG_Config_Err)) {
		// Errors = 1
		if($SQL_Type == "mysql") {
			$result = mysql_query("select distinct agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community, mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop
						from agent,agent_ip,mrtg
						where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0 and agent.errors=1
						order by id asc");
			$rows = mysql_num_rows($result);
		} else {
			$result = pg_query($db, "select distinct agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community, mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop
						from agent,agent_ip,mrtg
						where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0 and agent.errors=1
						order by id asc");
			$rows = pg_num_rows($result);
		}

		for ($i=0; $i<$rows; $i++ ) {
			$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
			$ip = split("/", $row[1]);
			$row[1] = $ip[0];
			if($row[3] == "0") $full_target = $row[46];
			else if($row[3] == "1") $full_target = ".1.3.6.1.2.1.2.2.1.14.".$row[5]."&.1.3.6.1.2.1.2.2.1.20.".$row[5].":".$row[4]."@".$row[1];
			else $full_target = ".1.3.6.1.2.1.2.2.1.14.".$row[5]."&.1.3.6.1.2.1.2.2.1.20.".$row[5].":".$row[4]."@".$row[1].":::::2";
			fputs ($total_err, "Title[".$row[6]."]: ".$row[2]."\n");
			for ($z=6; $z<count($row); $z++ ) {
				if ( $row[$z] != '' ) {
					if($MRTG_Settings[$z-6] == "STarget") echo '';
					else if($MRTG_Settings[$z-6] == "Target") fputs ($total_err, $MRTG_Settings[$z-6]."[".$row[6]."]: ".$full_target."\n");
					else if($MRTG_Settings[$z-6] == "RouterUptime" && $row[$z] == "1") fputs ($total_err, "RouterUptime[".$row[6]."]: ".$row[4]."@".$row[1]."\n");
					else fputs ($total_err, $MRTG_Settings[$z-6]."[".$row[6]."]: ".$row[$z]."\n");
				}
			}
			fputs ($total_err, "\n");
		}
	}

	print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[23]</b><br>";
	print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
	print "</td></tr></table></div>";

	fclose($total);
	if(file_exists($MRTG_Config_Err)) fclose($total_err);

	$mrtg_date = date("Y-m-d-G:i:s");

	system ("cp $MRTG_CFG_File $BACKUP_MRTG_CFG_File$mrtg_date");
	system ("mv $TMP_MRTG_CFG_File $MRTG_CFG_File");

	if(file_exists($MRTG_Config_Err)) {
		system ("cp $MRTG_CFG_File_Err $BACKUP_MRTG_CFG_File_Err$mrtg_date");
		system ("mv $TMP_MRTG_CFG_File_Err $MRTG_CFG_File_Err");
	}
}

HTMLBottomPrint();

?>
