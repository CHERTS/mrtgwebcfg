<?php

define('IN_ADMIN', true);

require "./../config.php";
require "./../function.php";

$MRTGLang = ($MRTGAutoLanguage == '1') ? Get_Language() : $MRTGLanguage;
require "./../lang/$MRTGLang.php";

if (Check_Access() != "Allow") MRTGErrors(6);

if($SQL_Type == "mysql") {
	$db = @mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or MRTGErrors(3);
	$sdb = @mysql_select_db($SQL_Base, $db) or MRTGErrors(3);
} else $db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or MRTGErrors(3);

HTMLTopPrint($MRTGMsg[32]);

$self = $_SERVER['PHP_SELF'];

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[32]</b></td></tr></table><br>";

if (isset($add) && !(isset($save)) ) {

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id from agent order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent.id from agent order by id asc");
		$rows = pg_num_rows($result);
	}
	$ids = 0;
	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if ( $i != $row[0] && $ids == 0) {
			$id = $i;
			$ids = 1;
		}
	}
	if ($ids == 0) $id = $rows;

	// Select IP address
	if($SQL_Type == "mysql") {
		$result_ip = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = mysql_num_rows($result_ip);
	} else {
		$result_ip = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = pg_num_rows($result_ip);
	}
	// End

	// SID
	if($SQL_Type == "mysql") {
		$result_sid = mysql_query("select templates.id from templates order by id asc");
		$rows_sid = mysql_num_rows($result_sid);
	} else {
		$result_sid = pg_query($db, "select templates.id from templates order by id asc");
		$rows_sid = pg_num_rows($result_sid);
	}
	$sids = 0;
	for ($i=0; $i<$rows_sid; $i++) {
		$row_sid = ($SQL_Type == "mysql") ? mysql_fetch_row($result_sid) : pg_fetch_row($result_sid, $i);
		if ( $i != $row_sid[0] && $sids == 0) {
			$sid = $i;
			$sids = 1;
		}
	}
	if ($sids == 0) $sid = $rows_sid;
	// End

	print "<table width='100%' align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self' name='mrtg_param'><tr bgcolor='#AABBCC' align=center><td width=20%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>ID</td><td><input type=hidden name='id' value='$id'></input><font color='#0000FF'><b>$id</b></font></td></tr>";

	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>$MRTGMsg[271]</td>
		<td><select name='mrtg_param_value' onChange='ChangeMRTGParam()'>
				<option value='0'>$MRTGMsg[272]
				<option value='-1'>SNMP
				<option value='-2'>OTHER
				</select>";

	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>Title</td><td><input type='text' name='title' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>RRD FileName (Host Name)</td><td><input type='text' name='filename' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>MaxBytes</td><td><input type='text' name='maxbytes' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0' id='-4' style='display: none'><td class=red>Other target value</td><td><input type='text' name='sfilename' value=''></input></td></tr>";

	// List IP hosts
	print "<tr align=center bgcolor='#F0F0F0' id='-2' style='display: none'><td class=red>IP (Target)</td><td>";
	print "<select name='ip'>";
	for ($h=0; $h<$rows_ip; $h++) {
		$row_ip = ($SQL_Type == "mysql") ? mysql_fetch_row($result_ip) : pg_fetch_row($result_ip, $h);
		$ip = split("/", $row_ip[1]);
		$row_ip[1] = $ip[0];
		if ($row[1] == $row_ip[1]) print "<option selected value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
		else print "<option value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
	}
	print "</select></td></tr>";
	// End

	// SNMP Version
	print "<tr align=center bgcolor='#F0F0F0' id='-3' style='display: none'><td class=red>SNMP Version (Target)</td><td>
		<select name='ver_snmp'>
		<option selected value='1'>1
		<option value='2'>2
		</select></td></tr>";
	// End

	print "<tr align=center bgcolor='#F0F0F0' id='-1' style='display: none'><td class=red>Router Port or SNMP OID (Target)</td><td><input type='text' name='target' value=''></input></td></tr>";

	print "<tr align=center bgcolor='#F0F0F0'><td>RouterUptime</td><td>
		<select name='routeruptime'>
		<option selected value=''>$MRTGMsg[167]
		<option value='1'>$MRTGMsg[166]
		</select></td></tr>";

	print "<tr align=center bgcolor='#F0F0F0'><td>SetEnv</td><td><input type='text' name='setenv' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>PageTop</td><td><input type='text' name='pagetop' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Options</td><td><input type='text' name='options' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>AbsMax</td><td><input type='text' name='absmax' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Unscaled</td><td><input type='text' name='unscaled' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>WithPeak</td><td><input type='text' name='withpeak' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Suppress</td><td><input type='text' name='suppress' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Colours</td><td><input type='text' name='colours' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YLegend</td><td><input type='text' name='ylegend' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>ShortLegend</td><td><input type='text' name='shortlegend' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend1</td><td><input type='text' name='legend1' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend2</td><td><input type='text' name='legend2' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend3</td><td><input type='text' name='legend3' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend4</td><td><input type='text' name='legend4' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>LegendI</td><td><input type='text' name='legendi' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>LegendO</td><td><input type='text' name='legendo' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>kMG</td><td><input type='text' name='kmg' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>IPv4Only</td><td><select name='ipv4only'><option selected value=''>$MRTGMsg[167]<option value='Yes'>Yes<option value='No'>No</select></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RouterName</td><td><input type='text' name='routername' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>XSize</td><td><input type='text' name='xsize' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YSize</td><td><input type='text' name='ysize' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>XZoom</td><td><input type='text' name='xzoom' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YZoom</td><td><input type='text' name='yzoom' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>XScale</td><td><input type='text' name='xscale' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YScale</td><td><input type='text' name='yscale' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YTics</td><td><input type='text' name='ytics' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YTicsFactor</td><td><input type='text' name='yticsfactor' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Factor</td><td><input type='text' name='factor' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Step</td><td><input type='text' name='step' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Timezone</td><td><input type='text' name='timezone' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Weekformat</td><td><input type='text' name='weekformat' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount</td><td><input type='text' name='rrdrowcount' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>TimeStrPos</td><td><input type='text' name='timestrpos' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>TimeStrFmt</td><td><input type='text' name='timestrfmt' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>kilo</td><td><input type='text' name='kilo' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount30m</td><td><input type='text' name='rrdrowcount30m' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount2h</td><td><input type='text' name='rrdrowcount2h' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount1d</td><td><input type='text' name='rrdrowcount1d' value=''></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDHWRRAs</td><td><input type='text' name='rrdhwrras' value=''></input></td></tr>";

	print "<tr bgcolor='#F0F0F0' align=center><td colspan=2><input type=hidden name=save value='set'><input type=hidden name=sid value='$sid'><input type='submit' name='submit' class='submit_button' title='$MRTGMsg[43]' value='$MRTGMsg[43]'></input></td></tr></form></table>";

	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post action='index.php'><input type=submit class='submit_main_button' style='width:100px' value='$MRTGMsg[24]'></form></td></tr></table>";

}

if ( isset($save) ) {

	if($mrtg_param_value == '0') {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[273]</b><br>";
		print "<form method=post ACTION='add.php'><input type=hidden name=add value='set'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else if ($title == '' || $filename == '' || $maxbytes == '') {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br>";
		print "<form method=post ACTION='add.php'><input type=hidden name=add value='set'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else if ( $mrtg_param_value == '-1' && ($ip == '' || $ver_snmp == '' || $target == '') ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br>";
		print "<form method=post ACTION='add.php'><input type=hidden name=add value='set'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else if ( $mrtg_param_value == '-2' && $sfilename == '' ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[274]</b><br>";
		print "<form method=post ACTION='add.php'><input type=hidden name=add value='set'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else if ( CheckFileName($filename) == 1 ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[120]</b><br>";
		print "<form method=post ACTION='add.php'><input type=hidden name=add value='set'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else {
		$filename = strtolower($filename);
		if($mrtg_param_value == "-2") $ver_snmp = "0";
		if($SQL_Type == "mysql") {
			$result_agent = @mysql_query("insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$id.",'".$ip."','".$title."','".$ver_snmp."',0,0)");
			$result_mrtg = @mysql_query("insert into mrtg (id,target,filename,maxbytes,routeruptime,routername,ipv4only,absmax,unscaled,withpeak,suppress,xsize,ysize,xzoom,yzoom,xscale,yscale,ytics,yticsfactor,factor,step,options,kmg,colours,ylegend,shortlegend,legend1,legend2,legend3,legend4,legendi,legendo,timezone,weekformat,rrdrowcount,timestrpos,timestrfmt,kilo,rrdrowcount30m,rrdrowcount2h,rrdrowcount1d,rrdhwrras,sfilename,setenv,pagetop) VALUE (".$id.",'".$target."','".$filename."','".$maxbytes."','".$routeruptime."','".$routername."','".$ipv4only."','".$absmax."','".$unscaled."','".$withpeak."','".$suppress."','".$xsize."','".$ysize."','".$xzoom."','".$yzoom."','".$xscale."','".$yscale."','".$ytics."','".$yticsfactor."','".$factor."','".$step."','".$options."','".$kmg."','".$colours."','".$ylegend."','".$shortlegend."','".$legend1."','".$legend2."','".$legend3."','".$legend4."','".$legendi."','".$legendo."','".$timezone."','".$weekformat."','".$rrdrowcount."','".$timestrpos."','".$timestrfmt."','".$kilo."','".$rrdrowcount30m."','".$rrdrowcount2h."','".$rrdrowcount1d."','".$rrdhwrras."','".$sfilename."','".$setenv."','".$pagetop."')");
		} else {
			$result_agent = @pg_query($db, "insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$id.",'".$ip."','".$title."','".$ver_snmp."',0,0)");
			$result_mrtg = @pg_query($db, "insert into mrtg (id,target,filename,maxbytes,routeruptime,routername,ipv4only,absmax,unscaled,withpeak,suppress,xsize,ysize,xzoom,yzoom,xscale,yscale,ytics,yticsfactor,factor,step,options,kmg,colours,ylegend,shortlegend,legend1,legend2,legend3,legend4,legendi,legendo,timezone,weekformat,rrdrowcount,timestrpos,timestrfmt,kilo,rrdrowcount30m,rrdrowcount2h,rrdrowcount1d,rrdhwrras,sfilename,setenv,pagetop) VALUE (".$id.",'".$target."','".$filename."','".$maxbytes."','".$routeruptime."','".$routername."','".$ipv4only."','".$absmax."','".$unscaled."','".$withpeak."','".$suppress."','".$xsize."','".$ysize."','".$xzoom."','".$yzoom."','".$xscale."','".$yscale."','".$ytics."','".$yticsfactor."','".$factor."','".$step."','".$options."','".$kmg."','".$colours."','".$ylegend."','".$shortlegend."','".$legend1."','".$legend2."','".$legend3."','".$legend4."','".$legendi."','".$legendo."','".$timezone."','".$weekformat."','".$rrdrowcount."','".$timestrpos."','".$timestrfmt."','".$kilo."','".$rrdrowcount30m."','".$rrdrowcount2h."','".$rrdrowcount1d."','".$rrdhwrras."','".$sfilename."','".$setenv."','".$pagetop."')");
		}
		//$result_templates = pg_query($db, "insert into templates (id, agent_id, group_id, row_set, column_set, hide_set) values(".$sid.",".$id.",0,0,0,0)");
		//if ($result_agent && $result_mrtg && $result_templates) {
		if ($result_agent && $result_mrtg) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] $id $MRTGMsg[51]<br>"; //<br>$MRTGMsg[99] <font color='#0000FF'>$sid</font> $MRTGMsg[100]</b><br>";
			print "<br><form method=post ACTION='index.php'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
			HTMLBottomPrint();
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[50] $id</font></b><br>";
			print "<br><form method=post ACTION='index.php'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
			HTMLBottomPrint();
			exit;
		}
	}
}

HTMLBottomPrint();

?>
