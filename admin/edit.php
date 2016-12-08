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

HTMLTopPrint($MRTGMsg[13]);

$self = $_SERVER['PHP_SELF'];
$p = $_GET['p'];

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[13] $MRTGMsg[46] <font color='#0000FF'>$id</font></b></td></tr></table>";

if (isset($id) && !(isset($save)) ) {

	// Запрос на выбор IP хоста
	if($SQL_Type == "mysql") {
		$result_ip = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = mysql_num_rows($result_ip);
	} else {
		$result_ip = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = pg_num_rows($result_ip);
	}
	// Конец

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id,agent.title,agent_ip.ip,agent.ver_snmp,mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.id=".$id);
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select agent.id,agent.title,agent_ip.ip,agent.ver_snmp,mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.id=".$id);
		$row = pg_fetch_row($result);
	}

	if (count($row) < 2) {
		print "<tr bgcolor='#F0F0F0'><td colspan=2 align=center class=red>$MRTGMsg[5]</td></tr></table>";
		exit;
	}
	$ip = split("/", $row[2]);
	$row[2] = $ip[0];

	if($row[3] == '0') { 
		$selected_2 = "selected";
		$st_1 = "display: none";
		$st_2 = "display: none";
		$st_3 = "display: none";
		$st_4 = "display:";
	} else { 
		$selected_1 = "selected";
		$st_1 = "display:";
		$st_2 = "display:";
		$st_3 = "display:";
		$st_4 = "display: none";
	}

	print "<br><table width='100%' align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self' name='mrtg_param'><tr bgcolor='#AABBCC' align=center><td width=20%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>ID</td><td><input type=hidden name='id' value='$row[0]'></input><font color='#0000FF'><b>$row[0]</b></font></td></tr>";

	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>$MRTGMsg[271]</td>
		<td><select name='mrtg_param_value' onChange='ChangeMRTGParam()'>
				<option value='0'>$MRTGMsg[272]
				<option $selected_1 value='-1'>SNMP
				<option $selected_2 value='-2'>OTHER
				</select>";

	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>Title</td><td><input type='text' name='title' value='$row[1]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>RRD FileName (Host Name)</td><td><input type='text' name='filename' value='$row[5]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td class=blue>MaxBytes</td><td><input type='text' name='maxbytes' value='$row[6]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0' id='-4' style='$st_4'><td class=red>Other target value</td><td><input type='text' name='sfilename' value='$row[45]'></input></td></tr>";

	// List IP hosts
	print "<tr align=center bgcolor='#F0F0F0' id='-2' style='$st_2'><td class=red>IP (Target)</td><td>";
	print "<select name='ip'>";
	for ($h=0; $h<$rows_ip; $h++) {
		$row_ip = ($SQL_Type == "mysql") ? mysql_fetch_row($result_ip) : pg_fetch_row($result_ip, $h);
		$ip = split("/", $row_ip[1]);
		$row_ip[1] = $ip[0];
		if ($row[2] == $row_ip[1]) print "<option selected value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
		else print "<option value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
	}
	print "</select></td></tr>";
	// End

	// SNMP Version
	if( $row[3] == "1" ) { $versnmp_select_1 = "selected"; $versnmp_select_2 = ""; }
	else { $versnmp_select_1 = ""; $versnmp_select_2 = "selected"; }
	print "<tr align=center bgcolor='#F0F0F0' id='-3' style='$st_3'><td class=red>SNMP Version (Target)</td><td>
		<select name='ver_snmp'>
		<option $versnmp_select_1 value='1'>1
		<option $versnmp_select_2 value='2'>2
		</select></td></tr>";
	// End

	print "<tr align=center bgcolor='#F0F0F0' id='-1' style='$st_1'><td class=red>Router Port or SNMP OID (Target)</td><td><input type='text' name='target' value='$row[4]'></input></td></tr>";


	// RouteUptime
	if( $row[7] == ""  ) { $routeruptime_select_1 = "selected"; $routeruptime_select_2 = ""; }
	else { $routeruptime_select_1 = ""; $routeruptime_select_2 = "selected"; }
	print "<tr align=center bgcolor='#F0F0F0'><td>RouterUptime</td><td>
		<select name='routeruptime'>
		<option $routeruptime_select_1 value=''>$MRTGMsg[167]
		<option $routeruptime_select_2 value='1'>$MRTGMsg[166]
		</select></td></tr>";
	// End

	print "<tr align=center bgcolor='#F0F0F0'><td>SetEnv</td><td><input type='text' name='setenv' value='$row[46]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>PageTop</td><td><input type='text' name='pagetop' value='$row[47]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Options</td><td><input type='text' name='options' value='$row[24]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>AbsMax</td><td><input type='text' name='absmax' value='$row[10]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>WithPeak</td><td><input type='text' name='withpeak' value='$row[12]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Colours</td><td><input type='text' name='colours' value='$row[26]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YLegend</td><td><input type='text' name='ylegend' value='$row[27]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>ShortLegend</td><td><input type='text' name='shortlegend' value='$row[28]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend1</td><td><input type='text' name='legend1' value='$row[29]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend2</td><td><input type='text' name='legend2' value='$row[30]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend3</td><td><input type='text' name='legend3' value='$row[31]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Legend4</td><td><input type='text' name='legend4' value='$row[32]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>LegendI</td><td><input type='text' name='legendi' value='$row[33]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>LegendO</td><td><input type='text' name='legendo' value='$row[34]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>kMG</td><td><input type='text' name='kmg' value='$row[25]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Unscaled</td><td><input type='text' name='unscaled' value='$row[11]'></input></td></tr>";

	if( $row[9] == ""  ) { $ipv4only_select_1 = "selected"; $ipv4only_select_2 = ""; $ipv4only_select_3 = ""; }
	else if( $row[9] == "Yes" ) { $ipv4only_select_1 = ""; $ipv4only_select_2 = "selected"; $ipv4only_select_3 = ""; }
	else { $ipv4only_select_1 = ""; $ipv4only_select_2 = ""; $ipv4only_select_3 = "selected"; }
	print "<tr align=center bgcolor='#F0F0F0'><td>IPv4Only</td><td>
	<select name='ipv4only'>
		<option $ipv4only_select_1 value=''>$MRTGMsg[167]
		<option $ipv4only_select_2 value='Yes'>Yes
		<option $ipv4only_select_3 value='No'>No
	</select></td></tr>";

	print "<tr align=center bgcolor='#F0F0F0'><td>RouterName</td><td><input type='text' name='routername' value='$row[8]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Suppress</td><td><input type='text' name='suppress' value='$row[13]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>XSize</td><td><input type='text' name='xsize' value='$row[14]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YSize</td><td><input type='text' name='ysize' value='$row[15]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>XZoom</td><td><input type='text' name='xzoom' value='$row[16]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YZoom</td><td><input type='text' name='yzoom' value='$row[17]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>XScale</td><td><input type='text' name='xscale' value='$row[18]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YScale</td><td><input type='text' name='yscale' value='$row[19]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YTics</td><td><input type='text' name='ytics' value='$row[20]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>YTicsFactor</td><td><input type='text' name='yticsfactor' value='$row[21]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Factor</td><td><input type='text' name='factor' value='$row[22]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Step</td><td><input type='text' name='step' value='$row[23]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>kilo</td><td><input type='text' name='kilo' value='$row[40]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Timezone</td><td><input type='text' name='timezone' value='$row[35]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>Weekformat</td><td><input type='text' name='weekformat' value='$row[36]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount</td><td><input type='text' name='rrdrowcount' value='$row[37]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount30m</td><td><input type='text' name='rrdrowcount30m' value='$row[41]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount2h</td><td><input type='text' name='rrdrowcount2h' value='$row[42]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDRowCount1d</td><td><input type='text' name='rrdrowcount1d' value='$row[43]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>RRDHWRRAs</td><td><input type='text' name='rrdhwrras' value='$row[44]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>TimeStrPos</td><td><input type='text' name='timestrpos' value='$row[38]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>TimeStrFmt</td><td><input type='text' name='timestrfmt' value='$row[39]'></input></td></tr>";

	print "<tr bgcolor='#F0F0F0' align=center><td colspan=2><input type=hidden name=p value='$p'><input type=hidden name=id value='$id'><input type=hidden name=save value='set'><input type='submit' name='submit' class='submit_button' value='$MRTGMsg[43]'></input></td></tr></form></table>";

} elseif ( isset($save) ) {

	if($mrtg_param_value == '0') {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[273]</b><br>";
		print "<form method=post ACTION='edit.php'><input type=hidden name=id value='$id'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else if ($title == '' || $filename == '' || $maxbytes == '') {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br>";
		print "<form method=post ACTION='edit.php'><input type=hidden name=id value='$id'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else if ( $mrtg_param_value == '-1' && ($ip == '' || $ver_snmp == '' || $target == '') ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br>";
		print "<form method=post ACTION='edit.php'><input type=hidden name=id value='$id'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	} else if ( $mrtg_param_value == '-2' && $sfilename == '' ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[274]</b><br>";
		print "<form method=post ACTION='edit.php'><input type=hidden name=id value='$id'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	/*} else if ( CheckFileName($filename) == 1 ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[120]</b><br>";
		print "<form method=post ACTION='edit.php'><input type=hidden name=id value='$id'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[275]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;*/
	} else {
		if($mrtg_param_value == "-2") {
			$ver_snmp = "0";
			$target = "";
		} else $sfilename = "";
		if($SQL_Type == "mysql") {
			$result_agent = @mysql_query("update agent set ip='".$ip."',title='".$title."',ver_snmp='".$ver_snmp."' where id='".$id."'");
			$result_mrtg = @mysql_query("update mrtg set target='".$target."',filename='".$filename."',maxbytes='".$maxbytes."',routeruptime='".$routeruptime."',routername='".$routername."',ipv4only='".$ipv4only."',absmax='".$absmax."',unscaled='".$unscaled."',withpeak='".$withpeak."',suppress='".$suppress."',xsize='".$xsize."',ysize='".$ysize."',xzoom='".$xzoom."',yzoom='".$yzoom."',xscale='".$xscale."',yscale='".$yscale."',ytics='".$ytics."',yticsfactor='".$yticsfactor."',factor='".$factor."',step='".$step."',options='".$options."',kmg='".$kmg."',colours='".$colours."',ylegend='".$ylegend."',shortlegend='".$shortlegend."',legend1='".$legend1."',legend2='".$legend2."',legend3='".$legend3."',legend4='".$legend4."',legendi='".$legendi."',legendo='".$legendo."',timezone='".$timezone."',weekformat='".$weekformat."',rrdrowcount='".$rrdrowcount."',timestrpos='".$timestrpos."',timestrfmt='".$timestrfmt."',kilo='".$kilo."',rrdrowcount30m='".$rrdrowcount30m."',rrdrowcount2h='".$rrdrowcount2h."',rrdrowcount1d='".$rrdrowcount1d."',rrdhwrras='".$rrdhwrras."',sfilename='".$sfilename."',setenv='".$setenv."',pagetop='".$pagetop."' where id='".$id."'");
		} else {
			$result_agent = @pg_query($db, "update agent set ip='".$ip."',title='".$title."',ver_snmp='".$ver_snmp."' where id='".$id."'");
			$result_mrtg = @pg_query($db, "update mrtg set target='".$target."',filename='".$filename."',maxbytes='".$maxbytes."',routeruptime='".$routeruptime."',routername='".$routername."',ipv4only='".$ipv4only."',absmax='".$absmax."',unscaled='".$unscaled."',withpeak='".$withpeak."',suppress='".$suppress."',xsize='".$xsize."',ysize='".$ysize."',xzoom='".$xzoom."',yzoom='".$yzoom."',xscale='".$xscale."',yscale='".$yscale."',ytics='".$ytics."',yticsfactor='".$yticsfactor."',factor='".$factor."',step='".$step."',options='".$options."',kmg='".$kmg."',colours='".$colours."',ylegend='".$ylegend."',shortlegend='".$shortlegend."',legend1='".$legend1."',legend2='".$legend2."',legend3='".$legend3."',legend4='".$legend4."',legendi='".$legendi."',legendo='".$legendo."',timezone='".$timezone."',weekformat='".$weekformat."',rrdrowcount='".$rrdrowcount."',timestrpos='".$timestrpos."',timestrfmt='".$timestrfmt."',kilo='".$kilo."',rrdrowcount30m='".$rrdrowcount30m."',rrdrowcount2h='".$rrdrowcount2h."',rrdrowcount1d='".$rrdrowcount1d."',rrdhwrras='".$rrdhwrras."',sfilename='".$sfilename."',setenv='".$setenv."',pagetop='".$pagetop."' where id='".$id."'");
		}
		if ( $p == '') $p = 1;
		if ($result_agent && $result_mrtg) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] $id $MRTGMsg[48]</b><br>";
			print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
			HTMLBottomPrint();
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] $id</font></b><br>";
			print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
			HTMLBottomPrint();
			exit;
		}
	}
}

HTMLBottomPrint();

?>