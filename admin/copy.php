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

HTMLTopPrint($MRTGMsg[115]);

$self = $_SERVER['PHP_SELF'];

if ( isset($id) && $id != '' && !(isset($clone)) ) {

	// Запрос на ID и на новый ID
	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id from agent order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent.id from agent order by id asc");
		$rows = pg_num_rows($result);
	}
	$ids = 0;
	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if ( $i != $row[0] && $ids == 0) {
			$new_id = $i;
			$ids = 1;
		}
	}
	if ($ids == 0) $new_id = $rows;
	// End

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[115] <font color='#0000FF'>$id -> $new_id</font></b></td></tr></table>";

	print "<br><div align=center>
		<table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'>
		<tr bgcolor='#F0F0F0' align=center>
		<td class=red><br><b>$MRTGMsg[116] <font color='#0000FF'>$id</font> ?</b><br><br>";
	print "<form method=post ACTION='copy.php'><input type=hidden name=id value='$id'><input type=hidden name=new_id value='$new_id'><input type=hidden name=clone value='set'><input type=submit class='submit_button' value='$MRTGMsg[118]'></form>";
	print "</td></tr></table></div>";

} elseif ( isset($id) && $id != '' && isset($clone) && $clone != '' ) {

	print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
	print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[115] <font color='#0000FF'>$id -> $new_id</font></b></td></tr></table>";

	// Запрос на ID
	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id from agent order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select agent.id from agent order by id asc");
		$rows = pg_num_rows($result);
	}
	$ids = 0;
	for ($i=0; $i<$rows; $i++) {
		$roww = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if ( $i != $roww[0] && $ids == 0) {
			$new_id_confirm = $i;
			$ids = 1;
		}
	}
	if ($ids == 0) $new_id_confirm = $rows;
	// Конец запроса

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent.id,agent.title,agent.ip,agent.ver_snmp,mrtg.filename,mrtg.target,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0 and agent.id=$id order by id asc");
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select agent.id,agent.title,agent.ip,agent.ver_snmp,mrtg.filename,mrtg.target,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop from agent,agent_ip,mrtg where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.trash=0 and agent.id=$id order by id asc");
		$row = pg_fetch_row($result);
	}

	if ( $new_id == $new_id_confirm ) {

		if ( $p == '') $p = 1;

		if($SQL_Type == "mysql") {
			$result_agent = @mysql_query("insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$new_id.",'".$row[2]."','".$row[1]." (clone)','".$row[3]."',0,0)");
			$result_mrtg = @mysql_query("insert into mrtg (id, filename, target, maxbytes, routeruptime, routername, ipv4only, absmax, unscaled, withpeak, suppress, xsize, ysize, xzoom, yzoom, xscale, yscale, ytics, yticsfactor, factor, step, options, kmg, colours, ylegend, shortlegend, legend1, legend2, legend3, legend4, legendi, legendo, timezone, weekformat, rrdrowcount, timestrpos, timestrfmt, kilo, rrdrowcount30m, rrdrowcount2h, rrdrowcount1d, rrdhwrras, sfilename, setenv, pagetop) values(".$new_id.",'".$row[4]."','".$row[5]."','".$row[6]."','".$row[7]."','".$row[8]."','".$row[9]."','".$row[10]."','".$row[11]."','".$row[12]."','".$row[13]."','".$row[14]."','".$row[15]."','".$row[16]."','".$row[17]."','".$row[18]."','".$row[19]."','".$row[20]."','".$row[21]."','".$row[22]."','".$row[23]."','".$row[24]."','".$row[25]."','".$row[26]."','".$row[27]."','".$row[28]."','".$row[29]."','".$row[30]."','".$row[31]."','".$row[32]."','".$row[33]."','".$row[34]."','".$row[35]."','".$row[36]."','".$row[37]."','".$row[38]."','".$row[39]."','".$row[40]."','".$row[41]."','".$row[42]."','".$row[43]."','".$row[44]."','".$row[45]."','".$row[46]."','".$row[47]."')");
		}else{
			$result_agent = @pg_query($db, "insert into agent (id,ip,title,ver_snmp,trash,errors) values(".$new_id.",'".$row[2]."','".$row[1]." (clone)','".$row[3]."',0,0)");
			$result_mrtg = @pg_query($db, "insert into mrtg (id, filename, target, maxbytes, routeruptime, routername, ipv4only, absmax, unscaled, withpeak, suppress, xsize, ysize, xzoom, yzoom, xscale, yscale, ytics, yticsfactor, factor, step, options, kmg, colours, ylegend, shortlegend, legend1, legend2, legend3, legend4, legendi, legendo, timezone, weekformat, rrdrowcount, timestrpos, timestrfmt, kilo, rrdrowcount30m, rrdrowcount2h, rrdrowcount1d, rrdhwrras, sfilename, setenv, pagetop) values(".$new_id.",'".$row[4]."','".$row[5]."','".$row[6]."','".$row[7]."','".$row[8]."','".$row[9]."','".$row[10]."','".$row[11]."','".$row[12]."','".$row[13]."','".$row[14]."','".$row[15]."','".$row[16]."','".$row[17]."','".$row[18]."','".$row[19]."','".$row[20]."','".$row[21]."','".$row[22]."','".$row[23]."','".$row[24]."','".$row[25]."','".$row[26]."','".$row[27]."','".$row[28]."','".$row[29]."','".$row[30]."','".$row[31]."','".$row[32]."','".$row[33]."','".$row[34]."','".$row[35]."','".$row[36]."','".$row[37]."','".$row[38]."','".$row[39]."','".$row[40]."','".$row[41]."','".$row[42]."','".$row[43]."','".$row[44]."','".$row[45]."','".$row[46]."','".$row[47]."')");
		}

		if ($result_agent && $result_mrtg) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[47] $new_id $MRTGMsg[51]<br><br>";
			print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
			HTMLBottomPrint();
			exit;
		} else {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[50] $new_id</font></b><br>";
			if(!$result_agent) {
				print "<br><b>$MRTGMsg[278] agent</b><br>";
				if($result_mrtg) {
					if($SQL_Type == "mysql") $result = @mysql_query("delete from mrtg where id =".$new_id);
					else $result = @pg_query($db, "delete from mrtg where id=".$new_id);
				}
			}
			if(!$result_mrtg) {
				print "<br><b>$MRTGMsg[278] mrtg</b><br>";
				if($result_agent) {
					if($SQL_Type == "mysql") $result = @mysql_query("delete from agent where id =".$new_id);
					else $result = @pg_query($db, "delete from agent where id=".$new_id);
				}
			}
			print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
			print "</td></tr></table></div>";
			HTMLBottomPrint();
			exit;
		}

	} else {
		print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[119]</b><br>";
		print "<br><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
		print "</td></tr></table></div>";
		HTMLBottomPrint();
		exit;
	}

} else {
	print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>Error</b><br>";
	print "<br><form method=post ACTION='index.php'><input type=submit class='submit_button' value='$MRTGMsg[24]'></form>";
	print "</td></tr></table></div>";
}

HTMLBottomPrint();

?>