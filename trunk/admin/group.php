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

HTMLTopPrint($MRTGMsg[54]);

$self = $_SERVER['PHP_SELF'];

if ( isset($group) && !(isset($mode)) ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[54]</b></td></tr></table><br>";

	if (!isset($p)) $p = 1;

	print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=30><b>$MRTGMsg[55]</b></td><td><b>$MRTGMsg[56]</b></td><td width=130><b>$MRTGMsg[9]</b></td></tr>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select mrtg_group.id,mrtg_group.title from mrtg_group order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select mrtg_group.id,mrtg_group.title from mrtg_group order by id asc");
		$rows = pg_num_rows($result);
	}

	$pn=ceil($rows/20);
	$ps=($p-1)*20+1;
	$pe=$p*20;
	if ($pe > $rows) $pe = $rows;

	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if (($i >= $ps-1) && ($i < $pe)) {
			print "<tr bgcolor='#F0F0F0'><td align=center>".$row[0]."</td>";
			print "<td>&nbsp;<a href='group.php?gid=$row[0]&mode=view'>".$row[1]."</a></td>";
			print "<td align=center><a href='group.php?gid=$row[0]&mode=edit'>$MRTGMsg[11]</a> | <a href='group.php?gid=$row[0]&mode=delete'>$MRTGMsg[12]</a></td>";
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
		if ($p > 1) print " <a href='group.php?group=set&p=$ip'><<</a>";
		for ($i=1; $i<=$pn; $i++) {
			if ($i == $p) print("<b> [$i]</b>");
			else print " <a href='group.php?group=set&p=$i'>[$i]</a>";
		}
		if ($p < $pn) print " <a href='group.php?group=set&p=$in'>>></a>";
		print "</td></tr></table>";
	}

	print "<div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post ACTION='group.php'><input type=hidden name=mode value='add'><input type=submit value=\"$MRTGMsg[72]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:130px\"></form></td>";
	print "<td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form></td></tr></table>";

} elseif ( isset($mode) && $mode == "view") {

	if (!isset($p)) $p = 1;

	if($SQL_Type == "mysql") {
		$result = mysql_query("select mrtg_group.title from mrtg_group where mrtg_group.id=".$gid);
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select mrtg_group.title from mrtg_group where mrtg_group.id=".$gid);
		$row = pg_fetch_row($result);
	}

	$title_group = $row[0];

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[63] <font color='#0000FF'>$title_group</font></b></td></tr></table><br>";
	print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40><b>$MRTGMsg[55]</b></td><td><b>$MRTGMsg[56]</b></td><td width=150><b>$MRTGMsg[9]</b></td></tr>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id=".$gid);
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id=".$gid);
		$rows = pg_num_rows($result);
	}

	print "<tr align=center bgcolor='#F0F0F0'>";

	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		for ($j=0; $j < count($row); $j++) print "<td>".$row[$j]."</td>";
	}
	print "<td><a href='group.php?gid=$gid&mode=edit'>$MRTGMsg[11]</a> | <a href='group.php?gid=$gid&mode=delete'>$MRTGMsg[12]</a></td>";
	print "</tr></table><br><br>";

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[64] <font color='#0000FF'>$title_group</font></b></td></tr></table><br>";

	if ( $gid != $GID_Trash ) {

		// Показываем состав группы (кроме Корзины)
		print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40><b>$MRTGMsg[58]</b></td><td width=40><b>$MRTGMsg[67]</b></td><td width=140><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[2]</b></td><td width=70><b>$MRTGMsg[61]</b></td><td width=70><b>$MRTGMsg[62]</b></td><td width=150><b>$MRTGMsg[9]</b></td></tr>";

		if($SQL_Type == "mysql") {
			$result = mysql_query("select templates.id, templates.agent_id, agent_ip.ip, agent.title, templates.hide_set, templates.row_set, templates.column_set from templates,mrtg_group,agent,agent_ip where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and agent.ip=agent_ip.id and mrtg_group.id=".$gid." and agent.trash=0 order by templates.id asc");
			$rows = mysql_num_rows($result);
		} else {
			$result = pg_query($db, "select templates.id, templates.agent_id, agent_ip.ip, agent.title, templates.hide_set, templates.row_set, templates.column_set from templates,mrtg_group,agent,agent_ip where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and agent.ip=agent_ip.id and mrtg_group.id=".$gid." and agent.trash=0 order by templates.id asc");
			$rows = pg_num_rows($result);
		}

		$pn=ceil($rows/20);
		$ps=($p-1)*20+1;
		$pe=$p*20;
		if ($pe > $rows) $pe = $rows;

		for ($i=0; $i<$rows; $i++) {
			$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
			if (($i >= $ps-1) && ($i < $pe)) {
				print "<tr align=center bgcolor='#F0F0F0'>";
				for ($j=0; $j < count($row); $j++) {
					if ($j == 2) {
						$ip = split("/", $row[$j]);
						print "<td>".$ip[0]."</td>";
					} elseif ($j == 3) {
						print "<td align=left>&nbsp;<a href='view.php?id=$row[1]'>".$row[$j]."</a></td>";
					} elseif ($j == 4) {
						if ( $row[4] == 1) $STATUS_HIDE = $MRTGMsg[73];
						else $STATUS_HIDE = $MRTGMsg[93];
					} else print "<td>".$row[$j]."</td>";
				}
				print "<td><a href='templates.php?sid=$row[0]&gid=$gid&hid=$row[1]&mode=edit'>$MRTGMsg[11]</a> | <a href='templates.php?sid=$row[0]&gid=$gid&hid=$row[1]&mode=delete'>$MRTGMsg[12]</a> | <a href='templates.php?sid=$row[0]&gid=$gid&hid=$row[1]&mode=hide'>$STATUS_HIDE</a></td>";
				print "</tr>";
			}
		}
		// Конец

	} else {

		// Показываем Корзину

		print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40><b>$MRTGMsg[67]</b></td><td width=120><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[2]</b></td><td width=120><b>$MRTGMsg[3]</b></td><td width=100><b>$MRTGMsg[4]</b></td><td width=200><b>$MRTGMsg[9]</b></td></tr>";

		if($SQL_Type == "mysql") {
			$result = mysql_query("select agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community from agent,agent_ip where agent.trash=$GID_Trash and agent.ip=agent_ip.id order by id asc");
			$rows = mysql_num_rows($result);
		} else {
			$result = pg_query($db, "select agent.id, agent_ip.ip, agent.title, agent.ver_snmp, agent_ip.community from agent,agent_ip where agent.trash=$GID_Trash and agent.ip=agent_ip.id order by id asc");
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
				print "<td>".$row[2]."</td>";
				print "<td>".$row[3]."</td>";
				if( $Show_Community == "0" ) print "<td>*****</td>";
				else print "<td>".$row[4]."</td>";
				print "<td><a href='view.php?id=$row[0]&mode=delete'>$MRTGMsg[10]</a> | <a href='delete.php?id=$row[0]&mode=restore'>$MRTGMsg[101]</a> | <a href='delete.php?id=$row[0]&mode=delete'>$MRTGMsg[12]</a></td>";
			}
		}

	}

	if ($rows == 0) {
		$ps = 0;
		if ($gid != $GID_Trash) print "<tr align=center bgcolor='#F0F0F0'><td colspan=6 class=red>$MRTGMsg[5]</td><td><a href='templates.php?gid=$gid&mode=add'>$MRTGMsg[65]</a></td></tr>";
		else print "<tr align=center bgcolor='#F0F0F0'><td colspan=6 class=red>$MRTGMsg[5]</td></tr>";
		print "</table>";
	} else {
		print "</table>";
		print "<table cellpadding=2 cellspacing=0 width=100%><tr><td align=left><b>$MRTGMsg[6]:</b> [$ps - $pe] $MRTGMsg[7] $rows</td><td align=right><b>$MRTGMsg[8]: </b>";
		$ip=$p-1;
		$in=$p+1;
		if ($p > 1) print " <a href='group.php?gid=$gid&mode=view&group=set&p=$ip'><<</a>";
		for ($i=1; $i<=$pn; $i++) {
			if ($i == $p) print("<b> [$i]</b>");
			else print " <a href='group.php?gid=$gid&mode=view&group=set&p=$i'>[$i]</a>";
		}
		if ($p < $pn) print " <a href='group.php?gid=$gid&mode=view&group=set&p=$in'>>></a>";
		print "</td></tr></table>";
	}

	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	if ( $gid != $GID_Trash ) print "<td><form method=post ACTION='templates.php'><input type=hidden name=mode value='add'><input type=hidden name=gid value='$gid'><input type=submit value='$MRTGMsg[74]' style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:200px\"></form></td>";
	print "<td><form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit value=\"$MRTGMsg[54]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:200px\"></form></td>";
	print "<td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form></td></tr></table>";

} elseif ( isset($mode) && $mode == "edit" ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[13] $MRTGMsg[46] <font color='#0000FF'>$gid</font></b></td></tr></table>";

	if (isset($gid) && !(isset($save)) ) {

		$title = "";

		if($SQL_Type == "mysql") {
			$result = mysql_query("select mrtg_group.id, mrtg_group.title from mrtg_group where mrtg_group.id=".$gid);
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select mrtg_group.id, mrtg_group.title from mrtg_group where mrtg_group.id=".$gid);
			$row = pg_fetch_row($result);
		}

		print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'>";
		print "<tr bgcolor='#AABBCC' align=center><td width=20%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td width=20%><b>".$MRTGMsg[55]."</b></td><td><input type=hidden name='gid' value='$row[0]'></input><font color='#0000FF'><b>$row[0]</b></font></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[70]."</td><td><input type='text' name='title' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value='$row[1]'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td colspan=2><input type=hidden name=mode value='edit'><input type=hidden name=save value='set'><input type='submit' name='submit' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px' value='$MRTGMsg[43]'></input></td></tr></form></table>";

	} elseif ( isset($save) ) {
		if ( $title == '' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=mode value='edit'><input type=hidden name=gid value='$gid'><input type=submit value=\"$MRTGMsg[75]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:170px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
		if($SQL_Type == "mysql") $result = @mysql_query("update mrtg_group set id='".$gid."',title='".$title."' where id='".$gid."'");
		else $result = @pg_query($db, "update mrtg_group set id='".$gid."',title='".$title."' where id='".$gid."'");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$gid</font> $MRTGMsg[48]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit value=\"$MRTGMsg[54]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:200px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] <font color='#0000FF'>$gid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=mode value='edit'><input type=hidden name=gid value='$gid'><input type=submit value=\"$MRTGMsg[75]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:170px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	}

} elseif ( isset($mode) && $mode == "add" ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width=100% class=red><b>$MRTGMsg[69]</b></td></tr></table>";

	if ( !(isset($save)) ) {

		$title = "";

		if($SQL_Type == "mysql") {
			$result = mysql_query("select mrtg_group.id from mrtg_group order by id asc");
			$rows = mysql_num_rows($result);
		} else {
			$result = pg_query($db, "select mrtg_group.id from mrtg_group order by id asc");
			$rows = pg_num_rows($result);
		}

		$gids = 0;
		for ($i=0; $i<$rows; $i++) {
			$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
			if ( $i != $row[0] && $gids == 0) {
				$gid = $i;
				$gids = 1;
			}
		}
		if ($gids == 0) $gid = $rows;

		print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'>";
		print "<tr align=center bgcolor='#F0F0F0'><td width=20%><b>".$MRTGMsg[55]."</b></td><td><input type=hidden name='gid' value='$gid'></input><font color='#0000FF'><b>$gid</b></font></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[70]."</td><td><input type='text' name='title' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td colspan=2><input type=hidden name=mode value='add'><input type=hidden name=save value='set'><input type='submit' name='submit' style='color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width: 100px' value='$MRTGMsg[43]'></input></td></tr></form></table>";

	} else {

		if ( $title == '' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=mode value='add'><input type=submit value=\"$MRTGMsg[72]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:130px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
		if($SQL_Type == "mysql") $result = @mysql_query("insert into mrtg_group (id, title) values(".$gid.",'".$title."')");
		else $result = @pg_query($db, "insert into mrtg_group (id, title) values(".$gid.",'".$title."')");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$gid</font> $MRTGMsg[51]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit value=\"$MRTGMsg[54]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:200px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[50] <font color='#0000FF'>$gid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=mode value='add'><input type=submit value=\"$MRTGMsg[72]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:130px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	}

} elseif ( isset($mode) && $mode == "delete" ) {

	print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[25]</b></td></tr></table><br>";

	if ( isset($mode) && ( $gid == 0 || $gid == $GID_Trash ) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[68]</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit value=\"$MRTGMsg[54]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:200px\"></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
		print "</td></tr></table></td></tr></table></div>";
		exit;
	}

	if ( isset($mode) && !(isset($confirm_delete)) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[26] <font color='#0000FF'>$gid</font> ???</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION=$self><input type=hidden name=mode value='delete'><input type=hidden name=gid value=$gid><input type=hidden name=confirm_delete value='set'><input type=submit value=\"$MRTGMsg[21]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[22]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:70px\"></form>";
		print "</td></tr></table></td></tr></table></div>";
	} elseif ( isset($mode) && isset($confirm_delete) ) {
		$records = array("id" => $gid);
		if($SQL_Type == "mysql") $result = @mysql_query("delete FROM mrtg_group WHERE id =".$gid);
		else $result = @pg_delete($db, 'mrtg_group', $records);
		if ($result) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[28] <font color='#0000FF'>$gid</font> $MRTGMsg[29]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit value=\"$MRTGMsg[54]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:200px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[30] <font color='#0000FF'>$gid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit value=\"$MRTGMsg[54]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:200px\"></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></td></tr></table></div>";
		}

	}

}

HTMLBottomPrint();

?>
