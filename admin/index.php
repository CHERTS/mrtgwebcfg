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

HTMLTopPrint();

if(!DBVersionCheck()) {
	print "<br><div align=center><table cellpadding=4 cellspacing=2 width=50% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>".DBVersionCheck(1)."</b><br><br>";
	print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
	print "<td><form method=post ACTION='index.php'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
	print "</td></tr></table></td></tr></table></div>";
	HTMLBottomPrint();
	exit;
}

if (!isset($p)) $p = 1;

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr></table>";
print "<br><table width='100%' cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40><b>$MRTGMsg[67]</b></td><td width=110><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[2]</b></td><td width=100><b>$MRTGMsg[3]</b></td><td width=100><b>$MRTGMsg[4]</b></td><td width=150><b>$MRTGMsg[9]</b></td></tr>";

if($SQL_Type == "mysql") {
	$result = mysql_query("select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community from agent, agent_ip where agent.ip=agent_ip.id and agent.trash=0 order by id asc");
	$rows = mysql_num_rows($result);
} else {
	$result = pg_query($db, "select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community from agent, agent_ip where agent.ip=agent_ip.id and agent.trash=0 order by id asc");
	$rows = pg_num_rows($result);
}

$pn=ceil($rows/20);
$ps=($p-1)*20+1;
$pe=$p*20;
if ($pe > $rows) $pe = $rows;

for ($i=0; $i<$rows; $i++) {
	if($SQL_Type == "mysql") $row = mysql_fetch_row($result);
	else $row = pg_fetch_row($result, $i);
	if (($i >= $ps-1) && ($i < $pe)) {
		print "<tr align=center bgcolor='#F0F0F0'><td>".$row[0]."</td>";
		$ip = split("/", $row[1]);
		print "<td>".$ip[0]."</td>";
		print "<td align=left>&nbsp;<a href='view.php?id=$row[0]&amp;p=$p'>".$row[2]."</a></td>";
		if($row[3] == "0") print "<td>No SNMP</td>";
		else print "<td>SNMP v".$row[3]."</td>";
		if($Show_Community == "0") print "<td>*****</td>";
		else print "<td>".$row[4]."</td>";
		print "<td><a href='edit.php?id=$row[0]&amp;p=$p'>$MRTGMsg[11]</a> | <a href='copy.php?id=$row[0]&amp;p=$p'>$MRTGMsg[114]</a> | <a href='delete.php?id=$row[0]&amp;p=$p'>$MRTGMsg[12]</a></td>";
	}
}

if ($rows == 0) {
	$ps = 0;
	print "<tr bgcolor='#F0F0F0'><td colspan=6 align=center class=red>$MRTGMsg[5]</td></tr>";
	print "</table>";
} else {
	print "</table>";
	print "<table cellpadding=2 cellspacing=0 width='100%'><tr><td align=left><b>$MRTGMsg[6]:</b> [$ps - $pe] $MRTGMsg[7] $rows</td><td align=right><b>$MRTGMsg[8]: </b>";
	$ip=$p-1;
	$in=$p+1;
	if ($p > 1) print " <a href='index.php?p=$ip'><<</a>";
	for ($i=1; $i<=$pn; $i++)
	{
		if ($i == $p) print "<b> [$i]</b>";
		else print " <a href='index.php?p=$i'>[$i]</a>";
	}
	if ($p < $pn) print " <a href='index.php?p=$in'>>></a>";
	print "</td></tr></table>";
}

print "<div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
print "<td><form method=post ACTION='add.php'><input type=hidden name=p value='$p'><input type=hidden name=add value='set'><input type=submit class='submit_main_button' style='width:130px' value='$MRTGMsg[34]'></form></td>";
print "<td><form method=post ACTION='global.php'><input type=hidden name=p value='$p'><input type=hidden name=config value='set'><input type=submit class='submit_main_button' style='width:320px' value='$MRTGMsg[35]'></form></td>";
print "<td><form method=post ACTION='make.php'><input type=hidden name=p value='$p'><input type=hidden name=make value='set'><input type=submit class='submit_main_button' style='width:250px' value='$MRTGMsg[19]'></form></td></tr></table>";
print "<table cellpadding=0 cellspacing=5><tr align=center>";
print "<td><form method=post ACTION='ip.php'><input type=hidden name=ip_host value='set'><input type=submit class='submit_main_button' style='width:180px' value='$MRTGMsg[137]'></form></td>";
print "<td><form method=post ACTION='templates.php'><input type=hidden name=templates value='set'><input type=submit class='submit_main_button' style='width:190px' value='$MRTGMsg[53]'></form></td>";
print "<td><form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit class='submit_main_button' style='width:190px' value='$MRTGMsg[54]'></form></td>";
print "<td><form method=post ACTION='search.php'><input type=submit class='submit_main_button' style='width:140px' value='$MRTGMsg[80]'></form></td>";
print "</tr></table>";
print "<table cellpadding=0 cellspacing=5><tr align=center>";
print "<td><form method=post ACTION='snmp.php'><input type=submit class='submit_main_button' style='width:120px' value='$MRTGMsg[186]'></form></td>";
print "<td><form method=post ACTION='setup.php'><input type=submit class='submit_main_button' style='width:180px' value='$MRTGMsg[224]'></form></td>";
print "<td><form method=post ACTION='./../index.php'><input type=submit class='submit_main_button' style='width:170px' value='$MRTGMsg[97]'></form></td>";
print "</tr></table>";
if($Deny_Check_Update != "1") print VersionCheck();
print "</div>";

HTMLBottomPrint();

?>
