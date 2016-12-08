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

HTMLTopPrint($MRTGMsg[134]);

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[134]</b></td></tr></table>";

if ( $gid != '') {
	$Set_Errors = 0;
	if($SQL_Type == "mysql") {
		if( $Mode_Add_Rows == "0" )  $result = mysql_query("select templates.id, templates.agent_id, templates.row_set, templates.column_set from mrtg_group,templates where mrtg_group.id=templates.group_id and templates.group_id = $gid and mrtg_group.id != $GID_Trash and templates.row_set != 0 and templates.row_set >= $delrows order by templates.row_set,templates.column_set asc");
		else $result = mysql_query("select templates.id, templates.agent_id, templates.row_set, templates.column_set from mrtg_group,templates where mrtg_group.id=templates.group_id and templates.group_id = $gid and mrtg_group.id != $GID_Trash and templates.row_set != 0 and templates.row_set > $delrows order by templates.row_set,templates.column_set asc");
		$rows = mysql_num_rows($result);
	}else{
		if( $Mode_Add_Rows == "0" )  $result = pg_query($db, "select templates.id, templates.agent_id, templates.row_set, templates.column_set from mrtg_group,templates where mrtg_group.id=templates.group_id and templates.group_id = $gid and mrtg_group.id != $GID_Trash and templates.row_set != 0 and templates.row_set >= $delrows order by templates.row_set,templates.column_set asc");
		else $result = pg_query($db, "select templates.id, templates.agent_id, templates.row_set, templates.column_set from mrtg_group,templates where mrtg_group.id=templates.group_id and templates.group_id = $gid and mrtg_group.id != $GID_Trash and templates.row_set != 0 and templates.row_set > $delrows order by templates.row_set,templates.column_set asc");
		$rows = pg_num_rows($result);
	}

	if($Debug_Mode == '1') {
		print "<br><table width='100%' align=center cellpadding=2 cellspacing=1 bgcolor='#808080'>";
		print "<tr bgcolor='#AABBCC' align=center><td width=50%><b>$MRTGMsg[127]</b></td><td width=50%><b>$MRTGMsg[128]</b></td></tr>";
	}

	for($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		$row_set = $row[2]-1;
		$update_result = ($SQL_Type == "mysql") ? @mysql_query("update templates set row_set='".$row_set."' where id='".$row[0]."'") : @pg_query($db, "update templates set row_set='".$row_set."' where id='".$row[0]."'");
		if($update_result) {
			if($Debug_Mode == '1') print "<tr align=center bgcolor='#F0F0F0'><td>$row[2] <b>-></b> $row_set</td><td>$MRTGMsg[129]</td></tr>";
		} else {
			$Set_Errors = 1;
			if($Debug_Mode == '1') print "<tr align=center bgcolor='#F0F0F0'><td>$row[2] <b>-></b> $row_set</td><td><font color='#FF0000'>$MRTGMsg[130]</font></td></tr>";
		}
	}
	print "</table>";
	if ( $Set_Errors != '1' ) {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[135]</b><br>";
		print "<form method=post ACTION='templates-gui.php'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[121]'></form>";
		print "</td></tr></table></div>";
		exit;
	} else {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[136]</b><br>";
		print "<form method=post ACTION='templates-gui.php'><input type=submit class='submit_button' style='width:200px' value='$MRTGMsg[121]'></form>";
		print "</td></tr></table></div>";
		exit;
	}
}

HTMLBottomPrint();

?>
