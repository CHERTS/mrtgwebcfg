<?

require "config.php";
require "function.php";

$MRTGLang = ($MRTGAutoLanguage == '1') ? Get_Language() : $MRTGLanguage;
require "./lang/$MRTGLang.php";

if (Check_Access() != "Allow") MRTGErrors(6);

$self = $_SERVER['PHP_SELF'];

if ( System_Check() == 0 ) {

if($SQL_Type == "mysql") {
	$db = mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or MRTGErrors(3);
	$sdb = mysql_select_db($SQL_Base, $db) or MRTGErrors(3);
} else $db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or MRTGErrors(3);

if ($page == 'errors') HTMLTopPrint($MRTGViewMsg[2]);
else HTMLTopPrint();

print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[223]</b></td></tr>";

if ($page == 'errors') {
	$sql_table_cfg = "global_err";
	$MRTG_Stat_Patch = $MRTG_Stat_Patch_Err;
	print "<tr bgcolor='#F0F0F0' align=center><td class=blue><b>$MRTGViewMsg[2]</b></td></tr>";
} else {
	$sql_table_cfg = "global";
}

if (!(isset($id)) ) {

	if ($mode != 'view') {
		$gid = 0;
		$group_name = "Default";
	} else {
		if($SQL_Type == "mysql") {
			$result = mysql_query("select mrtg_group.title from mrtg_group where mrtg_group.id=$gid");
			$row = mysql_fetch_row($result);
		} else {
			$result = pg_query($db, "select	mrtg_group.title from mrtg_group where mrtg_group.id=$gid");
			$row = pg_fetch_row($result);
		}
		$group_name = $row[0];
	}

	if($SQL_Type == "mysql") {
		$result_max = mysql_query("select max(templates.row_set), max(templates.column_set)
					from agent, mrtg, mrtg_group, templates
					where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and templates.hide_set=1 and agent.trash=0 and mrtg_group.id=$gid");
		$row_max = mysql_fetch_row($result_max);
	} else {
		$result_max = pg_query($db, "select max(templates.row_set), max(templates.column_set)
					from agent, mrtg, mrtg_group, templates
					where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and templates.hide_set=1 and agent.trash=0 and mrtg_group.id=$gid");
		$row_max = pg_fetch_row($result_max);
	}
	$index_rows = $row_max[0];
	$index_column = $row_max[1];

	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[59] <font color='#0000FF'>$group_name</font></b></td></tr></table>\n";

	if($Auto_CHMOD == "1") {
        @chmod_R($CHMOD_Images_Dir);
		@chmod_R($CHMOD_Images_Dir_Err);
	}

	print "<br><div align=center><table cellpadding=5 cellspacing=1 width=100% bgcolor='#808080'>\n";

	for ($i=1; $i<=$index_rows; $i++) {

		if($SQL_Type == "mysql") {
			$result = mysql_query("select distinct agent.id, agent.title, mrtg.filename, templates.row_set, templates.column_set
							from agent, mrtg, mrtg_group, templates
							where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and templates.hide_set=1 and agent.trash=0 and mrtg_group.id=$gid and templates.row_set=$i
							order by templates.column_set asc");
			$rows = mysql_num_rows($result);

			$result_global = mysql_query("select imagedir from ".$sql_table_cfg);
			$row_global = mysql_fetch_row($result_global);
		} else {
			$result = pg_query($db, "select distinct agent.id, agent.title, mrtg.filename, templates.row_set, templates.column_set
							from agent, mrtg, mrtg_group, templates
							where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=mrtg.id and templates.hide_set=1 and agent.trash=0 and mrtg_group.id=$gid and templates.row_set=$i
							order by templates.column_set asc");
			$rows = pg_num_rows($result);

			$result_global = pg_query($db, "select imagedir	from ".$sql_table_cfg);
			$row_global = pg_fetch_row($result_global);
		}

		if ( $rows == 1 ) {

			$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, 0);

			$file = $row_global[0]."/".$row[2]."-day.gif";

			if ( file_exists($file) ) $fdate = time()-filectime($file);
			else $fdate = "0";

			CreateImg($row[0], "day", $sql_table_cfg);

			if ($row[4] == 1) {
				if ( file_exists($file) ) $href = "<a href='$self?gid=$gid&mode=view&id=$row[0]&page=$page'><img src='$MRTG_Stat_Patch$row[2]-day.gif' border=0 alt='$MRTGMsg[183] $fdate $MRTGMsg[184]'></a>";
				else $href = "<font color='#FF0000'><b>$MRTGMsg[113]</b></font>";
				print "<tr bgcolor='#F0F0F0'><td width=50%><table cellpadding=4 cellspacing=0 width=100%><tr><b>$row[1]</b></tr><tr>$href</tr></table></td><td width=50%></td></tr>\n";
			} else {
				if ( file_exists($file) ) $href = "<a href='$self?gid=$gid&mode=view&id=$row[0]&page=$page'><img src='$MRTG_Stat_Patch$row[2]-day.gif' border=0 alt='$MRTGMsg[183] $fdate $MRTGMsg[184]'></a>";
				else $href = "<font color='#FF0000'><b>$MRTGMsg[113]</b></font>";
				print "<tr bgcolor='#F0F0F0'><td width=50%></td><td width=50%><table cellpadding=4 cellspacing=0 width=100%><tr><b>$row[1]</b></tr><tr>$href</tr></table></td></tr>\n";
			}

		} elseif ( $rows == 2 ) {

			if($SQL_Type == "mysql") {
				$row_1 = mysql_fetch_row($result);
				$row_2 = mysql_fetch_row($result);
			} else {
				$row_1 = pg_fetch_row($result, 0);
				$row_2 = pg_fetch_row($result, 1);
			}

			$file_1 = $row_global[0]."/".$row_1[2]."-day.gif";
			$file_2 = $row_global[0]."/".$row_2[2]."-day.gif";

			if ( file_exists($file_1) ) $fdate_1 = time()-filectime($file_1);
			else $fdate_1 = "0";
			if ( file_exists($file_2) ) $fdate_2 = time()-filectime($file_2);
			else $fdate_2 = "0";

			CreateImg($row_1[0], "day", $sql_table_cfg);
			CreateImg($row_2[0], "day", $sql_table_cfg);

			if ( file_exists($file_1) ) $href_1 = "<a href='$self?gid=$gid&mode=view&id=$row_1[0]&page=$page'><img src='$MRTG_Stat_Patch$row_1[2]-day.gif' border=0 alt='$MRTGMsg[183] $fdate_1 $MRTGMsg[184]'></a>";
			else $href_1 = "<font color='#FF0000'><b>$MRTGMsg[113]</b></font>";
			if ( file_exists($file_2) ) $href_2 = "<a href='$self?gid=$gid&mode=view&id=$row_2[0]&page=$page'><img src='$MRTG_Stat_Patch$row_2[2]-day.gif' border=0 alt='$MRTGMsg[183] $fdate_2 $MRTGMsg[184]'></a>";
			else $href_2 = "<font color='#FF0000'><b>$MRTGMsg[113]</b></font>";

		        print "<tr bgcolor='#F0F0F0'><td width=50%>
			<table cellpadding=4 cellspacing=0 width=100%><tr><b>$row_1[1]</b></tr>
			<tr>$href_1</tr></table>
			</td><td width=50%>
			<table cellpadding=0 cellspacing=0 width=100%><tr><b>$row_2[1]</b></tr>
			<tr>$href_2</tr></table>
			</td></tr>\n";
		}

	}

	print "</table><br>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id!=$GID_Trash order by id asc");
		$rows = mysql_num_rows($result);
	} else {
		$result = pg_query($db, "select mrtg_group.id,mrtg_group.title from mrtg_group where mrtg_group.id!=$GID_Trash order by id asc");
		$rows = pg_num_rows($result);
	}
	print "<table width=100% cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=50><b>$MRTGMsg[55]</b></td><td><b>$MRTGMsg[56]</b></td></tr>";

	if (!isset($p)) $p = 1;

	$pn=ceil($rows/10);
	$ps=($p-1)*10+1;
	$pe=$p*10;
	if ($pe > $rows) $pe = $rows;

	for ($i=0; $i<$rows; $i++) {
		$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
		if (($i >= $ps-1) && ($i < $pe)) {
			print "<tr align=center bgcolor='#F0F0F0'><td>".$row[0]."</td>";
			print "<td><a href='$self?gid=$row[0]&mode=view&page=$page'>$row[1]</a></td></tr>";
		}
	}
	
	if ($rows == 0) {
		$ps = 0;
		print "<tr bgcolor='#F0F0F0'><td colspan=2 align=center class=red>$MRTGMsg[5]</td></tr>";
		print "</table>";
	} else {
		print "</table>";
		print "<table cellpadding=2 cellspacing=0 width=100%><tr><td align=left><b>$MRTGMsg[6]:</b> [$ps - $pe] $MRTGMsg[7] $rows</td><td align=right><b>$MRTGMsg[8]: </b>";
		$ip=$p-1;
		$in=$p+1;
		if ($p > 1) print " <a href='$self?p=$ip'><<</a>";
		for ($i=1; $i<=$pn; $i++) {
			if ($i == $p) print("<b> [$i]</b>");
			else print " <a href='$self?p=$i'>[$i]</a>";
		}
		if ($p < $pn) print " <a href='$self?p=$in'>>></a>";
		print "</td></tr></table>";
	}

	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>\n";
	if($page == "errors") print "<td><form method=post ACTION='$self?gid=$gid&mode=view'><input type=submit value='$MRTGMsg[24]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:170px'></form></td>\n";
	else print "<td><form method=post ACTION='$self?gid=$gid&mode=view&page=errors'><input type=submit value='$MRTGViewMsg[2]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:180px'></form></td>\n";
	print "<td><form method=post ACTION='admin/index.php'><input type=submit value='$MRTGViewMsg[1]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:170px'></form></td>\n";
	print "</tr></table></div>\n";

} else {

	if($SQL_Type == "mysql") {
		$result = mysql_query("select agent_ip.ip, agent_ip.title, agent.title, mrtg.iftype, mrtg.interface_name, mrtg.maxbytes, mrtg.title_ip, mrtg.options
						from agent,agent_ip,mrtg
						where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.id=".$id);
		$row = mysql_fetch_row($result);
	} else {
		$result = pg_query($db, "select agent_ip.ip, agent_ip.title, agent.title, mrtg.iftype, mrtg.interface_name, mrtg.maxbytes, mrtg.title_ip, mrtg.options
						from agent,agent_ip,mrtg
						where agent.id=mrtg.id and agent.ip=agent_ip.id and agent.id=".$id);
		$row = pg_fetch_row($result);
	}

	if( $row[7] != "" ) {
		$col = split(",", $row[7]);
		$cnt = count($col);
		$growright_set = 0;
		for($ii=0; $ii<$cnt; $ii++) {
			$col[$ii] = trim($col[$ii], "\x00..\x20");
			$col[$ii] = ltrim($col[$ii], "\x00..\x20");
			if( $col[$ii] == "growright" && $growright_set == 0) $growright_set = 1;
		}
	}

	print "<tr bgcolor='#F0F0F0' align=center><td class=red><b><font color='#0000FF'>$row[2]</font></b></td></tr></table>\n";
	if($growright_set == 0) {
		$ip = split("/", $row[0]);
		print "<br><table width=700 cellpadding=2 cellspacing=1 bgcolor='#808080'>";
		print "<tr bgcolor='#F0F0F0'><td width=30%><b>&nbsp;$MRTGMsg[1]</b></td><td>&nbsp;".$ip[0]." - ".$row[1]."</td></tr>";
		print "<tr bgcolor='#F0F0F0'><td width=30%><b>&nbsp;$MRTGMsg[2]</b></td><td>&nbsp;".$row[2]."</td></tr>";
		if($row[3]) print "<tr bgcolor='#F0F0F0'><td width=30%><b>&nbsp;$MRTGMsg[147]</b></td><td>&nbsp;".$row[3]."</td></tr>";
		if($row[4]) print "<tr bgcolor='#F0F0F0'><td width=30%><b>&nbsp;$MRTGMsg[148]</b></td><td>&nbsp;".$row[4]."</td></tr>";
		if($row[5]) {
			if ($row[5] < 1024) $row[5] = $row[5]." Bytes/s";
			elseif ($row[5] >= 1024 && $row[5] < 1048576) $row[5] = number_format($row[5]/1024, 2)." KBytes/s";
			else $row[5] = number_format($row[5]/1048576, 2)." MBytes/s";
			print "<tr bgcolor='#F0F0F0'><td width=30%><b>&nbsp;$MRTGMsg[149]</b></td><td>&nbsp;".$row[5]."</td></tr>";
		}
		if($row[6]) print "<tr bgcolor='#F0F0F0'><td width=30%><b>&nbsp;$MRTGMsg[150]</b></td><td>&nbsp;".$row[6]."</td></tr>";
	}
	print "</table><br>";

	if($SQL_Type == "mysql") {
		$result = mysql_query("select mrtg.filename from mrtg where mrtg.id=".$id);
		$row = mysql_fetch_row($result);
		$result_global = mysql_query("select workdir, imagedir from ".$sql_table_cfg);
		$row_global = mysql_fetch_row($result_global);
	} else {
		$result = pg_query($db, "select mrtg.filename from mrtg where mrtg.id=".$id);
		$row = pg_fetch_row($result);
		$result_global = pg_query($db, "select workdir, imagedir from ".$sql_table_cfg);
		$row_global = pg_fetch_row($result_global);

	}

	if($growright_set == 0) print "<hr>";
	$file_rrd = $row_global[0]."/".$row[0].".rrd";
	if ( file_exists($file_rrd) ) print "$MRTGMsg[151]: <b>".formatDateString(filectime($file_rrd))."</b><hr>";
	else print "<br>";

	print "<b>$MRTGMsg[152] $MRTGMsg[156] ($MRTGMsg[157] 5 $MRTGMsg[158])</b><br>";
	CreateImg($id, "day", $sql_table_cfg);
	$file = $row_global[1]."/".$row[0]."-day.gif";
	if ( file_exists($file) ) {
		print "<br><img src='$MRTG_Stat_Patch$row[0]-day.gif' border=0 alt='$row[0]'><br><br><hr>";
	} else print "<br><font color='#FF0000'><b>$MRTGMsg[113]</b></font><br><br><hr>";

	print "<b>$MRTGMsg[153] $MRTGMsg[156] ($MRTGMsg[157] 30 $MRTGMsg[158])</b><br>";
	CreateImg($id, "week", $sql_table_cfg);
	$file = $row_global[1]."/".$row[0]."-week.gif";
	if ( file_exists($file) ) {
		print "<br><img src='$MRTG_Stat_Patch$row[0]-week.gif' border=0 alt='$row[0]'><br><br><hr>";
	} else print "<br><font color='#FF0000'><b>$MRTGMsg[113]</b></font><br><br><hr>";

	print "<b>$MRTGMsg[154] $MRTGMsg[156] ($MRTGMsg[157] 2 $MRTGMsg[159])</b><br>";
	CreateImg($id, "month", $sql_table_cfg);
	$file = $row_global[1]."/".$row[0]."-month.gif";
	if ( file_exists($file) ) {
		print "<br><img src='$MRTG_Stat_Patch$row[0]-month.gif' border=0 alt='$row[0]'><br><br><hr>";
	} else print "<br><font color='#FF0000'><b>$MRTGMsg[113]</b></font><br><br><hr>";

	print "<b>$MRTGMsg[155] $MRTGMsg[156] ($MRTGMsg[157] 1 $MRTGMsg[156])</b><br>";
	CreateImg($id, "year", $sql_table_cfg);
	$file = $row_global[1]."/".$row[0]."-year.gif";
	if ( file_exists($file) ) {
		print "<br><img src='$MRTG_Stat_Patch$row[0]-year.gif' border=0 alt='$row[0]'><br><br><hr>";
	} else print "<br><font color='#FF0000'><b>$MRTGMsg[113]</b></font><br><br><hr>";

	PrintColorComment($id);

	print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>\n";
	print "<td><form method=post ACTION='$self?gid=$gid&mode=view'><input type=submit value='$MRTGMsg[24]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:170px'></form></td>\n";
	print "<td><form method=post ACTION='admin/index.php'><input type=submit value='$MRTGViewMsg[1]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:170px'></form></td>\n";
	print "</tr></table></div>\n";

}

}

HTMLBottomPrint();

?>
