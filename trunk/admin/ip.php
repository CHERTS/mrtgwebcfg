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

HTMLTopPrint($MRTGMsg[137]);

$self = $_SERVER['PHP_SELF'];

if ( isset($ip_host) && !(isset($mode)) ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[137]</b></td></tr></table><br>";

	if (!isset($p)) $p = 1;

	print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=50><b>#</b></td><td width=150><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[138]</b></td><td width=110><b>$MRTGMsg[4]</b></td><td width=130><b>$MRTGMsg[9]</b></td></tr>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title,agent_ip.community from agent_ip order by agent_ip.id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title,agent_ip.community from agent_ip order by agent_ip.id asc");
		$rows = pg_num_rows($result);
	}

	$pn=ceil($rows/20);
	$ps=($p-1)*20+1;
	$pe=$p*20;
	if ($pe > $rows) $pe = $rows;

	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if (($i >= $ps-1) && ($i < $pe)) {
			print "<tr align=center bgcolor='#F0F0F0'><td>".$row[0]."</td>";
			$ip = split("/", $row[1]);
			$row[1] = $ip[0];
			print "<td>".$row[1]."</td>";
			print "<td align=left>&nbsp;<a href='ip.php?id=$row[0]&mode=view'>".$row[2]."</a></td>";
			if($Show_Community == "0") print "<td>*****</td>";
			else print "<td>".$row[3]."</td>";
			print "<td><a href='ip.php?id=$row[0]&mode=edit&p=$p'>$MRTGMsg[11]</a> | <a href='ip.php?id=$row[0]&mode=delete'>$MRTGMsg[12]</a></td></tr>";
		}
	}


	if ($rows == 0) {
		$ps = 0;
		print "<tr bgcolor='#F0F0F0'><td colspan=4 align=center class=red>$MRTGMsg[5]</td></tr>";
		print "</table>";
	} else {
		print "</table>";
		print "<table cellpadding=2 cellspacing=0 width=100%><tr><td align=left><b>$MRTGMsg[6]:</b> [$ps - $pe] $MRTGMsg[7] $rows</td><td align=right><b>$MRTGMsg[8]: </b>";
		$ip=$p-1;
		$in=$p+1;
		if ($p > 1) print " <a href='ip.php?ip_host=set&p=$ip'><<</a>";
		for ($i=1; $i<=$pn; $i++) {
			if ($i == $p) print "<b> [$i]</b>";
			else print " <a href='ip.php?ip_host=set&p=$i'>[$i]</a>";
		}
		if ($p < $pn) print " <a href='ip.php?ip_host=set&p=$in'>>></a>";
		print "</td></tr></table>";
	}

	print "<div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post ACTION='ip.php'><input type=hidden name=mode value='add'><input type=submit value=\"$MRTGMsg[139]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:140px\"></form></td>";
	print "<td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form></td></tr></table>";

} elseif ( isset($mode) && $mode == "view") {

	if (!isset($p)) $p = 1;

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title,agent_ip.community from agent_ip where agent_ip.id=".$id);
		$result_1 = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title from agent_ip where agent_ip.id=".$id);
		$row_1 = mysql_fetch_row($result_1);
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title,agent_ip.community from agent_ip where agent_ip.id=".$id);
		$row_1 = pg_fetch_row($result, 0);
		$rows = pg_num_rows($result);
	}

	$title_ip = $row_1[2];

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b><font color='#0000FF'>$title_ip</font></b></td></tr></table><br>";
	print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=50><b>#</b></td><td width=150><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[138]</b></td><td width=100><b>$MRTGMsg[4]</b></td><td width=130><b>$MRTGMsg[9]</b></td></tr>";

	print "<tr align=center bgcolor='#F0F0F0'>";

	for($i = 0; $i < count($rows); $i++ ) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		for ($j=0; $j < count($row); $j++) {
			$ip = split("/", $row[1]);
			$row[1] = $ip[0];
			if( $j==3 && $Show_Community == "0" ) print "<td>*****</td>";
			else print "<td>".$row[$j]."</td>";
		}
	}
	print "<td><a href='ip.php?id=$id&mode=edit'>$MRTGMsg[11]</a> | <a href='ip.php?id=$id&mode=delete'>$MRTGMsg[12]</a></td>";
	print "</tr></table><br><br>";

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[64] <font color='#0000FF'>$title_ip</font></b></td></tr></table>";
	print "<br><table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=50><b>$MRTGMsg[67]</b></td><td width=110><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[2]</b></td><td width=120><b>$MRTGMsg[3]</b></td><td width=100><b>$MRTGMsg[4]</b></td><td width=130><b>$MRTGMsg[9]</b></td></tr>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community from agent, agent_ip where agent.ip=agent_ip.id and agent.trash=0 and agent.ip=".$id." order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community from agent, agent_ip where agent.ip=agent_ip.id and agent.trash=0 and agent.ip=".$id." order by id asc");
		$rows = pg_num_rows($result);
	}

	$pn=ceil($rows/20);
	$ps=($p-1)*20+1;
	$pe=$p*20;
	if ($pe > $rows) $pe = $rows;

	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if (($i >= $ps-1) && ($i < $pe)) {
			print "<tr align=center bgcolor='#F0F0F0'><td>".$row[0]."</td>";
			$ip = split("/", $row[1]);
			print "<td>".$ip[0]."</td>";
			print "<td align=left>&nbsp;<a href='view.php?id=$row[0]'>".$row[2]."</a></td>";
			print "<td>".$row[3]."</td>";
			if( $Show_Community == "0" ) print "<td>*****</td>";
			else print "<td>".$row[4]."</td>";
			print "<td><a href='edit.php?id=$row[0]&p=$p'>$MRTGMsg[11]</a> | <a href='delete.php?id=$row[0]'>$MRTGMsg[12]</a></td>";
		}
	}
	
	if ($rows == 0) {
		$ps = 0;
		print "<tr bgcolor='#F0F0F0'><td colspan=6 align=center class=red>$MRTGMsg[5]</td></tr>";
		print "</table>";
	} else {
		print "</table>";
		print "<table cellpadding=2 cellspacing=0 width=100%><tr><td align=left><b>$MRTGMsg[6]:</b> [$ps - $pe] $MRTGMsg[7] $rows</td><td align=right><b>$MRTGMsg[8]: </b>";
		$ip=$p-1;
		$in=$p+1;
		if ($p > 1) print " <a href='ip.php?id=$id&mode=view&p=$ip'><<</a>";
		for ($i=1; $i<=$pn; $i++)
		{
			if ($i == $p) print "<b> [$i]</b>";
			else print " <a href='ip.php?id=$id&mode=view&p=$i'>[$i]</a>";
		}
		if ($p < $pn) print " <a href='ip.php?id=$id&mode=view&p=$in'>>></a>";
		print "</td></tr></table>";
	}

	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post ACTION='ip.php'><input type=hidden name=ip_host value='set'><input type=submit value=\"$MRTGMsg[137]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px\"></form></td>";
	print "<td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form></td></tr></table>";

} elseif ( isset($mode) && $mode == "edit" ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[13] $MRTGMsg[46] <font color='#0000FF'>$id</font></b></td></tr></table>";

	if (isset($id) && !(isset($save)) ) {

		$ip = $title = "";

		if($SQL_Type == "mysql") {
			$result = mysql_query("select agent_ip.id,agent_ip.ip,agent_ip.title,agent_ip.community from agent_ip where agent_ip.id=".$id);
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select agent_ip.id,agent_ip.ip,agent_ip.title,agent_ip.community from agent_ip where agent_ip.id=".$id);
			$row = pg_fetch_row($result);
		}

		$ip = split("/", $row[1]);
		$row[1] = $ip[0];

		print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'>";
		print "<tr bgcolor='#AABBCC' align=center><td width=20%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td width=20%><b>".$MRTGMsg[140]."</b></td><td><input type=hidden name='id' value='$row[0]'></input><font color='#0000FF'><b>$row[0]</b></font></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[1]."</td><td><input type='text' name='ip' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value='$row[1]'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[2]."</td><td><input type='text' name='title' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value='$row[2]'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[4]."</td><td><input type='password' name='community' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value='$row[3]'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td colspan=2><input type=hidden name=mode value='edit'><input type=hidden name=save value='set'><input type='submit' name='submit' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px' value='$MRTGMsg[43]'></input></td></tr></form></table>";

	} elseif ( isset($save) ) {
		if ( $ip == '' || $title == '' || $community == '' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=mode value='edit'><input type=hidden name=id value='$id'><input type=submit value=\"$MRTGMsg[137]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
		if($SQL_Type == "mysql") $result = @mysql_query("update agent_ip set ip='".$ip."',title='".$title."',community='".$community."' where id='".$id."'");
		else $result = @pg_query($db, "update agent_ip set ip='".$ip."',title='".$title."',community='".$community."' where id='".$id."'");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$id</font> $MRTGMsg[48]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=ip_host value='set'><input type=submit value=\"$MRTGMsg[137]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] <font color='#0000FF'>$id</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=mode value='edit'><input type=hidden name=id value='$id'><input type=submit value=\"$MRTGMsg[137]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	}

} elseif ( isset($mode) && $mode == "add" ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[141]</b></td></tr></table>";

	if ( !(isset($save)) ) {

		$ip = $title = "";

		if($SQL_Type == "mysql") {
			$result = mysql_query("select agent_ip.id from agent_ip order by agent_ip.id asc");
			$rows = mysql_num_rows($result);
		} else {
			$result = pg_query($db, "select agent_ip.id from agent_ip order by agent_ip.id asc");
			$rows = pg_num_rows($result);
		}
		$gids = 0;
		for ($i=0; $i<$rows; $i++) {
			$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
			if ( $i != $row[0] && $gids == 0) {
				$id = $i;
				$gids = 1;
			}
		}
		if ($gids == 0) $id = $rows;

		print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'>";
		print "<tr align=center bgcolor='#F0F0F0'><td width=20%><b>".$MRTGMsg[140]."</b></td><td><input type=hidden name='id' value='$id'></input><font color='#0000FF'><b>$id</b></font></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[1]."</td><td><input type='text' name='ip' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[2]."</td><td><input type='text' name='title' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[4]."</td><td><input type='password' name='community' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td colspan=2><input type=hidden name=mode value='add'><input type=hidden name=save value='set'><input type='submit' name='submit' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px' value='$MRTGMsg[43]'></input></td></tr></form></table>";

	} else {

		if ( $ip == '' || $title == '' || $community == '' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=mode value='add'><input type=submit value=\"$MRTGMsg[141]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:190px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
		if($SQL_Type == "mysql") $result = @mysql_query("insert into agent_ip (id, ip, title, community) values(".$id.",'".$ip."','".$title."','".$community."')");
		else $result = @pg_query($db, "insert into agent_ip (id, ip, title, community) values(".$id.",'".$ip."','".$title."','".$community."')");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$id</font> $MRTGMsg[51]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=ip_host value='set'><input type=submit value=\"$MRTGMsg[137]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[50] <font color='#0000FF'>$id</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=mode value='add'><input type=submit value=\"$MRTGMsg[141]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:190px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	}

} elseif ( isset($mode) && $mode == "delete" ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[25]</b></td></tr></table><br>";

	if ( isset($mode) && !(isset($delete)) && !(isset($confirm_delete)) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[26] <font color='#0000FF'>$id</font> ???</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION='$self'><input type=hidden name=mode value='delete'><input type=hidden name=delete value='set'><input type=hidden name=id value=$id><input type=submit value=\"$MRTGMsg[21]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[22]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
		print "</td></tr></table></td></tr></table></div>";
	} elseif ( isset($mode) && isset($delete) && !(isset($confirm_delete)) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[27] <font color='#0000FF'>$id</font> ???</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[21]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
		print "</td><td><form method=post ACTION='$self'><input type=hidden name=mode value='delete'><input type=hidden name=id value=$id><input type=hidden name=delete value='set'><input type=hidden name=confirm_delete value='set'><input type=submit value=\"$MRTGMsg[22]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
		print "</td></tr></table></td></tr></table></div>";
	} elseif ( isset($mode) && isset($delete) && isset($confirm_delete) ) {
		$records = array("id" => $id);
		if($SQL_Type == "mysql") $result = @mysql_query("delete FROM agent_ip WHERE id =".$id);
		else $result = @pg_delete($db, 'agent_ip', $records);
		if ($result) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[28] <font color='#0000FF'>$id</font> $MRTGMsg[29]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=ip_host value='set'><input type=submit value=\"$MRTGMsg[137]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[30] <font color='#0000FF'>$gid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='ip.php'><input type=hidden name=ip_host value='set'><input type=submit value=\"$MRTGMsg[137]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
		}

	}

}

HTMLBottomPrint();

?>
