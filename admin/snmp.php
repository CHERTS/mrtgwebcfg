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

HTMLTopPrint();

print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[185]</b></td></tr></table>";

if ( !(isset($getsnmp)) ) {

	// Запрос на выбор IP хоста
	if($SQL_Type == "mysql") {
		$result_ip = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = mysql_num_rows($result_ip);
	} else {
		$result_ip = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip order by agent_ip.id asc");
		$rows_ip = pg_num_rows($result_ip);
	} 
	// Конец

	print "<br><form methode='post' name='snmp_param' action='$self'><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td colspan=2>$MRTGMsg[185]</td></tr>";
	// Показываем список IP хостов
	print "<tr align=center bgcolor='#F0F0F0'><td width='30%'>$MRTGMsg[187]</td>";
	print "<td><SELECT name='hid' onChange='ChangeIPHost()' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>
				<option value='-2'>$MRTGMsg[188]</option>
				<option value='-1'>$MRTGMsg[189]</option>";
	for ($h=0; $h<$rows_ip; $h++) {
		$row_ip = ($SQL_Type == "mysql") ? mysql_fetch_row($result_ip) : pg_fetch_row($result_ip, $h);
		$ip = split("/", $row_ip[1]);
		$row_ip[1] = $ip[0];
		if ($row[1] == $row_ip[1]) print "<option selected value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
		else print "<option value='$row_ip[0]'>$row_ip[1] - $row_ip[2]";
	}
	print "</SELECT></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0' id='-1' style='display: none'>
		<td width='30%'>$MRTGMsg[190]</td><td><input type='text' name='ip_address' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'></td>
		</tr>
		<tr align=center bgcolor='#F0F0F0' id='-2' style='display: none'>
		<td width='30%'>$MRTGMsg[191]</td>
		<td><SELECT name='ver_snmp' onChange='ChangeSNMP()' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>
				<option value='0'>$MRTGMsg[192]</option>
				<option value='-3'>$MRTGMsg[193]</option>
				<option value='-3'>$MRTGMsg[194]</option>
				<option value='-4'>$MRTGMsg[195]</option>
				<option value='-5'>$MRTGMsg[196]</option>
		</SELECT></td></tr>
		<tr align=center bgcolor='#F0F0F0' id='-4' style='display: none'>
		<td width='30%'>$MRTGMsg[197]</td><td><input type='text' name='snmp_username' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'></td>
		</tr>
		<tr align=center bgcolor='#F0F0F0' id='-3' style='display: none'>
		<td width='30%'>$MRTGMsg[198]</td><td><input type='password' name='snmp_passwd' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'></td>
		</tr>
		<tr align=center bgcolor='#F0F0F0' id='-5' style='display: none'>
		<td width='30%'>$MRTGMsg[199]</td>
		<td><SELECT name='auth_snmp_protocol' onChange='AuthSNMPProtocol()' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>
				<option value='0'>$MRTGMsg[200]</option>
				<option value='HMAC-MD5-96'>$MRTGMsg[201]</option>
				<option value='HMAC-SHA-96'>$MRTGMsg[202]</option>
		</SELECT></td></tr>
		";
	// Конец
	print "<tr align=center bgcolor='#F0F0F0'><td width='30%'>$MRTGMsg[203]</td><td>";
	print "<SELECT name='snmp_access' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
	print "<option selected value='ifindex'>$MRTGMsg[204]</option>";
	print "<option value='ro'>$MRTGMsg[205]</option>";
	//print "<option value='rw'>$MRTGMsg[206]</option>";
	print "</SELECT></td></tr>";

	print "<tr bgcolor='#F0F0F0' align=center><td colspan=2><input type=hidden name='getsnmp' value='yes'><input type='submit' value='$MRTGMsg[207]' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px'></input></td></tr></form></table>";

	print "<div align='center'><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form></div>";


} elseif( $snmp_access == "ro" ) {

	Check_SNMP();

	if( $ip_address == "" ) {
		// Запрос на выбор IP хоста
		if($SQL_Type == "mysql") {
			$result = mysql_query("select agent.id, agent_ip.ip, agent.title, agent_ip.community from agent_ip, agent where agent_ip.id=".$hid);
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select agent.id, agent_ip.ip, agent.title, agent_ip.community from agent_ip, agent where agent_ip.id=".$hid);
			$row = pg_fetch_row($result);
		}
		$ip = split("/", $row[1]);
		$host = $ip[0];
		$community = $row[3];
		// Конец
	} else {
		$host = $ip_address;
		$community = $snmp_passwd;
	}

	$snmp_con = snmpget($host,$community,"system.sysName.0");
	if( !($snmp_con) ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[216] $host</b><br>";
		print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	}

	$OIDName  = array('system.sysName.0','system.sysLocation.0','system.sysContact.0','system.sysDescr.0','system.sysUpTime.0');
	$OIDNameDesc  = array('System Name','System Location','System Contact','System Descrption','System UpTime');

	print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[1]: $host</b></td></tr></table>";

	print "<br><table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'>";
	for($i = 0; $i < count($OIDName)-1; $i++) {
		$sysInfo = snmpget($host,$community,$OIDName[$i]);
			$sysInfo = split("STRING: ", $sysInfo);
			print "<tr bgcolor='#F0F0F0' align=center><td width=250><b>$OIDNameDesc[$i]</b></td><td class=blue><b>$sysInfo[1]</b></td></tr>";
	}

	$sysInfo = snmpget($host,$community,$OIDName[count($OIDName)-1]);
	$sysInfo = ereg_replace("Timeticks: \([0-9]+\) ", "", $sysInfo);
	print "<tr bgcolor='#F0F0F0' align=center><td width=250><b>".$OIDNameDesc[count($OIDName)-1]."</b></td><td class=blue><b>$sysInfo</b></td></tr>";
	print "</table>";

	$ifIndex = snmpwalk($host,$community,"interfaces.ifTable.ifEntry.ifIndex"); 
	$ifDescr = snmpwalk($host,$community,"interfaces.ifTable.ifEntry.ifDescr"); 
	$ifAdminStatus = snmpwalk($host,$community,"interfaces.ifTable.ifEntry.ifAdminStatus"); 
	$ifOperStatus = snmpwalk($host,$community,"interfaces.ifTable.ifEntry.ifOperStatus"); 
	$ifLastChange = snmpwalk($host,$community,"interfaces.ifTable.ifEntry.ifLastChange"); 

	print "<br><table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'>
		<tr bgcolor='#AABBCC' align=center>
		<td width=150><b>ifIndex</b></td>
		<td width=250><b>ifDescr</b></td>
		<td width=150><b>ifAdminStatus</b></td>
		<td width=150><b>ifOperStatus</b></td>
		<td><b>ifLastChange</b></td>
		</tr>";

	for ($i=0; $i<count($ifIndex); $i++) { 
		$ifIndex[$i] = split("INTEGER: ", $ifIndex[$i]);
		$ifDescr[$i] = split("STRING: ", $ifDescr[$i]);
		$ifAdminStatus[$i] = split("INTEGER: ", $ifAdminStatus[$i]);
		$ifOperStatus[$i] = split("INTEGER: ", $ifOperStatus[$i]);
		$ifLastChange[$i] = split("Timeticks: ", $ifLastChange[$i]);
		print "<tr align=center bgcolor='#F0F0F0'>";
		print "<td>".$ifIndex[$i][1]."</td>"; 
		print "<td>".$ifDescr[$i][1]."</td>"; 
		print "<td>".$ifAdminStatus[$i][1]."</td>"; 
		print "<td>".$ifOperStatus[$i][1]."</td>"; 
		print "<td>".$ifLastChange[$i][1]."</td>"; 
		print "</tr>"; 
	}            
	print "</table><br>"; 

	print "<div align='center'><form methode='post' action='$self'><input type='submit' value='$MRTGMsg[185]' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 150px'></input></div>";

} elseif( $snmp_access == "rw" ) {

	Check_SNMP();

} elseif( $snmp_access == "ifindex" ) {

	Check_SNMP();

	if( $ip_address == "" ) {
		// Запрос на выбор IP хоста
		if($SQL_Type == "mysql") {
			$result = mysql_query("select agent.id, agent_ip.ip, agent_ip.title, agent_ip.community from agent_ip, agent where agent_ip.id=".$hid);
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select agent.id, agent_ip.ip, agent_ip.title, agent_ip.community from agent_ip, agent where agent_ip.id=".$hid);
			$row = pg_fetch_row($result);
		}
		$ip = split("/", $row[1]);
		$host = $ip[0];
		$host_desc = $row[2];
		$community = $row[3];
		// Конец
	} else {
		$host = $ip_address;
		$community = $snmp_passwd;
	}

	$snmp_con = snmpget($host,$community,"system.sysName.0");
	if( !($snmp_con) ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[216] $host</b><br>";
		print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></div>";
		exit;
	}

	print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[204]</b></td></tr></table>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id,agent.title,mrtg.target,mrtg.interface_name from agent, agent_ip, mrtg where mrtg.id=agent.id and agent.ip=agent_ip.id and agent.trash=0 and mrtg.interface_name != '' and agent_ip.id=".$hid." order by mrtg.interface_name asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent.id,agent.title,mrtg.target,mrtg.interface_name from agent, agent_ip, mrtg where mrtg.id=agent.id and agent.ip=agent_ip.id and agent.trash=0 and mrtg.interface_name != '' and agent_ip.id=".$hid." order by mrtg.interface_name asc");
		$rows = pg_num_rows($result);
	}

	$ifIndex = snmpwalk($host,$community,"interfaces.ifTable.ifEntry.ifIndex"); 
	$ifDescr = snmpwalk($host,$community,"interfaces.ifTable.ifEntry.ifDescr"); 

	for ($i=0; $i<count($ifIndex); $i++) { 
		$ifIndex[$i] = split("INTEGER: ", $ifIndex[$i]);
		$ifDescr[$i] = split("STRING: ", $ifDescr[$i]);
		$ifDescr_1 = split( "-", $ifDescr[$i][1]);
		$array_snmp_ifindex[$i] = $ifIndex[$i][1];
		$array_snmp_ifdescr[$i] = $ifDescr_1[0];
	}

	for ($j=0; $j<$rows; $j++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $j);
		$array_mrtg_hid[$j] = $row[0];
		$array_mrtg_title[$j] = $row[1];
		$array_mrtg_ifindex[$j] = $row[2];
		$array_mrtg_ifdescr[$j] = $row[3];
	}

	if( $ifindex_rewrite != "1" ) {
		print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$host $host_desc</b></td></tr></table>";
		print "<br><table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'>";
		print "<tr align=center bgcolor='#AABBCC'><td colspan=2 width=30%><b>$MRTGMsg[208]</b></td><td colspan=4><b>$MRTGMsg[209]</b></td></tr>";
		print "<tr align=center bgcolor='#AAAACC'><td><b>ifDescr</b></td><td><b>ifIndex</b></td><td><b>$MRTGMsg[67]</b></td><td><b>$MRTGMsg[2]</b></td><td><b>ifDescr</b></td><td><b>ifIndex</b></td></tr>";
	}

	$cnt=0;
	for($ii=0; $ii<count($array_snmp_ifdescr); $ii++) {
		for($jj=0; $jj<count($array_mrtg_ifdescr); $jj++) {
			if( $array_snmp_ifdescr[$ii] == $array_mrtg_ifdescr[$jj] ) {
				if( $array_snmp_ifindex[$ii] != $array_mrtg_ifindex[$jj] ) {
					$array_bad[$cnt][0] = $array_mrtg_hid[$jj];
					$array_bad[$cnt][1] = $array_mrtg_ifindex[$jj];
					$array_bad[$cnt][2] = $array_snmp_ifindex[$ii];
					$cnt++;
					if( $ifindex_rewrite != "1" ) {
						print "<tr bgcolor='#F0F0F0' align=center><td class=red>&nbsp;".$array_snmp_ifdescr[$ii]."</td><td align=center class=red>&nbsp;".$array_snmp_ifindex[$ii]."</td>";
						print "<td class=red>".$array_mrtg_hid[$jj]."</td><td>&nbsp;<a href='view.php?id=".$array_mrtg_hid[$jj]."' target='_userwww'>".$array_mrtg_title[$jj]."</a></td><td class=red>&nbsp;".$array_mrtg_ifdescr[$jj]."</td><td class=red>&nbsp;".$array_mrtg_ifindex[$jj]."</td></tr>";
					}
				} else {
					//print "<tr bgcolor='#F0F0F0'><td>&nbsp;".$array_snmp_ifdescr[$ii]."</td><td align=center>&nbsp;".$array_snmp_ifindex[$ii]."</td>";
					//print "<td align=center>".$array_mrtg_hid[$jj]."</td><td>&nbsp;<a href='view.php?id=".$array_mrtg_hid[$jj]."' target='_userwww'>".$array_mrtg_title[$jj]."</a></td><td>&nbsp;".$array_mrtg_ifdescr[$jj]."</td><td align=center>&nbsp;".$array_mrtg_ifindex[$jj]."</td></tr>";
				}
			}
		}
	}
	if( !(count($array_bad)) && $ifindex_rewrite != "1" ) print "<tr bgcolor='#F0F0F0' align=center><td class=red colspan=6>$MRTGMsg[210]</td></tr>";
	if($ifindex_rewrite != "1" ) print "</table>";

	if( count($array_bad) && $ifindex_rewrite != "1" ) {
		print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[211] ".count($array_bad)." $MRTGMsg[212]</b></td></tr></table>";
		print "<div align='center'><form methode='post' name='snmp_param' action='$self'>
			<input type=hidden name='ifindex_rewrite' value='1'>
			<input type=hidden name='getsnmp' value='yes'>
			<input type=hidden name='snmp_access' value='ifindex'>
			<input type=hidden name='hid' value='$hid'>
			<input type='submit' value='$MRTGMsg[213]' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 250px'></input></div>";
	} elseif( $ifindex_rewrite != "1" ) {
		print "<div align='center'><form methode='post' action='$self'><input type='submit' value='$MRTGMsg[185]' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 150px'></input></div>";
	}

	if( $ifindex_rewrite == "1" ) {
		$array_bad_result=0;
		for($i=0; $i<count($array_bad);$i++) {
			$result_mrtg = ($SQL_Type == "mysql") ? @mysql_query("update mrtg set target='".$array_bad[$i][2]."' where id='".$array_bad[$i][0]."'") : @pg_query($db, "update mrtg set target='".$array_bad[$i][2]."' where id='".$array_bad[$i][0]."'");
			if ( !($result_mrtg) ) $array_bad_result++;
		}
		if ( $array_bad_result == 0 ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[214]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[215]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
		}
	}

}

HTMLBottomPrint();

?>
