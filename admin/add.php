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

HTMLTopPrint($MRTGMsg[32]);

$self = $_SERVER['PHP_SELF'];

print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[32]</b></td></tr></table><br>";

if (isset($add) && !(isset($save)) ) {

	$ip = $title = $ver_snmp = $community = $filename = $target = $interface_ip = $interface_name = $maxbytes = $iftype = $title_ip = $absmax = $withpeak = $options = $colours = $ylegend = $shortlegend = $legend1 = $legend2 = $legend3 = $legend4 = $legendi = $legendo = $routeruptime = $kmg = $unscaled = "";

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

	// Запрос на выбор IP хоста
	if($SQL_Type == "mysql") {
		$result_ip = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = mysql_num_rows($result_ip);
	} else {
		$result_ip = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = pg_num_rows($result_ip);
	}
	// Конец

	// Запрос на SID
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
	// Конец

	print "<table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'><tr bgcolor='#AABBCC' align=center><td width=20%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$Full_Settings[0]."</td><td><input type=hidden name='id' value='$id'></input><font color='#0000FF'><b>$id</b></font></td></tr>";

	for ($z=1; $z<count($Full_Settings); $z++) {
		if ( ereg($Full_Settings[$z], '^title$|^filename$|^target$|^maxbytes$') ) {
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$Full_Settings[$z]."</td><td><input type='text' name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
		} elseif( $Full_Settings[$z] == 'ver_snmp' ) {
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$Full_Settings[$z]."</td><td>";
			print "<SELECT name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
			print "<option selected value='1'>1";
			print "<option value='2'>2";
			print "</SELECT></td></tr>";
		} elseif( $Full_Settings[$z] == 'ip' ) {
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
			print "<option selected value=''>$MRTGMsg[167]";
			print "<option value='1'>$MRTGMsg[166]";
			print "</SELECT></td></tr>";
		} else print "<tr align=center bgcolor='#F0F0F0'><td>".$Full_Settings[$z]."</td><td><input type='text' name='$Full_Settings[$z]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
	}

	print "<tr bgcolor='#F0F0F0' align=center><td colspan=2><input type=hidden name=save value='set'><input type=hidden name=sid value='$sid'><input type='submit' name='submit' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px' value='$MRTGMsg[43]'></input></td></tr></form></table>";
}

if ( isset($save) ) {
	if ($ip == '' || $title == '' || $ver_snmp == '' || $filename == '' || $target == '' || $maxbytes == '') {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br>";
		print "<form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	}
	if ( CheckFileName($filename) == 1 ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[120]</b><br>";
		print "<form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	} else {
		$filename = strtolower($filename);
		if($SQL_Type == "mysql") {
			$result_agent = @mysql_query("insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$id.",'".$ip."','".$title."','".$ver_snmp."',0,0)");
			$result_mrtg = @mysql_query("insert into mrtg (id,filename,target,interface_ip,interface_name,maxbytes,iftype,title_ip,absmax,withpeak,options,colours,ylegend,shortlegend,legend1,legend2,legend3,legend4,legendi,legendo,routeruptime,kmg,unscaled) values(".$id.",'".$filename."','".$target."','".$interface_ip."','".$interface_name."','".$maxbytes."','".$iftype."','".$title_ip."','".$absmax."','".$withpeak."','".$options."','".$colours."','".$ylegend."','".$shortlegend."','".$legend1."','".$legend2."','".$legend3."','".$legend4."','".$legendi."','".$legendo."','".$routeruptime."','".$kmg."','".$unscaled."')");
		} else {
			$result_agent = @pg_query($db, "insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$id.",'".$ip."','".$title."','".$ver_snmp."',0,0)");
			$result_mrtg = @pg_query($db, "insert into mrtg (id,filename,target,interface_ip,interface_name,maxbytes,iftype,title_ip,absmax,withpeak,options,colours,ylegend,shortlegend,legend1,legend2,legend3,legend4,legendi,legendo,routeruptime,kmg,unscaled) values(".$id.",'".$filename."','".$target."','".$interface_ip."','".$interface_name."','".$maxbytes."','".$iftype."','".$title_ip."','".$absmax."','".$withpeak."','".$options."','".$colours."','".$ylegend."','".$shortlegend."','".$legend1."','".$legend2."','".$legend3."','".$legend4."','".$legendi."','".$legendo."','".$routeruptime."','".$kmg."','".$unscaled."')");
		}
		//$result_templates = pg_query($db, "insert into templates (id, agent_id, group_id, row_set, column_set, hide_set) values(".$sid.",".$id.",0,0,0,0)");
		//if ($result_agent && $result_mrtg && $result_templates) {
		if ($result_agent && $result_mrtg) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] $id $MRTGMsg[51]<br>"; //<br>$MRTGMsg[99] <font color='#0000FF'>$sid</font> $MRTGMsg[100]</b><br>";
			print "<form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[50] $id</font></b><br>";
			print "<form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
		}
	}
}

HTMLBottomPrint();

?>
