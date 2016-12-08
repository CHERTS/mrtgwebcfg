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

HTMLTopPrint($MRTGMsg[53]);

$self = $_SERVER['PHP_SELF'];

if ( $p == '') $p = 1;

if ( isset($templates) && !(isset($mode)) ) {

	if (!isset($p)) $p = 1;

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[53]</b></td></tr></table><br>";
	print "<table width='100%' cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=50><b>$MRTGMsg[58]</b></td><td width=50><b>$MRTGMsg[67]</b></td><td width=50><b>$MRTGMsg[55]</b></td><td><b>$MRTGMsg[59]</b></td><td width=70><b>$MRTGMsg[61]</b></td><td width=70><b>$MRTGMsg[62]</b></td><td width=150><b>$MRTGMsg[9]</b></td></tr>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select templates.id, templates.agent_id, templates.group_id, mrtg_group.title, templates.row_set, templates.column_set, templates.hide_set from mrtg_group,templates,agent where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and mrtg_group.id != $GID_Trash order by templates.id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select templates.id, templates.agent_id, templates.group_id, mrtg_group.title, templates.row_set, templates.column_set, templates.hide_set from mrtg_group,templates,agent where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and mrtg_group.id != $GID_Trash order by templates.id asc");
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
			print "<td>".$row[1]."</td>";
			print "<td>".$row[2]."</td>";
			print "<td><a href='group.php?gid=$row[2]&mode=view'>".$row[3]."</a></td>";
			print "<td>".$row[4]."</td>";
			print "<td>".$row[5]."</td>";
			if ( $row[6] == 1) $STATUS_HIDE = $MRTGMsg[73];
			else $STATUS_HIDE = $MRTGMsg[93];
			print "<td><a href='templates.php?sid=$row[0]&hid=$row[1]&gid=$row[2]&mode=edit'>$MRTGMsg[11]</a> | <a href='templates.php?sid=$row[0]&hid=$row[1]&gid=$row[2]&mode=delete'>$MRTGMsg[12]</a> | <a href='templates.php?sid=$row[0]&hid=$row[1]&gid=$row[2]&mode=hide'>$STATUS_HIDE</a></td>";
		}
	}

	if ($rows == 0) {
		$ps = 0;
		print "<tr bgcolor='#F0F0F0'><td colspan=7 align=center class=red>$MRTGMsg[5]</td></tr>";
		print "</table>";
	} else {
		print "</table>";
		print "<table cellpadding=2 cellspacing=0 width='100%'><tr><td align=left><b>$MRTGMsg[6]:</b> [$ps - $pe] $MRTGMsg[7] $rows</td><td align=right><b>$MRTGMsg[8]: </b>";
		$ip=$p-1;
		$in=$p+1;
		if ($p > 1) print " <a href='templates.php?templates=set&amp;p=$ip'><<</a>";
		for ($i=1; $i<=$pn; $i++) {
			if ($i == $p) print "<b> [$i]</b>";
			else print " <a href='templates.php?templates=set&amp;p=$i'>[$i]</a>";
		}
		if ($p < $pn) print " <a href='templates.php?templates=set&amp;p=$in'>>></a>";
		print "</td></tr></table>";
	}

	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post ACTION='templates.php'><input type=hidden name=mode value='add'><input type=hidden name=templates value='set'><input type=submit class='submit_main_button' style='width:140px' value='$MRTGMsg[74]'></form></td>";
	print "<td><form method=post ACTION='templates-gui.php'><input type=submit class='submit_main_button' style='width:220px' value='$MRTGMsg[121]'></form></td>";
	print "<td><form method=post ACTION='index.php'><input type=submit class='submit_main_button' style='width:100px' value='$MRTGMsg[24]'></form></td></tr></table>";

} elseif ( isset($mode) && $mode == "edit" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[13] SID <font color='#0000FF'>$sid</font></b></td></tr></table>";

	if (isset($sid) && !(isset($save)) ) {

		$id = $agent_id = $group_id = $row_set = $column_set = "";

		// Показываем описание
		print "<br><table width='100%' cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td><b>$MRTGMsg[67]</b></td><td><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[2]</b></td><td><b>$MRTGMsg[3]</b></td><td><b>$MRTGMsg[4]</b></td></tr>";
		if($SQL_Type == "mysql") {
			$result = mysql_query("select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community from agent, agent_ip where agent.ip=agent_ip.id and agent.id=".$hid);
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community from agent, agent_ip where agent.ip=agent_ip.id and agent.id=".$hid);
			$row = pg_fetch_row($result);
		}
		print "<tr align=center bgcolor='#F0F0F0'><td>".$row[0]."</td>";
		$ip = split("/", $row[1]);
		print "<td>".$ip[0]."</td>";
		print "<td>".$row[2]."</td>";
		print "<td>".$row[3]."</td>";
		if( $Show_Community == "0" ) print "<td>*****</td>";
		else print "<td>".$row[4]."</td>";
		print "</table><br>";
		// Заканчиваем вывод

		// Запрос на выбор группы
		if($SQL_Type == "mysql") {
			$result_group = mysql_query("select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id != $GID_Trash order by id asc");
			$rows_group = mysql_num_rows($result_group);
		} else {
			$result_group = pg_query($db, "select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id != $GID_Trash order by id asc");
			$rows_group = pg_num_rows($result_group);
		}
		// Конец

		// Запрос на выбор хоста
		if($SQL_Type == "mysql") {
			$result_hid = mysql_query("select agent.id, agent.title from agent order by id asc");
			$rows_hid = mysql_num_rows($result_hid);
		} else {
			$result_hid = pg_query($db, "select agent.id, agent.title from agent order by id asc");
			$rows_hid = pg_num_rows($result_hid);
		}
		// Конец

		if($SQL_Type == "mysql") {
			$result = mysql_query("select templates.id, templates.agent_id, templates.group_id, templates.row_set, templates.column_set  from templates where templates.id=".$sid);
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select templates.id, templates.agent_id, templates.group_id, templates.row_set, templates.column_set  from templates where templates.id=".$sid);
			$row = pg_fetch_row($result);
		}

		print "<br><table width='100%' align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'>";
		print "<tr bgcolor='#AABBCC' align=center><td width=20%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td width=20%><b>".$MRTGMsg[58]."</b></td><td><input type=hidden name='sid' value='$row[0]'></input><font color='#0000FF'><b>$row[0]</b></font></td></tr>";

		// Показываем список групп
		if ( $gid == 0 && $SET_Access_Default_Group_Edit == "0") {
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[67]."</td><td><input type=hidden name='agent_id' value='$row[1]'></input><font color='#0000FF'><b>$row[1]</b></font></td></tr>";
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[55]."</td><td><input type=hidden name='group_id' value='$gid'></input><font color='#0000FF'><b>$gid</b></font></td></tr>";
		} else {
			// Показываем список хостов
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[67]."</td><td>";
			print "<SELECT name='agent_id' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
			for ($h=0; $h<$rows_hid; $h++) {
				$row_hid = ($SQL_Type == "mysql") ? mysql_fetch_row($result_hid) : pg_fetch_row($result_hid, $h);
				if ( $row[1] == $row_hid[0]) print "<option selected value='$row_hid[0]'>$row_hid[0] - $row_hid[1]";
				else print "<option value='$row_hid[0]'>$row_hid[0] - $row_hid[1]";
			}
			print "</SELECT></td></tr>";
			// Конец
			print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[55]."</td><td>";
			print "<SELECT name='group_id' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
			for ($i=0; $i<$rows_group; $i++) {
				$row_group = ($SQL_Type == "mysql") ? mysql_fetch_row($result_group) : pg_fetch_row($result_group, $i);
				if ( $gid == $row_group[0]) print "<option selected value='$row_group[0]'>$row_group[1]";
				else print "<option value='$row_group[0]'>$row_group[1]";
			}
			print "</SELECT></td></tr>";
		}
		// Конец

		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[61]."</td><td><input type='text' name='row_set' value='$row[3]'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[62]."</td><td><input type='text' name='column_set' value='$row[4]'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td colspan=2><input type=hidden name=p value='$p'><input type=hidden name=mode value='edit'><input type=hidden name=save value='set'><input type='submit' name='submit' class='submit_button' value='$MRTGMsg[43]'></input></td></tr></form></table>";

	} elseif ( isset($save) ) {
		if ( $agent_id == '' || $group_id == '' || $row_set == '' || $column_set == '') {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=mode value='edit'><input type=hidden name=hid value='$agent_id'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:170px' value='$MRTGMsg[76]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} elseif ( $column_set > 2 ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[98] <font color='#0000FF'>2</font> $MRTGMsg[217]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=mode value='add'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:170px' value='$MRTGMsg[74]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
		$result = ($SQL_Type == "mysql") ? @mysql_query("update templates set agent_id='".$agent_id."',group_id='".$group_id."',row_set='".$row_set."',column_set='".$column_set."',hide_set='1' where id='".$sid."'") : @pg_query($db, "update templates set agent_id='".$agent_id."',group_id='".$group_id."',row_set='".$row_set."',column_set='".$column_set."',hide_set='1' where id='".$sid."'");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$sid</font> $MRTGMsg[48]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=templates value='set'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[53]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] <font color='#0000FF'>$sid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=mode value='edit'><input type=hidden name=hid value='$agent_id'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:170px' value='$MRTGMsg[76]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	}

} elseif ( isset($mode) && $mode == "add" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[78]</b></td></tr></table>";

	if ( !(isset($save)) ) {

		$title = "";

		// Запрос на выбор группы
		if($SQL_Type == "mysql") {
			$result_group = mysql_query("select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id != $GID_Trash order by id asc");
			$rows_group = mysql_num_rows($result_group);
		} else {
			$result_group = pg_query($db, "select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id != $GID_Trash order by id asc");
			$rows_group = pg_num_rows($result_group);
		}
		// Конец

		// Запрос на выбор хоста
		if($SQL_Type == "mysql") {
			$result_hid = mysql_query("select agent.id, agent.title from agent order by id asc");
			$rows_hid = mysql_num_rows($result_hid);
		} else {
			$result_hid = pg_query($db, "select agent.id, agent.title from agent order by id asc");
			$rows_hid = pg_num_rows($result_hid);
		}
		// Конец

		// Запрос на SID
		if($SQL_Type == "mysql") {
			$result = mysql_query("select templates.id from templates order by id asc");
			$rows = mysql_num_rows($result);
		} else {
			$result = pg_query($db, "select templates.id from templates order by id asc");
			$rows = pg_num_rows($result);
		} 
		$sids = 0;
		for ($i=0; $i<$rows; $i++) {
			$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
			if ( $i != $row[0] && $sids == 0) {
				$sid = $i;
				$sids = 1;
			}
		}
		if ($sids == 0) $sid = $rows;
		// Конец

		print "<br><table width='100%' align=center cellpadding=2 cellspacing=1 bgcolor='#808080'><form methode='post' action='$self'>";
		print "<tr align=center bgcolor='#F0F0F0'><td width=20%><b>".$MRTGMsg[58]."</b></td><td><input type=hidden name='sid' value='$sid'></input><font color='#0000FF'><b>$sid</b></font></td></tr>";

		// Показываем список хостов
		//print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[67]."</td><td><input type='text' name='agent_id' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%' value=''></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[67]."</td><td>";
		print "<SELECT name='agent_id' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
		for ($h=0; $h<$rows_hid; $h++) {
			$row_hid = ($SQL_Type == "mysql") ? mysql_fetch_row($result_hid) : pg_fetch_row($result_hid, $h);
			if ( $hid == $row_hid[0]) print "<option selected value='$row_hid[0]'>$row_hid[0] - $row_hid[1]";
			else print "<option value='$row_hid[0]'>$row_hid[0] - $row_hid[1]";
		}
		print "</SELECT></td></tr>";
		// Конец

		// Показываем список групп
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[55]."</td><td>";
		print "<SELECT name='group_id' style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 90%'>";
		for ($i=0; $i<$rows_group; $i++) {
			$row_group = ($SQL_Type == "mysql") ? mysql_fetch_row($result_group) : pg_fetch_row($result_group, $i);
			if ( $gid == $row_group[0]) print "<option selected value='$row_group[0]'>$row_group[1]";
			else print "<option value='$row_group[0]'>$row_group[1]";
		}
		print "</SELECT></td></tr>";
		// Конец

		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[61]."</td><td><input type='text' name='row_set' value='$row_set'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td class=blue>".$MRTGMsg[62]."</td><td><input type='text' name='column_set' value='$column_set'></input></td></tr>";
		print "<tr align=center bgcolor='#F0F0F0'><td colspan=2><input type=hidden name=p value='$p'><input type=hidden name=mode value='add'><input type=hidden name=save value='set'><input type='submit' name='submit' class='submit_button' style='width:100px' value='$MRTGMsg[43]'></input></td></tr></form></table>";

	} else {

		if ( $agent_id == '' || $group_id == '' || $row_set == '' || $column_set == '') {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[52]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=mode value='add'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:170px' value='$MRTGMsg[74]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} elseif ( $column_set > 2 ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[98] <font color='#0000FF'>2</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=mode value='add'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:170px' value='$MRTGMsg[74]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
		//$templates_check = pg_query($db, "select templates.id, templates.agent_id, templates.group_id, mrtg_group.title, templates.row_set, templates.column_set, templates.hide_set from mrtg_group,templates,agent where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and mrtg_group.id=$group_id and agent.id=$agent_id and templates.hide_set=1 order by templates.id asc");
		//$templates_check_rows = pg_num_rows($templates_check);
		//if($templates_check_rows == 0) {
			$subgroup_id = "NULL";
			$result = ($SQL_Type == "mysql") ? @mysql_query("insert into templates (id, agent_id, group_id, subgroup_id, row_set, column_set, hide_set) values(".$sid.",".$agent_id.",".$group_id.",".$subgroup_id.",".$row_set.",".$column_set.",1)") : @pg_query($db, "insert into templates (id, agent_id, group_id, subgroup_id, row_set, column_set, hide_set) values(".$sid.",".$agent_id.",".$group_id.",".$subgroup_id.",".$row_set.",".$column_set.",1)");
			if($result) {
				print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$sid</font> $MRTGMsg[51]</b><br><br>";
				print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
				print "<form method=post ACTION='templates.php'><input type=hidden name=templates value='set'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[53]'></form>";
				print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
				print "</td></tr></table></td></tr></table></div>";
				exit;
			} else {
				print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[50] <font color='#0000FF'>$sid</font></b><br><br>";
				print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
				print "<form method=post ACTION='templates.php'><input type=hidden name=mode value='add'><input type=submit class='submit_button' style='width:130px' value='$MRTGMsg[74]'></form>";
				print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
				print "</td></tr></table></td></tr></table></div>";
				exit;
			}
		/*
		} else {
			print "Templates alredy exist";
			exit;
		}*/
	}

} elseif ( isset($mode) && $mode == "delete" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[25]</b></td></tr></table><br>";

	if ( isset($mode) && $gid == 0 && $SET_Access_Default_Group_Delete == "0") {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[77]</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION='templates.php'><input type=hidden name=templates value='set'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[53]'></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
		print "</td></tr></table></td></tr></table></div>";
		exit;
	}

	if ( isset($mode) && !(isset($confirm_delete)) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[26] <font color='#0000FF'>$sid</font>?</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION=$self><input type=hidden name=p value='$p'><input type=hidden name=mode value='delete'><input type=hidden name=sid value=$sid><input type=hidden name=gid value=$gid><input type=hidden name=confirm_delete value='set'><input type=submit class='submit_button' style='width:70px' value='$MRTGMsg[21]'></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:70px' value='$MRTGMsg[22]'></form>";
		print "</td></tr></table></td></tr></table></div>";
	} elseif ( isset($mode) && isset($confirm_delete) ) {
		$records = array("id" => $sid);
		if($SQL_Type == "mysql") $result = @mysql_query("delete FROM templates WHERE id =".$sid);
		else $result = @pg_delete($db, 'templates', $records);
		if ($result) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[28] <font color='#0000FF'>$sid</font> $MRTGMsg[29]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=templates value='set'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[53]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[30] <font color='#0000FF'>$sid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=templates value='set'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[53]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		}

	}

} elseif ( isset($mode) && $mode == "hide" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[85]</b></td></tr></table><br>";

	if ($hide_sid == '') {

		if($SQL_Type == "mysql") {
			$result = mysql_query("select templates.hide_set from templates where templates.agent_id=".$hid." and templates.group_id=".$gid." and templates.id=".$sid." order by templates.id asc");
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select templates.hide_set from templates where templates.agent_id=".$hid." and templates.group_id=".$gid." and templates.id=".$sid." order by templates.id asc");
			$row = pg_fetch_row($result);
		}
		if ( $row[0] == 0 ) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[86]<br>$MRTGMsg[87] <font color='#0000FF'>$sid</font> $MRTGMsg[89]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=p value='$p'><input type=hidden name=mode value='hide'><input type=hidden name=hide_sid value='1'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[91]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_main_button' style='width:100px' value='$MRTGMsg[92]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[86]<br>$MRTGMsg[87] <font color='#0000FF'>$sid</font> $MRTGMsg[88]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=p value='$p'><input type=hidden name=mode value='hide'><input type=hidden name=hide_sid value='0'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[90]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[92]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		}

	} else {
		$result = ($SQL_Type == "mysql") ? @mysql_query("update templates set hide_set='".$hide_sid."' where id='".$sid."'") : @pg_query($db, "update templates set hide_set='".$hide_sid."' where id='".$sid."'");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$sid</font> $MRTGMsg[48]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=templates value='set'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[53]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] <font color='#0000FF'>$sid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=mode value='edit'><input type=hidden name=hid value='$agent_id'><input type=hidden name=sid value='$sid'><input type=submit class='submit_button' style='width:170px' value='$MRTGMsg[76]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	}

} elseif ( isset($mode) && $mode == "change" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[121]</b></td></tr></table><br>";

	//$result = pg_query($db, "select templates.id, templates.agent_id, templates.group_id, mrtg_group.title, templates.row_set, templates.column_set, templates.hide_set from mrtg_group,templates,agent where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.id=$sid order by templates.id asc");
	if($SQL_Type == "mysql") {
		$result = mysql_query("select templates.id, templates.agent_id, templates.group_id, templates.row_set, templates.column_set, templates.hide_set from templates where templates.id=$sid order by templates.id asc");
		$rows = mysql_num_rows($result);
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select templates.id, templates.agent_id, templates.group_id, templates.row_set, templates.column_set, templates.hide_set from templates where templates.id=$sid order by templates.id asc");
		$rows = pg_num_rows($result);
		$row = pg_fetch_row($result, 0);
	}

	//if( $sid == $row[0] &&  $hid == $row[1] && $gid == $row[2] && $row_set == $row[3] && $column_set == $row[4] ) {
	if( $sid == $row[0] && $gid == $row[2] && $row_set == $row[3] && $column_set == $row[4] ) {
		$result = ($SQL_Type == "mysql") ? @mysql_query("update templates set agent_id='".$newhid."' where id='".$sid."'") : @pg_query($db, "update templates set agent_id='".$newhid."' where id='".$sid."'");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$sid</font> $MRTGMsg[48]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates-gui.php'><input type=submit class='submit_button' style='width:250px' value='$MRTGMsg[121]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] <font color='#0000FF'>$sid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates-gui.php'><input type=submit class='submit_button' style='width:250px' value='$MRTGMsg[121]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] <font color='#0000FF'>$sid</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates-gui.php'><input type=submit class='submit_button' style='width:250px' value='$MRTGMsg[121]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
	}

} elseif ( isset($mode) && $mode == "errors" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[171]</b></td></tr></table><br>";

	if ($err_set == '') {

		if($SQL_Type == "mysql") {
			$result = mysql_query("select agent.errors from agent where agent.id=".$id);
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select agent.errors from agent where agent.id=".$id);
			$row = pg_fetch_row($result);
		}
		if ( $row[0] == 0 ) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[86]<br>$MRTGMsg[172] <font color='#0000FF'>$id</font> $MRTGMsg[173]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=p value='$p'><input type=hidden name=mode value='errors'><input type=hidden name=err_set value='1'><input type=hidden name=id value='$id'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[175]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[92]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[86]<br>$MRTGMsg[172] <font color='#0000FF'>$id</font> $MRTGMsg[174]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='templates.php'><input type=hidden name=p value='$p'><input type=hidden name=mode value='errors'><input type=hidden name=err_set value='0'><input type=hidden name=id value='$id'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[176]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[92]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		}

	} else {
		$result = ($SQL_Type == "mysql") ? @mysql_query("update agent set errors='".$err_set."' where id='".$id."'") : @pg_query($db, "update agent set errors='".$err_set."' where id='".$id."'");
		if ($result) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] <font color='#0000FF'>$id</font> $MRTGMsg[48]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[49] <font color='#0000FF'>$id</font></b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' style='width:100px' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
			exit;
		}
	}

}

HTMLBottomPrint();

?>