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

HTMLTopPrint($MRTGMsg[15]);

$mode = $_GET['mode'];
$p = $_GET['p'];

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[15]</b></td></tr></table><br>";
if( $mode == "delete" ) print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[165]</b></td></tr></table><br>";
print "<table width='100%' cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40><b>$MRTGMsg[67]</b></td><td width=110><b>$MRTGMsg[1]</b></td><td><b>$MRTGMsg[2]</b></td><td width=110><b>$MRTGMsg[3]</b></td><td width=110><b>$MRTGMsg[4]</b></td><td width=100><b>$MRTGMsg[168]</b></td><td width=150><b>$MRTGMsg[9]</b></td></tr>";

if($SQL_Type == "mysql") {
	$result = mysql_query("select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community,agent.errors from agent,agent_ip where agent.ip=agent_ip.id and agent.id=".$id);
	$row = mysql_fetch_row($result);
} else {
	$result = pg_query($db, "select agent.id,agent_ip.ip,agent.title,agent.ver_snmp,agent_ip.community,agent.errors from agent,agent_ip where agent.ip=agent_ip.id and agent.id=".$id);
	$row = pg_fetch_row($result);
}

$hid = $row[0];
if ( $p == '') $p = 1;

print "<tr align=center bgcolor='#F0F0F0'><td>".$row[0]."</td>";
$ip = split("/", $row[1]);
print "<td>".$ip[0]."</td>";
print "<td align=left>&nbsp;".$row[2]."</td>";
if($row[3] == "0") print "<td>No SNMP</td>";
else print "<td>SNMP v".$row[3]."</td>";
if( $Show_Community == "0" )print "<td>*****</td>";
else print "<td>".$row[4]."</td>";
print "<td width=100><a href='templates.php?id=$row[0]&amp;mode=errors'>";
if($row[5] == "0") print $MRTGMsg[170];
else print $MRTGMsg[169];
print "</a></td>";
print "<td width=150><a href='edit.php?id=$row[0]&amp;p=$p'>$MRTGMsg[11]</a> | <a href='copy.php?id=$row[0]&amp;p=$p'>$MRTGMsg[114]</a> | <a href='delete.php?id=$row[0]&amp;p=$p";
if( $mode == "delete" ) print "&amp;mode=delete";
print "'>$MRTGMsg[12]</a></td>";
print "</table><br>";

print "<table width='100%' cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td><b>$MRTGMsg[276]</b></td></tr>";

if($SQL_Type == "mysql") {
	$result = mysql_query("select mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop from mrtg where id=".$id);
	$row_all = mysql_fetch_row($result);
} else {
	$result = pg_query($db, "select mrtg.target,mrtg.filename,mrtg.maxbytes,mrtg.routeruptime,mrtg.routername,mrtg.ipv4only,mrtg.absmax,mrtg.unscaled,mrtg.withpeak,mrtg.suppress,mrtg.xsize,mrtg.ysize,mrtg.xzoom,mrtg.yzoom,mrtg.xscale,mrtg.yscale,mrtg.ytics,mrtg.yticsfactor,mrtg.factor,mrtg.step,mrtg.options,mrtg.kmg,mrtg.colours,mrtg.ylegend,mrtg.shortlegend,mrtg.legend1,mrtg.legend2,mrtg.legend3,mrtg.legend4,mrtg.legendi,mrtg.legendo,mrtg.timezone,mrtg.weekformat,mrtg.rrdrowcount,mrtg.timestrpos,mrtg.timestrfmt,mrtg.kilo,mrtg.rrdrowcount30m,mrtg.rrdrowcount2h,mrtg.rrdrowcount1d,mrtg.rrdhwrras,mrtg.sfilename,mrtg.setenv,mrtg.pagetop from mrtg where id=".$id);
	$row_all = pg_fetch_row($result);
}

if ($row_all == 0) {
	print "<tr bgcolor='#F0F0F0'><td colspan=2 align=center class=red>$MRTGMsg[5]</td></tr>";
	print "</table>";
}

$MRTG_Settings = array('MaxBytes','RouterUptime','RouterName','IPv4Only','AbsMax','Unscaled','WithPeak','Suppress','XSize','YSize','XZoom','YZoom','XScale','YScale','YTics','YTicsFactor','Factor','Step','Options','kMG','Colours','YLegend','ShortLegend','Legend1','Legend2','Legend3','Legend4','LegendI','LegendO','Timezone','Weekformat','RRDRowCount','TimeStrPos','TimeStrFmt','kilo','RRDRowCount30m','RRDRowCount2h','RRDRowCount1d','RRDHWRRAs','TargetS','SetEnv','PageTop');

if($Show_Community == "0") $snmp_community = "*****";
else $snmp_community = $row[4];

if($row[3] == "0") $full_target = $row_all[41];
else if($row[3] == "1") $full_target = $row_all[0].":".$snmp_community."@".$ip[0];
else $full_target = $row_all[0].":".$snmp_community."@".$ip[0].":::::2";

print "<tr align=left bgcolor='#F0F0F0'><td class='mrtgtextarea'><br>";
print "&nbsp;&nbsp;Target[".$row_all[1]."]: ".$full_target."<br>";
print "&nbsp;&nbsp;Title[".$row_all[1]."]: ".$row[2]."<br>";

for ($z=2; $z<count($row_all); $z++ ) {
	if ( $row_all[$z] != "" ) {
		if($MRTG_Settings[$z-2] == "TargetS") echo '';
		else if($MRTG_Settings[$z-2] == "RouterUptime" && $row_all[$z] == "1") print "&nbsp;&nbsp;".$MRTG_Settings[$z-2]."[".$row_all[1]."]: ".$snmp_community."@".$ip[0]."<br>";
		else print "&nbsp;&nbsp;".$MRTG_Settings[$z-2]."[".$row_all[1]."]: ".$row_all[$z]."<br>";
	}
}
print "<br></td></tr></table><br>";


//Управление шаблоними

print "<table cellpadding=4 cellspacing=1 width='100%' bgcolor='#808080'><tr bgcolor='#F0F0F0'><td align=center width='100%' class=red><b>$MRTGMsg[66]</b></td></tr></table><br>";
print "<table width='100%' cellpadding=1 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40><b>$MRTGMsg[58]</b></td><td width=200><b>$MRTGMsg[59]</b></td><td><b>$MRTGMsg[60]</b></td><td width=70><b>$MRTGMsg[61]</b></td><td width=70><b>$MRTGMsg[62]</b></td><td width=150><b>$MRTGMsg[9]</b></td></tr>";

if($SQL_Type == "mysql") {
	$result = mysql_query("select distinct templates.id, mrtg_group.title, mrtg.filename, templates.row_set, templates.column_set, mrtg_group.id, templates.hide_set
					 from mrtg_group,templates,agent,mrtg
					 where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=".$id." and mrtg.id=".$id."
					 order by templates.id asc");
	$rows = mysql_num_rows($result);

} else {
	$result = pg_query($db, "select distinct templates.id, mrtg_group.title, mrtg.filename, templates.row_set, templates.column_set, mrtg_group.id, templates.hide_set
					 from mrtg_group,templates,agent,mrtg
					 where mrtg_group.id=templates.group_id and templates.agent_id=agent.id and templates.agent_id=".$id." and mrtg.id=".$id."
					 order by templates.id asc");
	$rows = pg_num_rows($result);


}

for($i = 0; $i < $rows; $i++ ) {
	$row = ($SQL_Type == "mysql") ? mysql_fetch_row($result) : pg_fetch_row($result, $i);
	print "<tr align=center bgcolor='#F0F0F0'>";
	for ($j=0; $j < count($row); $j++) {
		if ($j == 1) print "<td><a href='group.php?gid=$row[5]&amp;mode=view'>".$row[1]."</a></td>";
		elseif ($j == 2) print "<td>".$row[2].".rrd</td>";
		elseif ($j == 5) print "";
		elseif ($j == 6) {
			if ( $row[6] == 1) $STATUS_HIDE = $MRTGMsg[73];
			else $STATUS_HIDE = $MRTGMsg[93];
		}
		else print "<td>".$row[$j]."</td>";
	}
	print "<td><a href='templates.php?sid=$row[0]&amp;hid=$hid&amp;gid=$row[5]&amp;mode=edit&amp;p=$p'>$MRTGMsg[11]</a> | <a href='templates.php?sid=$row[0]&amp;hid=$hid&amp;gid=$row[5]&amp;mode=delete&amp;p=$p'>$MRTGMsg[12]</a> | <a href='templates.php?sid=$row[0]&amp;hid=$hid&amp;gid=$row[5]&amp;mode=hide&amp;p=$p'>$STATUS_HIDE</a></td>";
	print "</tr>";
}

if ($rows == 0) {
	print "<tr align=center bgcolor='#F0F0F0'><td colspan=5 class=red>$MRTGMsg[5]</td><td><a href='templates.php?hid=$hid&amp;mode=add&amp;p=$p' title='$MRTGMsg[182]'>$MRTGMsg[65]</a></td></tr>";
}

print "</table>";

print "<br><div align=center><table cellpadding=0 cellspacing=5><tr align=center>";
print "<td><form method=post ACTION='templates.php'><input type=hidden name=p value='$p'><input type=hidden name=mode value='add'><input type=hidden name=hid value='$hid'><input type=submit class='submit_main_button' style='width:200px' value='$MRTGMsg[79]'></form></td>";
print "<td><form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit class='submit_main_button' style='width:100px' value='$MRTGMsg[24]'></form></td></tr></table></div>";

HTMLBottomPrint();

?>