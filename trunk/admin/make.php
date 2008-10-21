<?

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
	$db = mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or MRTGErrors(3);
	$sdb = mysql_select_db($SQL_Base, $db) or MRTGErrors(3);
} else $db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or MRTGErrors(3);

HTMLTopPrint($MRTGMsg[18]);

$id = $real_ip = $title = $ver_snmp = $community = $filename = $target = $interface_ip = $interface_name = $maxbytes = $system = $iftype = $ifname = $ip = $absmax = $withpeak = $options = $colours = $ylegend = $shortlegend = $legend1 = $legend2 = $legend3 = $legend4 = $legendi = $legendo = $kmg = "";

print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[18]</b></td></tr></table><br>";

if ( $p == '') $p = 1;

if( $Deny_ReBuild_MRTG_File == "1" ) {
	print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[142]</b><br>";
	print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
	print "</td></tr></table></div>";
    HTMLBottomPrint();
	exit;
}

if (isset($make) && !(isset($rebuild))) {
	print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[20]</b><br><br>";
	print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
	print "<form method=post ACTION='make.php'><input type=hidden name=p value='$p'><input type=hidden name=make value='set'><input type=hidden name=rebuild value='set'><input type=submit value=\"$MRTGMsg[21]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
	print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[22]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
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
		print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
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
		$result = mysql_query("select distinct agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community, agent.errors, mrtg.filename, mrtg.target, mrtg.interface_ip, mrtg.interface_name, mrtg.maxbytes, mrtg.iftype, mrtg.title_ip, mrtg.absmax, mrtg.withpeak, mrtg.options, mrtg.colours, mrtg.ylegend, mrtg.shortlegend, mrtg.legend1, mrtg.legend2, mrtg.legend3, mrtg.legend4, mrtg.legendi, mrtg.legendo, mrtg.routeruptime, mrtg.kmg, mrtg.unscaled 
						from agent, agent_ip, mrtg
						where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0
						order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select distinct agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community, agent.errors, mrtg.filename, mrtg.target, mrtg.interface_ip, mrtg.interface_name, mrtg.maxbytes, mrtg.iftype, mrtg.title_ip, mrtg.absmax, mrtg.withpeak, mrtg.options, mrtg.colours, mrtg.ylegend, mrtg.shortlegend, mrtg.legend1, mrtg.legend2, mrtg.legend3, mrtg.legend4, mrtg.legendi, mrtg.legendo, mrtg.routeruptime, mrtg.kmg, mrtg.unscaled 
						from agent, agent_ip, mrtg
						where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0
						order by id asc");
		$rows = pg_num_rows($result);
	}

	for ($i=0; $i<$rows; $i++) {

		$id = $real_ip = $title = $ver_snmp = $community = $filename = $target = $interface_ip = $interface_name = $maxbytes = $system = $iftype = $ifname = $ip = $absmax = $withpeak = $options = $colours = $ylegend = $shortlegend = $legend1 = $legend2 = $legend3 = $legend4 = $legendi = $legendo = "";
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		$id = $row[0];
		$real_ip = $row[1];
		$title = $row[2];
		$ver_snmp = $row[3];
		$community = $row[4];
		$errors = $row[5];
		$filename = $row[6];
		$target = $row[7];
		$interface_ip = $row[8];
		$ifname = $interface_name = $row[9];
		$maxbytes = $maxbytes1 = $maxbytes_err = $row[10];
		$iftype = $row[11];
		$ip = $row[12];
		$absmax = $row[13];
		$withpeak = $row[14];
		$options = $row[15];
		$colours = $row[16];
		$ylegend = $row[17];
		$shortlegend = $row[18];
		$legend1 = $row[19];
		$legend2 = $row[20];
		$legend3 = $row[21];
		$legend4 = $row[22];
		$legendi = $row[23];
		$legendo = $row[24];
		$routeruptime = $row[25];
		$kmg = $row[26];
		$unscaled = $row[27];
		$real_ip = split("/", $real_ip);
		$real_ip = $real_ip[0];
		$system = $real_ip;

		//--------------- MRTG v2 -------------------------------------------------------------------------------------
		if ($ver_snmp == "2") fputs ($total, "Target[".$filename."]: ".$target.":".$community."@".$real_ip.":::::2\n");
		else fputs ($total, "Target[".$filename."]: ".$target.":".$community."@".$real_ip."\n");
		if ($routeruptime == "1") fputs ($total, "RouterUptime[".$filename."]: ".$community."@".$real_ip."\n");
		fputs ($total, "Title[".$filename."]: ".$title."\n");
		if($interface_ip != "" || $interface_name != "") fputs ($total, "SetEnv[".$filename."]: MRTG_INT_IP=\"".$interface_ip."\" MRTG_INT_DESCR=\"".$interface_name."\"\n");
		if($maxbytes != "") fputs ($total, "MaxBytes[".$filename."]: ".$maxbytes."\n");
		if($kmg != "") fputs ($total, "kMG[".$filename."]: ".$kmg."\n");
		if($unscaled != "") fputs ($total, "Unscaled[".$filename."]: ".$unscaled."\n");
		if($options != "") fputs ($total, "Options[".$filename."]: ".$options."\n");
		if($ylegend != "") fputs ($total, "YLegend[".$filename."]: ".$ylegend."\n");
		if($shortlegend != "") fputs ($total, "ShortLegend[".$filename."]: ".$shortlegend."\n");
		if($colours != "") fputs ($total, "Colours[".$filename."]: ".$colours."\n");
		if($withpeak != "") fputs ($total, "WithPeak[".$filename."]: ".$withpeak."\n");
		if($legend1 != "") fputs ($total, "Legend1[".$filename."]: ".$legend1."\n");
		if($legend2 != "") fputs ($total, "Legend2[".$filename."]: ".$legend2."\n");
		if($legend3 != "") fputs ($total, "Legend3[".$filename."]: ".$legend3."\n");
		if($legend4 != "") fputs ($total, "Legend4[".$filename."]: ".$legend4."\n");
		if($legendi != "") fputs ($total, "LegendI[".$filename."]: ".$legendi."\n");
		if($legendo != "") fputs ($total, "LegendO[".$filename."]: ".$legendo."\n");
		if($absmax != "") fputs ($total, "AbsMax[".$filename."]: ".$absmax."\n");
		fputs ($total, "PageTop[".$filename."]: <H1>".$title."</H1>\n");
		if( !ereg("noinfo", $options) ) {
			fputs ($total, " <TABLE>\n");
			fputs ($total, "   <TR><TD>System:</TD><TD>".$system."</TD></TR>\n");
			fputs ($total, "   <TR><TD>Description:</TD><TD>".$title."</TD></TR>\n");
			if( $iftype != "" ) fputs ($total, "   <TR><TD>ifType:</TD><TD>".$iftype."</TD></TR>\n");
			if( $ifname != "" ) fputs ($total, "   <TR><TD>ifName:</TD><TD>".$ifname."</TD></TR>\n");
			if($absmax != "") $maxbytes = $absmax;
			if ($maxbytes < 1024) $maxbytes = $maxbytes." Bytes/s";
			elseif ($maxbytes >= 1024 && $maxbytes < 1048576) $maxbytes = number_format($maxbytes/1024, 2)." KBytes/s";
			else $maxbytes = number_format($maxbytes/1048576, 2)." MBytes/s";
			fputs ($total, "   <TR><TD>Max Speed:</TD><TD>".$maxbytes."</TD></TR>\n");
			if($ip != "") fputs ($total, "   <TR><TD>IP:</TD><TD>".$ip."</TD></TR>\n");
			fputs ($total, " </TABLE>\n");
		}
		fputs ($total, "\n");
		//--------------- MRTG v2 Конец----------------------------------------------------------------------------------

		//--------------- MRTG v2 Err -------------------------------------------------------------------------------------
		if( file_exists($MRTG_Config_Err)  && $errors == "1" ) {
			fputs ($total_err, "Target[".$filename."]: .1.3.6.1.2.1.2.2.1.14.".$target."&.1.3.6.1.2.1.2.2.1.20.".$target.":".$community."@".$real_ip."\n");
			fputs ($total_err, "Title[".$filename."]: ".$title."\n");
			if($interface_ip != "" || $interface_name != "" ) fputs ($total_err, "SetEnv[".$filename."]: MRTG_INT_IP=\"".$interface_ip."\" MRTG_INT_DESCR=\"".$interface_name."\"\n");
			if($maxbytes != "" ) fputs ($total_err, "MaxBytes[".$filename."]: ".$maxbytes_err."\n");
			if($kmg != "" ) fputs ($total_err, "kMG[".$filename."]: ".$kmg."\n");
			if($unscaled != "") fputs ($total_err, "Unscaled[".$filename."]: ".$unscaled."\n");
			if($options != "") fputs ($total_err, "Options[".$filename."]: ".$options."\n");
			if($ylegend != '') fputs ($total_err, "YLegend[".$filename."]: ".$ylegend."\n");
			if($shortlegend != "") fputs ($total_err, "ShortLegend[".$filename."]: ".$shortlegend."\n");
			if($colours != "") fputs ($total_err, "Colours[".$filename."]: ".$colours."\n");
			if($withpeak != "") fputs ($total_err, "WithPeak[".$filename."]: ".$withpeak."\n");
			if($legend1 != "") fputs ($total_err, "Legend1[".$filename."]: ".$legend1."\n");
			if($legend2 != "") fputs ($total_err, "Legend2[".$filename."]: ".$legend2."\n");
			if($legend3 != "") fputs ($total_err, "Legend3[".$filename."]: ".$legend3."\n");
			if($legend4 != "") fputs ($total_err, "Legend4[".$filename."]: ".$legend4."\n");
			if($legendi != "") fputs ($total_err, "LegendI[".$filename."]: ".$legendi."\n");
			if($legendo != "") fputs ($total_err, "LegendO[".$filename."]: ".$legendo."\n");
			if($absmax != "") fputs ($total_err, "AbsMax[".$filename."]: ".$absmax."\n");
			fputs ($total_err, "PageTop[".$filename."]: <H1>".$title."</H1>\n");
			if( !ereg("noinfo", $options) ) {
				fputs ($total_err, " <TABLE>\n");
				fputs ($total_err, "   <TR><TD>System:</TD><TD>".$system."</TD></TR>\n");
				fputs ($total_err, "   <TR><TD>Description:</TD><TD>".$title."</TD></TR>\n");
				if( $iftype != "" ) fputs ($total_err, "   <TR><TD>ifType:</TD><TD>".$iftype."</TD></TR>\n");
				if( $ifname != "" ) fputs ($total_err, "   <TR><TD>ifName:</TD><TD>".$ifname."</TD></TR>\n");
				if($absmax != "") $maxbytes_err = $absmax;
				if ($maxbytes_err < 1024) $maxbytes_err = $maxbytes_err." Bytes/s";
				elseif ($maxbytes_err >= 1024 && $maxbytes_err < 1048576) $maxbytes_err = number_format($maxbytes_err/1024, 2)." KBytes/s";
				else $maxbytes_err = number_format($maxbytes_err/1048576, 2)." MBytes/s";
				fputs ($total_err, "   <TR><TD>Max Speed:</TD><TD>".$maxbytes_err."</TD></TR>\n");
				if($ip != "") fputs ($total_err, "   <TR><TD>IP:</TD><TD>".$ip."</TD></TR>\n");
				fputs ($total_err, " </TABLE>\n");
			}
			fputs ($total_err, "\n");
		}
		//--------------- MRTG v2  Err Конец----------------------------------------------------------------------------------
	}

	print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[23]</b><br>";
	print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
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
