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

HTMLTopPrint($MRTGMsg[115]);

$self = $_SERVER['PHP_SELF'];

if ( isset($id) && $id != '' && !(isset($clone)) ) {

	$ip = $title = $ver_snmp = $community = $filename = $target = $interface_ip = $interface_name = $maxbytes = $iftype = $title_ip = $absmax = $withpeak = $options = $colours = $ylegend = $shortlegend = $legend1 = $legend2 = $legend3 = $legend4 = $legendi = $legendo = $routeruptime = $kmg = $unscaled = "";

	// Запрос на ID
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
			$new_id = $i;
			$ids = 1;
		}
	}
	if ($ids == 0) $new_id = $rows;
	// Конец запроса

	// Запрос на выбор IP хоста
	if($SQL_Type == "mysql") {
		$result_ip = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = mysql_num_rows($result_ip);
	} else {
		$result_ip = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = pg_num_rows($result_ip);
	}
	// Конец

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[115] <font color='#0000FF'>$id -> $new_id</font></b></td></tr></table><br>";
	print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,mrtg.filename,mrtg.target,mrtg.interface_ip,mrtg.interface_name,mrtg.maxbytes,mrtg.iftype,mrtg.title_ip,mrtg.absmax,mrtg.withpeak,mrtg.options,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.routeruptime,mrtg.kmg,mrtg.unscaled from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0 and agent.id=$id order by id asc");
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,mrtg.filename,mrtg.target,mrtg.interface_ip,mrtg.interface_name,mrtg.maxbytes,mrtg.iftype,mrtg.title_ip,mrtg.absmax,mrtg.withpeak,mrtg.options,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.routeruptime,mrtg.kmg,mrtg.unscaled from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0 and agent.id=$id order by id asc");
		$row = pg_fetch_row($result);
	}
	$ip = split("/", $row[1]);
	$row[1] = $ip[0];

	$id = $real_ip = $title = $ver_snmp = $community = $filename = $target = $interface_ip = $interface_name = $maxbytes = $system = $iftype = $ifname = $ip = $absmax = $withpeak = $options = $colours = $ylegend = $shortlegend = $legend1 = $legend2 = $legend3 = $legend4 = $legendi = $legendo = $routeruptime = $kmg = $unscaled = "";

	print "<tr bgcolor='#F0F0F0' align=center><td width=50% colspan=2>";
	print "<table width=100% height=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'>";
	print "<tr bgcolor='#AABBCC' align=center><td colspan=2 width=50%><b>$MRTGMsg[116]</b></td><td colspan=2 width=50%><b>$MRTGMsg[117]</b></td></tr>";
	for ($j=0; $j < count($row); $j++ ) {
		print "<tr bgcolor='#F0F0F0'><td width='70' align=left>";
		if ( ereg($Full_Settings[$j], '^ip$|^title$|^ver_snmp$|^filename$|^target$|^maxbytes$') ) print "&nbsp;<font color='#FF0000'>$Full_Settings[$j]</font>";
		else print "&nbsp;<font color='#0000FF'>$Full_Settings[$j]</font>";
		print "</td><td align=center width='35%'>";
		if( $j == 0 ) print "<font color='#FF0000'><b>$row[$j]</b></font></td>";
		//if( $j == 4 && $Show_Community == "0" ) print "<font color='#FF0000'><b>*****</b></font></td>";
		if( $Full_Settings[$j] == 'routeruptime' && $row[$j] != "" && $Show_Community == "1" ) print "*****@$row[1]</td>";
		//elseif( $Full_Settings[$j] == 'routeruptime' && $row[$j] != "" && $Show_Community == "0" ) print "*****@$row[1]</td>";
		else print "$row[$j]</td>";
		print "<td width='70' align=left>";
		if ( ereg($Full_Settings[$j], '^ip$|^title$|^ver_snmp$|^filename$|^target$|^maxbytes$') ) print "&nbsp;<font color='#FF0000'>$Full_Settings[$j]</font>";
		else print "&nbsp;<font color='#0000FF'>$Full_Settings[$j]</font>";
		print "</td><td align=center width='35%'>";
		if( $j == 0 ) print "<font color='#FF0000'><b>$new_id</b></font></td>";
		elseif($Full_Settings[$j] == 'ip') {
			// Показываем список IP хостов
			print "<SELECT name='$Full_Settings[$j]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width:350'>";
			for ($h=0; $h<$rows_ip; $h++) {
				$row_ip = ($SQL_Type == "mysql") ? mysql_fetch_row($result_ip) : pg_fetch_row($result_ip, $h);
				$ip = split("/", $row_ip[1]);
				$row_ip[1] = $ip[0];
				if ($row[1] == $row_ip[1]) print "<option selected value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
				else print "<option value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
			}
			print "</SELECT>";
			// Конец
		} elseif( $Full_Settings[$j] == 'routeruptime' ) {
			print "<SELECT name='$Full_Settings[$j]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 350'>";
			if( $row[$j] == ""  ) { $routeruptime_select_1 = "selected"; $routeruptime_select_2 = ""; }
			else { $routeruptime_select_1 = ""; $routeruptime_select_2 = "selected"; }
			print "<option $routeruptime_select_1 value=''>$MRTGMsg[167]";
			print "<option $routeruptime_select_2 value='1'>$MRTGMsg[166]";
			print "</SELECT>";
		} elseif($Full_Settings[$j] == 'community' && $Show_Community == "0") print "<input type='password' name='$Full_Settings[$j]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:11px;width:350' value='$row[$j]'></input></td>";
		else print "<input type='text' name='$Full_Settings[$j]' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:11px;width:350' value='$row[$j]'></input></td>";
		print "</tr>";
	}
	print "<tr bgcolor='#F0F0F0' align=center><td colspan=4><input type=hidden name=id value='$row[0]'><input type=hidden name=p value='$p'><input type=hidden name=new_id value='$new_id'><input type=hidden name=clone value='set'><input type='submit' name='submit' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:13px;width: 120px' value='$MRTGMsg[118]'></input></td></tr>";
	print "</table></td></tr></form></table>";

} elseif ( isset($id) && $id != '' && isset($clone) && $clone != '' ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[115] <font color='#0000FF'>$id -> $new_id</font></b></td></tr></table>";

	// Запрос на ID
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
			$new_id_confirm = $i;
			$ids = 1;
		}
	}
	if ($ids == 0) $new_id_confirm = $rows;
	// Конец запроса

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
	// Конец запроса

	if ( $new_id == $new_id_confirm ) {

		if ( $p == '') $p = 1;

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
				$result_agent = @mysql_query("insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$new_id.",'".$ip."','".$title."','".$ver_snmp."',0,0)");
				$result_mrtg = @mysql_query("insert into mrtg (id,filename,target,interface_ip,interface_name,maxbytes,iftype,title_ip,absmax,withpeak,options,colours,ylegend,shortlegend,legend1,legend2,legend3,legend4,legendi,legendo,routeruptime,kmg,unscaled) values(".$new_id.",'".$filename."','".$target."','".$interface_ip."','".$interface_name."','".$maxbytes."','".$iftype."','".$title_ip."','".$absmax."','".$withpeak."','".$options."','".$colours."','".$ylegend."','".$shortlegend."','".$legend1."','".$legend2."','".$legend3."','".$legend4."','".$legendi."','".$legendo."','".$routeruptime."','".$kmg."','".$unscaled."')");
			}else{
				$result_agent = @pg_query($db, "insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$new_id.",'".$ip."','".$title."','".$ver_snmp."',0,0)");
				$result_mrtg = @pg_query($db, "insert into mrtg (id,filename,target,interface_ip,interface_name,maxbytes,iftype,title_ip,absmax,withpeak,options,colours,ylegend,shortlegend,legend1,legend2,legend3,legend4,legendi,legendo,routeruptime,kmg,unscaled) values(".$new_id.",'".$filename."','".$target."','".$interface_ip."','".$interface_name."','".$maxbytes."','".$iftype."','".$title_ip."','".$absmax."','".$withpeak."','".$options."','".$colours."','".$ylegend."','".$shortlegend."','".$legend1."','".$legend2."','".$legend3."','".$legend4."','".$legendi."','".$legendo."','".$routeruptime."','".$kmg."','".$unscaled."')");
			}
			//$result_templates = pg_query($db, "insert into templates (id, agent_id, group_id, row_set, column_set, hide_set) values(".$sid.",".$new_id.",0,0,0,0)");
			//if ($result_agent && $result_mrtg && $result_templates) {
			if ($result_agent && $result_mrtg) {
				print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] $new_id $MRTGMsg[51]<br>"; //<br>$MRTGMsg[99] <font color='#0000FF'>$sid</font> $MRTGMsg[100]</b><br>";
				print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
				print "</td></tr></table></div>";
				exit;
			} else {
				print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[50] $new_id</font></b><br>";
				print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
				print "</td></tr></table></div>";
				exit;
			}
		}
	} else {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[119]</font></b><br>";
		print "<form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	}

} else {
	print "Error";
}

HTMLBottomPrint();

?>