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

HTMLTopPrint($MRTGMsg[121]);

if ( isset($set_templates) || $set_templates == '' ) {

	if ($mode != 'view') {
		$gid = 0;
		$group_name = "Default";
	} else {
		if($SQL_Type == "mysql") {
			$result = mysql_query("select mrtg_group.title from mrtg_group where mrtg_group.id=$gid");
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select mrtg_group.title from mrtg_group where mrtg_group.id=$gid");
			$row = pg_fetch_row($result);
		}
		$group_name = $row[0];
	}

	if($SQL_Type == "mysql") {
		$result_max = mysql_query("select max(templates.row_set), max(templates.column_set) from agent, mrtg, mrtg_group, templates where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and templates.hide_set=1 and mrtg_group.id=$gid");
		$row_max = mysql_fetch_row($result_max);
	} else {
		$result_max = pg_query($db, "select max(templates.row_set), max(templates.column_set) from agent, mrtg, mrtg_group, templates where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and templates.hide_set=1 and mrtg_group.id=$gid");
		$row_max = pg_fetch_row($result_max);
	}
	$index_rows = $row_max[0];
	$index_column = $row_max[1];

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b><font color='#0000FF'>$MRTGMsg[121]</font></b></td></tr>\n";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[59] <font color='#0000FF'>$group_name</font></b></td></tr></table>\n";

	print "<br><div align=center><table cellpadding=8 cellspacing=1 width='100%' bgcolor='#998080'>\n";

	for ($i=1; $i<=$index_rows; $i++) {

		if($SQL_Type == "mysql") {
			$result = mysql_query("select distinct agent.id, agent.title, mrtg.filename, templates.row_set, templates.column_set, templates.id, templates.hide_set from agent, mrtg_group, mrtg, templates where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and mrtg_group.id=$gid and templates.row_set=$i and agent.trash=0 order by templates.column_set asc");
			$rows = mysql_num_rows($result);
		} else {
			$result = pg_query($db, "select distinct agent.id, agent.title, mrtg.filename, templates.row_set, templates.column_set, templates.id, templates.hide_set from agent, mrtg_group, mrtg, templates where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and mrtg_group.id=$gid and templates.row_set=$i and agent.trash=0 order by templates.column_set asc");
			$rows = pg_num_rows($result);
		}

		if ( $rows == 1 ) {
			$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, 0);
			if ($row[4] == 1) {
				print "<tr bgcolor='#F0F0F0'>\n";
				print "<td width='50%'><table cellpadding=4 cellspacing=0 width='100%'><tr><td><b>$row[1]</b></td></tr><tr><td>\n";
				print "<SELECT name='agent_".$row[3]."_".$row[4]."' onchange=\"change('set_".$row[3]."_".$row[4]."',this.value)\">\n";
				print gui_select($row[0], "templates.php?sid=$row[5]&amp;gid=$gid&amp;row_set=$row[3]&amp;column_set=$row[4]&amp;mode=change");
				if ( $row[6] == 1) {
					$STATUS_HIDE = $MRTGMsg[73];
					$TITLE_HIDE = $MRTGMsg[178];
				} else {
					$STATUS_HIDE = $MRTGMsg[93];
					$TITLE_HIDE = $MRTGMsg[181];
				}
				print "</SELECT></td></tr><tr><td>$MRTGMsg[61]: $row[3] | $MRTGMsg[62]: $row[4] | <a href='' id='set_".$row[3]."_".$row[4]."' title='$MRTGMsg[177]'>$MRTGMsg[124]</a> | <a href='templates.php?sid=$row[5]&amp;hid=$row[0]&amp;gid=$gid&amp;mode=hide' title='$TITLE_HIDE'>$STATUS_HIDE</a> | <a href='templates.php?sid=$row[5]&amp;hid=$row[0]&amp;gid=$gid&amp;mode=delete' title='$MRTGMsg[179]'>$MRTGMsg[12]</a> | <a href='addrows.php?gid=$gid&amp;addrows=$row[3]' title='$MRTGMsg[125]'>+</a>";
				print "</td></tr></table></td>\n";
				print "<td width='50%'><table cellpadding=4 cellspacing=0 width='100%'><tr><td><b>$MRTGMsg[122]</b></td></tr><tr><td>\n";
				print "<SELECT name='id_a_".$row[3]."' onchange=\"change('id_".$row[3]."',this.value)\">\n";
				print "<option selected value=''> </option>\n";
				print gui_select("-1", "templates.php?gid=$gid&amp;row_set=$row[3]&amp;column_set=".($row[4]+1)."&amp;mode=add");
				print "</SELECT></td></tr><tr><td>$MRTGMsg[61]: $row[3] | $MRTGMsg[62]: ".($row[4]+1)." | <a href='' id='id_".$row[3]."' title='$MRTGMsg[180]'>$MRTGMsg[123]</a> | <a href='addrows.php?gid=$gid&amp;addrows=$row[3]' title='$MRTGMsg[125]'>+</a>";
				print "</td></tr></table></td></tr>\n";
			} else {
				print "<tr bgcolor='#F0F0F0'>\n";
				print "<td width='50%'><table cellpadding=4 cellspacing=0 width='100%'><tr><td><b>$MRTGMsg[122]</b></td></tr><tr><td>\n";
				print "<SELECT name='id_b_".$row[3]."' onchange=\"change('id_".$row[3]."',this.value)\">";
				print "<option selected value=''> </option>\n";
				print gui_select("-1", "templates.php?gid=$gid&amp;row_set=$row[3]&amp;column_set=".($row[4]-1)."&amp;mode=add");
				print "</SELECT></td></tr><tr><td>$MRTGMsg[61]: $row[3] | $MRTGMsg[62]: ".($row[4]-1)." | <a href='' id='id_".$row[3]."' title='$MRTGMsg[180]'>$MRTGMsg[123]</a> | <a href='addrows.php?gid=$gid&amp;addrows=$row[3]' title='$MRTGMsg[125]'>+</a>";
				print "</td></tr></table></td>\n";
				print "<td width='50%'><table cellpadding=4 cellspacing=0 width='100%'><tr><td><b>$row[1]</b></td></tr><tr><td>\n";
				print "<SELECT name='agent_".$row[3]."_".$row[4]."' onchange=\"change('set_".$row[3]."_".$row[4]."',this.value)\"\n";
				print gui_select($row[0], "templates.php?sid=$row[5]&amp;gid=$gid&amp;row_set=$row[3]&amp;column_set=$row[4]&amp;mode=change");
				if ( $row[6] == 1) {
					$STATUS_HIDE = $MRTGMsg[73];
					$TITLE_HIDE = $MRTGMsg[178];
				} else {
					$STATUS_HIDE = $MRTGMsg[93];
					$TITLE_HIDE = $MRTGMsg[181];
				}
				print "</SELECT></td></tr><tr><td>$MRTGMsg[61]: $row[3] | $MRTGMsg[62]: $row[4] | <a href='' id='set_".$row[3]."_".$row[4]."' title='$MRTGMsg[177]'>$MRTGMsg[124]</a> | <a href='templates.php?sid=$row[5]&amp;hid=$row[0]&amp;gid=$gid&amp;mode=hide' title='$TITLE_HIDE'>$STATUS_HIDE</a> | <a href='templates.php?sid=$row[5]&amp;hid=$row[0]&amp;gid=$gid&amp;mode=delete' title='$MRTGMsg[179]'>$MRTGMsg[12]</a> | <a href='addrows.php?gid=$gid&amp;addrows=$row[3]' title='$MRTGMsg[125]'>+</a>";
				print "</td></tr></table></td></tr>\n";
			}
		} elseif ( $rows == 2 ) {
			$row_1 = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, 0);
			$row_2 = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, 1);

		        print "<tr bgcolor='#F0F0F0'><td width='50%'>
			<table cellpadding=4 cellspacing=0 width='100%'><tr><td><b>$row_1[1]</b></td></tr><tr><td>";
			print "<SELECT name='agent_".$row_1[3]."_".$row_1[4]."' onchange=\"change('set_".$row_1[3]."_".$row_1[4]."',this.value)\">";
			print gui_select($row_1[0], "templates.php?sid=$row_1[5]&amp;gid=$gid&amp;row_set=$row_1[3]&amp;column_set=$row_1[4]&amp;mode=change");
			if ( $row_1[6] == 1) {
				$STATUS_HIDE = $MRTGMsg[73];
				$TITLE_HIDE = $MRTGMsg[178];
			} else {
				$STATUS_HIDE = $MRTGMsg[93];
				$TITLE_HIDE = $MRTGMsg[181];
			}
			print "</SELECT></td></tr><tr><td>$MRTGMsg[61]: $row_1[3] | $MRTGMsg[62]: $row_1[4] | <a href='' id='set_".$row_1[3]."_".$row_1[4]."' title='$MRTGMsg[177]'>$MRTGMsg[124]</a> | <a href='templates.php?sid=$row_1[5]&amp;hid=$row_1[0]&amp;gid=$gid&amp;mode=hide' title='$TITLE_HIDE'>$STATUS_HIDE</a> | <a href='templates.php?sid=$row_1[5]&amp;hid=$row_1[0]&amp;gid=$gid&amp;mode=delete' title='$MRTGMsg[179]'>$MRTGMsg[12]</a> | <a href='addrows.php?gid=$gid&amp;addrows=$row_1[3]' title='$MRTGMsg[125]'>+</a>";
			print "</td></tr></table></td>";
			print "<td width='50%'><table cellpadding=0 cellspacing=0 width='100%'><tr><td><b>$row_2[1]</b></td></tr><tr><td>";
			print "<SELECT name='agent_".$row_2[3]."_".$row_2[4]."' onchange=\"change('set_".$row_2[3]."_".$row_2[4]."',this.value)\">";
			print gui_select($row_2[0], "templates.php?sid=$row_2[5]&amp;gid=$gid&amp;row_set=$row_2[3]&amp;column_set=$row_2[4]&amp;mode=change");
			if ( $row_2[6] == 1) {
				$STATUS_HIDE = $MRTGMsg[73];
				$TITLE_HIDE = $MRTGMsg[178];
			} else {
				$STATUS_HIDE = $MRTGMsg[93];
				$TITLE_HIDE = $MRTGMsg[181];
			}
			print "</SELECT></td></tr><tr><td>$MRTGMsg[61]: $row_2[3] | $MRTGMsg[62]: $row_2[4] | <a href='' id='set_".$row_2[3]."_".$row_2[4]."' title='$MRTGMsg[177]'>$MRTGMsg[124]</a> | <a href='templates.php?sid=$row_2[5]&amp;hid=$row_2[0]&amp;gid=$gid&amp;mode=hide' title='$TITLE_HIDE'>$STATUS_HIDE</a> | <a href='templates.php?sid=$row_2[5]&amp;hid=$row_2[0]&amp;gid=$gid&amp;mode=delete' title='$MRTGMsg[179]'>$MRTGMsg[12]</a> | <a href='addrows.php?gid=$gid&amp;addrows=$row_2[3]' title='$MRTGMsg[125]'>+</a>";
			print "</td></tr></table></td>";
		} else {
			PrintTemplatesRow($i-1);
		}

	}

	PrintTemplatesRow($index_rows);

	print "</table></div><br>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id!=$GID_Trash order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id!=$GID_Trash order by id asc");
		$rows = pg_num_rows($result);
	}

	print "<table width='100%' cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width='4%'><b>$MRTGMsg[55]</b></td><td><b>$MRTGMsg[56]</b></td></tr>";

	if (!isset($p)) $p = 1;

	$pn=ceil($rows/20);
	$ps=($p-1)*20+1;
	$pe=$p*20;
	if ($pe > $rows) $pe = $rows;

	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if (($i >= $ps-1) && ($i < $pe)) {
			print "<tr align=center bgcolor='#F0F0F0'><td>".$row[0]."</td>";
			print "<td><a href='templates-gui.php?gid=$row[0]&amp;mode=view'>$row[1]</a></td></tr>";
		}
	}
	
	if ($rows == 0) {
		$ps = 0;
		print "<tr bgcolor='#F0F0F0'><td colspan=2 align=center class=red>$MRTGMsg[5]</td></tr>";
		print "</table>";
	} else {
		print "</table>";
		print "<table cellpadding=2 cellspacing=0 width='100%'><tr><td align=left><b>$MRTGMsg[6]:</b> [$ps - $pe] $MRTGMsg[7] $rows</td><td align=right><b>$MRTGMsg[8]: </b>";
		$ip=$p-1;
		$in=$p+1;
		if ($p > 1) print " <a href='templates-gui.php?p=$ip'><<</a>";
		for ($i=1; $i<=$pn; $i++) {
			if ($i == $p) print("<b> [$i]</b>");
			else print " <a href='templates-gui.php?p=$i'>[$i]</a>";
		}
		if ($p < $pn) print " <a href='templates-gui.php?p=$in'>>></a>";
		print "</td></tr></table>";
	}

	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>\n";
	print "<td><form method=post ACTION='index.php'><input type=submit class='submit_main_button' style='width:170px' value='$MRTGViewMsg[1]'></form></td>\n";
	print "</tr></table></div>\n";

} else {

}

HTMLBottomPrint();

?>
