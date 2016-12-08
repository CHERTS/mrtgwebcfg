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

HTMLTopPrint($MRTGMsg[14]);

$self = $_SERVER['PHP_SELF'];
$p = $_GET['p'];

if ( $p == '') $p = 1;

if ( isset($mode) && $mode == "delete" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[25]</b></td></tr></table><br>";

	if ( !(isset($confirm_delete)) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[26] <font color='#0000FF'>$id</font>?</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION='$self'><input type=hidden name=p value='$p'><input type=hidden name=mode value='delete'><input type=hidden name=confirm_delete value='set'><input type=hidden name=id value=$id><input type=hidden name=gid value=$gid><input type=submit class='submit_button' value='$MRTGMsg[21]'></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[22]'></form>";
		print "</td></tr></table></td></tr></table></div>";
	} elseif (isset($confirm_delete)) {
		$records = array("id" => $id);
		if($SQL_Type == "mysql") $result = @mysql_query("delete from agent where id =".$id);
		else $result = @pg_delete($db, 'agent', $records);
		//$result = pg_query($db, "delete from agent where id=".$id);
		if ($result) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[28] <font color='#0000FF'>$id</font> $MRTGMsg[29]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=hidden name=mode value='view'><input type=hidden name=gid value=$gid><input type=submit class='submit_button' style='width:150px' value='$MRTGMsg[54]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[30] $id</b><br>";
			print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
		}
	}

} elseif ( isset($mode) && $mode == "restore" ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[107]</b></td></tr></table><br>";

	if ( !(isset($confirm_restore)) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[108] <font color='#0000FF'>$id</font> $MRTGMsg[109]</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION='delete.php'><input type=hidden name=p value='$p'><input type=hidden name=mode value='restore'><input type=hidden name=confirm_restore value='set'><input type=hidden name=id value=$id><input type=hidden name=gid value=$gid><input type=submit class='submit_button' value='$MRTGMsg[21]'></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[22]'></form>";
		print "</td></tr></table></td></tr></table></div>";
	} elseif ( isset($confirm_restore) && $confirm_restore == "set" ) {
		if($SQL_Type == "mysql") $result = @mysql_query("update agent set trash='0' where id='".$id."'");
		else $result = @pg_query($db, "update agent set trash='0' where id='".$id."'");
		if ($result) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[110] <font color='#0000FF'>$id</font> $MRTGMsg[111]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=hidden name=mode value='view'><input type=hidden name=gid value=$gid><input type=submit class='submit_button' style='width:150px' value='$MRTGMsg[54]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[112] $id $MRTGMsg[218]</b><br>";
			print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
		}
	}

} else {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[102]</b></td></tr></table><br>";

	if ( !(isset($confirm_trash)) ) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[103] <font color='#0000FF'>$id</font> $MRTGMsg[104]</b><br><br>";
		print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
		print "<form method=post ACTION='delete.php'><input type=hidden name=p value='$p'><input type=hidden name=confirm_trash value='set'><input type=hidden name=id value=$id><input type=submit class='submit_button' value='$MRTGMsg[21]'></form>";
		print "</td><td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[22]'></form>";
		print "</td></tr></table></td></tr></table></div>";
	} elseif ( isset($confirm_trash) && $confirm_trash == "set" ) {
		if($SQL_Type == "mysql") $result = @mysql_query("update agent set trash='".$GID_Trash."' where id='".$id."'");
		else $result = @pg_query($db, "update agent set trash='".$GID_Trash."' where id='".$id."'");
		if ($result) {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[105] <font color='#0000FF'>$id</font> $MRTGMsg[106]</b><br><br>";
			print "<table cellpadding=2 cellspacing=2><tr align=center><td>";
			print "<form method=post ACTION='group.php'><input type=hidden name=group value='set'><input type=submit class='submit_button' style='width:150px' value='$MRTGMsg[54]'></form>";
			print "</td><td><form method=post ACTION='index.php'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></td></tr></table></div>";
		} else {
			print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[30] $id</b><br>";
			print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
		}
	}

}

HTMLBottomPrint();

?>