<?

define('IN_ADMIN', true);

require "./../config.php";
require "./../function.php";

$MRTGLang = ($MRTGAutoLanguage == '1') ? Get_Language() : $MRTGLanguage;
require "./../lang/$MRTGLang.php";

if (Check_Access() != "Allow") MRTGErrors(6);

if($SQL_Type == "mysql") {
	$db = mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or MRTGErrors(3);
	$sdb = mysql_select_db($SQL_Base, $db) or MRTGErrors(3);
} else $db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or MRTGErrors(3);

HTMLTopPrint($MRTGMsg[13]);

$self = $_SERVER['PHP_SELF'];

print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[13] $MRTGMsg[46] <font color='#0000FF'>$id</font></b></td></tr></table>";

if (isset($id) && !(isset($save)) ) {

	$ip = $title = $ver_snmp = $community = $filename = $target = $interface_ip = $interface_name = $maxbytes = $iftype = $title_ip = $absmax = $withpeak = $options = $colours = $ylegend = $shortlegend = $legend1 = $legend2 = $legend3 = $legend4 = $legendi = $legendo = $routeruptime = $kmg = $unscaled = "";

	print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'><tr bgcolor='#AABBCC' align=center><td width=20%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";

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
		$result = mysql_query("select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,mrtg.filename,mrtg.target,mrtg.interface_ip,mrtg.interface_name,mrtg.maxbytes,mrtg.iftype,mrtg.title_ip,mrtg.absmax,mrtg.withpeak,mrtg.options,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.routeruptime,mrtg.kmg,mrtg.unscaled from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.id=".$id);
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,mrtg.filename,mrtg.target,mrtg.interface_ip,mrtg.interface_name,mrtg.maxbytes,mrtg.iftype,mrtg.title_ip,mrtg.absmax,mrtg.withpeak,mrtg.options,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.routeruptime,mrtg.kmg,mrtg.unscaled from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.id=".$id);
		$row = pg_fetch_row($result);
	}

	if (count($row) < 2) {
		print "<tr bgcolor='#F0F0F0'><td colspan=2 align=center class=red>$MRTGMsg[5]</td></tr></table>";
		exit;
	}
	$ip = split("/", $row[1]);
	$row[1] = $ip[0];

	for ($z=1; $z<count($row); $z++ ) {
		if ( ereg($Full_Settings[$z], '^title$|^filename$|^target$|^maxbytes$') ) {
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$Full_Settings[$z]."</td><td><input type='text' name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value='$row[$z]'></input></td></tr>";
		} elseif ( $Full_Settings[$z] == 'ver_snmp' ) {
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$Full_Settings[$z]."</td><td>";
			print "<SELECT name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
			if( $row[$z] == "1" ) { $versnmp_select_1 = "selected"; $versnmp_select_2 = ""; }
			else { $versnmp_select_1 = ""; $versnmp_select_2 = "selected"; }
			print "<option $versnmp_select_1 value='1'>1";
			print "<option $versnmp_select_2 value='2'>2";
			print "</SELECT></td></tr>";
		} elseif ($Full_Settings[$z] == 'ip') {
			// Показываем список IP хостов
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$Full_Settings[$z]."</td><td>";
			print "<SELECT name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
			for ($h=0; $h<$rows_ip; $h++) {
				$row_ip = ($SQL_Type == "mysql") ? mysql_fetch_row($result_ip) : pg_fetch_row($result_ip, $h);
				$ip = split("/", $row_ip[1]);
				$row_ip[1] = $ip[0];
				if ($row[1] == $row_ip[1]) print "<option selected value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
				else print "<option value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
			}
			print "</SELECT></td></tr>";
			// Конец
		} elseif( $Full_Settings[$z] == 'routeruptime' ) {
			print "<tr align=center bgcolor='#F0F0F0'><td>".$Full_Settings[$z]."</td><td>";
			print "<SELECT name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
			if( $row[$z] == ""  ) { $routeruptime_select_1 = "selected"; $routeruptime_select_2 = ""; }
			else { $routeruptime_select_1 = ""; $routeruptime_select_2 = "selected"; }
			print "<option $routeruptime_select_1 value=''>$MRTGMsg[167]";
			print "<option $routeruptime_select_2 value='1'>$MRTGMsg[166]";
			print "</SELECT></td></tr>";
		} else print "<tr align=center bgcolor='#F0F0F0'><td>".$Full_Settings[$z]."</td><td><input type='text' name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value='$row[$z]'></input></td></tr>";
	}

	print "<tr bgcolor='#F0F0F0' align=center><td colspan=2><input type=hidden name=p value='$p'><input type=hidden name=id value='$id'><input type=hidden name=save value='set'><input type='submit' name='submit' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px' value='$MRTGMsg[43]'></input></td></tr></form></table>";

} elseif ( isset($save) ) {

	if ($ip == '' || $title == '' || $ver_snmp == '' || $filename == '' || $target == '' || $maxbytes == '') {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br>";
		print "<form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	}

	if($SQL_Type == "mysql") {
		$result_agent = @mysql_query("update agent set ip='".$ip."',title='".$title."',ver_snmp='".$ver_snmp."' where id='".$id."'");
		$result_mrtg = @mysql_query("update mrtg set filename='".$filename."',target='".$target."',interface_ip='".$interface_ip."',interface_name='".$interface_name."',maxbytes='".$maxbytes."',iftype='".$iftype."',title_ip='".$title_ip."',absmax='".$absmax."',withpeak='".$withpeak."',options='".$options."',colours='".$colours."',ylegend='".$ylegend."',shortlegend='".$shortlegend."',legend1='".$legend1."',legend2='".$legend2."',legend3='".$legend3."',legend4='".$legend4."',legendi='".$legendi."',legendo='".$legendo."',routeruptime='".$routeruptime."',kmg='".$kmg."',unscaled='".$unscaled."' where id='".$id."'");
	} else {
		$result_agent = @pg_query($db, "update agent set ip='".$ip."',title='".$title."',ver_snmp='".$ver_snmp."' where id='".$id."'");
		$result_mrtg = @pg_query($db, "update mrtg set filename='".$filename."',target='".$target."',interface_ip='".$interface_ip."',interface_name='".$interface_name."',maxbytes='".$maxbytes."',iftype='".$iftype."',title_ip='".$title_ip."',absmax='".$absmax."',withpeak='".$withpeak."',options='".$options."',colours='".$colours."',ylegend='".$ylegend."',shortlegend='".$shortlegend."',legend1='".$legend1."',legend2='".$legend2."',legend3='".$legend3."',legend4='".$legend4."',legendi='".$legendi."',legendo='".$legendo."',routeruptime='".$routeruptime."',kmg='".$kmg."',unscaled='".$unscaled."' where id='".$id."'");
	}
	if ( $p == '') $p = 1;
	if ($result_agent && $result_mrtg) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] $id $MRTGMsg[48]</b><br>";
		print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	} else {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] $id</font></b><br>";
		print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	}
}

HTMLBottomPrint();

?>