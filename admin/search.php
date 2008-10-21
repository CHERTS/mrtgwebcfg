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

HTMLTopPrint($MRTGMsg[80]);

$self = $_SERVER['PHP_SELF'];

print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[80]</b></td></tr></table>";

print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'><tr bgcolor='#AABBCC' align=center><td colspan=2><b>$MRTGMsg[81]</b></td></tr>";
print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[94]."</td><td>";
print "<SELECT name='search_id' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
print "<option selected value='title'>$MRTGMsg[95]";
print "<option value='ip'>$MRTGMsg[96]";
print "</SELECT></td></tr>";
print "<tr align=center bgcolor='#F0F0F0'><td width='30%'>$MRTGMsg[82]</td><td><input type='text' name='search_string' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
print "<tr bgcolor='#F0F0F0' align=center><td colspan=2><input type=hidden name='search' value='set'><input type='submit' name='submit' value='$MRTGMsg[84]' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px'></input></td></tr></form></table>";

if ($search_string == '') {
	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post ACTION='index.php'><input type=submit value='$MRTGMsg[24]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px'></form></td></tr></table>";
}

if ( isset($search) && $search_string != '') {

	print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[83]</b></td></tr></table><br>";
	print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=4%><b>$MRTGMsg[67]</b></td><td><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[2]</b></td><td width=10%><b>$MRTGMsg[3]</b></td><td width=8%><b>$MRTGMsg[4]</b></td><td width=25%><b>$MRTGMsg[9]</b></td></tr>";

	if($SQL_Type == "mysql") {
		if ( $search_id == 'ip' ) $result = mysql_query("SELECT agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community FROM agent,agent_ip WHERE agent.ip=agent_ip.id and agent.trash=0 and agent_ip.ip LIKE '%".$search_string."%' order by agent.id asc");
		else $result = mysql_query("SELECT agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community FROM agent,agent_ip WHERE agent.ip=agent_ip.id and agent.trash=0 and agent.title LIKE '%".$search_string."%' order by agent.id asc");
		$rows = mysql_num_rows($result);
	} else {
		if ( $search_id == 'ip' ) $result = pg_query($db, "SELECT agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community FROM agent,agent_ip WHERE agent.ip=agent_ip.id and agent.trash=0 and agent_ip.ip <<= '".pg_escape_string($search_string)."' order by agent.id asc");
		else $result = pg_query($db, "SELECT agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community FROM agent,agent_ip WHERE agent.ip=agent_ip.id and agent.trash=0 and agent.title ILIKE '%".pg_escape_string($search_string)."%' order by agent.id asc");
		$rows = pg_num_rows($result);
	}

	for ($i=0; $i < $rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		print "<tr align=center bgcolor='#F0F0F0'>";
		for ($j=0; $j < count($row); $j++) {
			if ( $j == 1 ) { $ip = split("/", $row[1]); $row[$j] = $ip[0]; }
			if ( $j == 4 ) {
				if( $Show_Community == "0" ) print "<td>*****</td>";
				else print "<td>".$row[$j]."</td>";
			}
			else print "<td>".$row[$j]."</td>";
		}
		print "<td><a href='view.php?id=$row[0]'>$MRTGMsg[10]</a> | <a href='edit.php?id=$row[0]'>$MRTGMsg[11]</a> | <a href='copy.php?id=$row[0]'>$MRTGMsg[114]</a> | <a href='delete.php?id=$row[0]'>$MRTGMsg[12]</a> | <a href='templates.php?hid=$row[0]&mode=add'>$MRTGMsg[65]</a></td>";
		print "</tr>";
	}

	if ($rows == 0) {
		print "<tr align=center bgcolor='#F0F0F0'><td colspan=6 class=red>$MRTGMsg[5]</td></tr>";
	}

	print "</table>";
	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post ACTION='index.php'><input type=submit value='$MRTGMsg[24]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px'></form></td></tr></table>";

}

HTMLBottomPrint();

?>
