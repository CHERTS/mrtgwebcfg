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

HTMLTopPrint($MRTGMsg[36]);

$self = $_SERVER['PHP_SELF'];

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[36]</b></td></tr></table><br>";

if ( $p == '') $p = 1;

if (isset($config) && !(isset($save)) ) {

	if($SQL_Type == "mysql") {
		$result = mysql_query("select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global");
		$result_err = mysql_query("select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global_err");
		$row = mysql_fetch_row($result);
		$row_err = mysql_fetch_row($result_err);
	} else {
		$result = pg_query($db, "select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global");
		$result_err = pg_query($db, "select workdir, language, options, enableipv6, logformat, pathadd, libadd, imagedir, runasdaemon, intervals, nodetach from global_err");
		$row = pg_fetch_row($result);
		$row_err = pg_fetch_row($result_err);
	}

	print "<table align=center cellpadding=2 cellspacing=1 width=70% bgcolor='#808080'><form methode='post' action='$self'>";
	print "<tr bgcolor='#F0F0F0'><td colspan=2 align=center width='100%' class=red><font size='2'><b>MRTG</b></font></td></tr>";
	print "<tr bgcolor='#AABBCC' align=center><td width=30%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[37]."</td><td><input type='text' name='workdir_' value='$row[0]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[38]."</td><td><input type='text' name='language_' value='$row[1]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[39]."</td><td><input type='text' name='options_' value='$row[2]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[40]."</td><td><input type='text' name='enableipv6_' value='$row[3]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[143]."</td><td><input type='text' name='logformat_' value='$row[4]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[144]."</td><td><input type='text' name='pathadd_' value='$row[5]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[145]."</td><td><input type='text' name='libadd_' value='$row[6]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[146]."</td><td><input type='text' name='imagedir_' value='$row[7]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[219]."</td><td><input type='text' name='runasdaemon_' value='$row[8]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[220]."</td><td><input type='text' name='intervals_' value='$row[9]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[221]."</td><td><input type='text' name='nodetach_' value='$row[10]'></input></td></tr>";

	print "<tr bgcolor='#F0F0F0'><td colspan=2 align=center width='100%' class=red><font size='2'><b>MRTG  Errors</b></font></td></tr>";
	print "<tr bgcolor='#AABBCC' align=center><td width=30%><b>$MRTGMsg[41]</b></td><td><b>$MRTGMsg[42]</b></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[37]."</td><td><input type='text' name='workdir_err_' value='$row_err[0]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[38]."</td><td><input type='text' name='language_err_' value='$row_err[1]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[39]."</td><td><input type='text' name='options_err_' value='$row_err[2]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[40]."</td><td><input type='text' name='enableipv6_err_' value='$row_err[3]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[143]."</td><td><input type='text' name='logformat_err_' value='$row_err[4]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[144]."</td><td><input type='text' name='pathadd_err_' value='$row_err[5]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[145]."</td><td><input type='text' name='libadd_err_' value='$row_err[6]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[146]."</td><td><input type='text' name='imagedir_err_' value='$row_err[7]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[219]."</td><td><input type='text' name='runasdaemon_err_' value='$row_err[8]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[220]."</td><td><input type='text' name='intervals_err_' value='$row_err[9]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td>".$MRTGMsg[221]."</td><td><input type='text' name='nodetach_err_' value='$row_err[10]'></input></td></tr>";
	print "<tr align=center bgcolor='#F0F0F0'><td colspan=2><input type=hidden name=p value='$p'><input type=hidden name=config value='set'><input type=hidden name=save value='set'><input type='submit' name='submit' class='submit_button' value='$MRTGMsg[43]'></input></td></tr>";
	print "</table></form>";

	print "<div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
	print "<td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_main_button' value='$MRTGMsg[24]'></form></td></tr></table></div>";

} elseif (isset($config) && isset($save) ) {

	if($SQL_Type == "mysql") {
		$result = @mysql_query("update global set workdir='".$workdir_."',language='".$language_."',options='".$options_."',enableipv6='".$enableipv6_."',logformat='".$logformat_."',pathadd='".$pathadd_."',libadd='".$libadd_."',imagedir='".$imagedir_."',runasdaemon='".$runasdaemon_."',intervals='".$intervals_."',nodetach='".$nodetach_."'");
		$result_err = @mysql_query("update global_err set workdir='".$workdir_err_."',language='".$language_err_."',options='".$options_err_."',enableipv6='".$enableipv6_err_."',logformat='".$logformat_err_."',pathadd='".$pathadd_err_."',libadd='".$libadd_err_."',imagedir='".$imagedir_err_."',runasdaemon='".$runasdaemon_err_."',intervals='".$intervals_err_."',nodetach='".$nodetach_err_."'");
	} else { 
		$result = @pg_query($db, "update global set workdir='".$workdir_."',language='".$language_."',options='".$options_."',enableipv6='".$enableipv6_."',logformat='".$logformat_."',pathadd='".$pathadd_."',libadd='".$libadd_."',imagedir='".$imagedir_."',runasdaemon='".$runasdaemon_."',intervals='".$intervals_."',nodetach='".$nodetach_."'");
		$result_err = @pg_query($db, "update global_err set workdir='".$workdir_err_."',language='".$language_err_."',options='".$options_err_."',enableipv6='".$enableipv6_err_."',logformat='".$logformat_err_."',pathadd='".$pathadd_err_."',libadd='".$libadd_err_."',imagedir='".$imagedir_err_."',runasdaemon='".$runasdaemon_err_."',intervals='".$intervals_err_."',nodetach='".$nodetach_err_."'");
	}
	if ($result && $result_err) {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[44]</b><br>";
		print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
		print "</td></tr></table></div>";
		exit;
	} else {
		print "<div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[45]</b><br>";
		print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
		print "</td></tr></table></div>";
		exit;
	}
}

HTMLBottomPrint();

?>
