<?

function CheckFileName ($filename) {

	require "config.php";

	global $MRTGLang;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	if($SQL_Type == "mysql") {
		$db = @mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or MRTGErrors(3);
		$sdb = @mysql_select_db($SQL_Base, $db) or MRTGErrors(3);
		$result = mysql_query("select agent.id, mrtg.filename from agent,mrtg where agent.id=mrtg.id and mrtg.filename='".$filename."' order by agent.id asc");
		$rows = mysql_num_rows($result);
	} else {
		$db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or MRTGErrors(3);
		$result = pg_query($db, "select agent.id, mrtg.filename from agent,mrtg where agent.id=mrtg.id and mrtg.filename='".$filename."' order by agent.id asc");
		$rows = pg_num_rows($result);
	}
	if( $rows == 0 ) return 0;
	else return 1;

}

function PrintTemplatesRow ($rows_next) {

	global $MRTGLang, $result_agent, $rows_agent, $gid, $index_rows;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	require "config.php";

	// Генерируем произвольный ID позиции.
	$rand_id = rand(0, 1000);
	
	// Пишем последнюю строчку выбора
	print "<tr bgcolor='#F0F0F0'>\n";
	for ($x=1; $x<3; $x++) {
		print "<td width=50%><table cellpadding=4 cellspacing=0 width=100%><tr><b>$MRTGMsg[122]</b></tr><tr>\n";
		print "<SELECT name='set_".$rand_id."_".$x."' onchange=\"change('set_end_".$rand_id."_".$x."',this.value)\" style='color:black;border:1x solid black;background-color:#FFFFFF;font-size:12px;width: 100%'>";
		print "<option selected value=''> </option>\n";
		print gui_select("-1", "templates.php?&gid=$gid&row_set=".($rows_next+1)."&column_set=$x&mode=add");
		print "</SELECT></tr><tr>$MRTGMsg[61]: ".($rows_next+1)." | $MRTGMsg[62]: $x | <a href='' id='set_end_".$rand_id."_".$x."' title='$MRTGMsg[180]'>$MRTGMsg[123]</a>";
		if( $index_rows != $rows_next ) print " | <a href='addrows.php?gid=$gid&addrows=".($rows_next+1)."' title='$MRTGMsg[125]'>+</a> | <a href='delrows.php?gid=$gid&delrows=".($rows_next+1)."' title='$MRTGMsg[133]'>-</a>";
		print "</tr></table></td>\n";
	}
	print "</tr>\n";
	// Конец

}

function CreateImg ($id, $mode, $Global_Table = "global") {

	require "config.php";

	if($SQL_Type == "mysql") {
		$db = @mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or exit('Function CreateImg -> Unable to connect to SQL server');
		$sdb = @mysql_select_db($SQL_Base, $db) or exit('Function CreateImg -> Unable to select database');
		$result = mysql_query("select mrtg.filename from mrtg where mrtg.id=".$id);
		$row = mysql_fetch_row($result);
		$result_global = mysql_query("select workdir, imagedir from ".$Global_Table);
		$row_global = mysql_fetch_row($result_global);
	} else {
		$db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or exit('Function CreateImg -> Unable to connect to SQL server');
		$result = pg_query($db, "select mrtg.filename from mrtg where mrtg.id=".$id);
		$row = pg_fetch_row($result);
		$result_global = pg_query($db, "select workdir, imagedir from ".$Global_Table);
		$row_global = pg_fetch_row($result_global);
	}

	$file = $row_global[1]."/".$row[0]."-".$mode.".gif";
	$file_rrd = $row_global[0]."/".$row[0].".rrd";

	if ( file_exists($file) ) $ftime = time()-filectime($file);
	else $ftime = "";

	if( !file_exists($file) || $ftime < "30" ) {

		$opts_1 = $opts_2 = $opts_3 = $opts_4 = array();

		if($SQL_Type == "mysql") {
			$result_all = mysql_query("select mrtg.filename, mrtg.target, mrtg.interface_ip, mrtg.interface_name, mrtg.maxbytes, mrtg.iftype, mrtg.title_ip, mrtg.absmax, mrtg.withpeak, mrtg.options, mrtg.colours, mrtg.ylegend, mrtg.shortlegend, mrtg.legend1, mrtg.legend2, mrtg.legend3, mrtg.legend4, mrtg.legendi, mrtg.legendo from mrtg where id=".$id);
			$row_all = mysql_fetch_row($result_all);
		} else {
			$result_all = pg_query($db, "select mrtg.filename, mrtg.target, mrtg.interface_ip, mrtg.interface_name, mrtg.maxbytes, mrtg.iftype, mrtg.title_ip, mrtg.absmax, mrtg.withpeak, mrtg.options, mrtg.colours, mrtg.ylegend, mrtg.shortlegend, mrtg.legend1, mrtg.legend2, mrtg.legend3, mrtg.legend4, mrtg.legendi, mrtg.legendo from mrtg where id=".$id);
			$row_all = pg_fetch_row($result_all);
		}

		if( $row_all[8] != "" ) {
			$opts_3 = array("LINE1:maxin#006600:MaxIn",
					"LINE1:maxout#ff00ff:MaxOut");
		}

		if( $row_all[9] != "" ) {
			$col = split(",", $row_all[9]);
			$cnt = count($col);
			$growright_set = 0;
			for($ii=0; $ii<$cnt; $ii++) {
				$col[$ii] = trim($col[$ii], "\x00..\x20");
				$col[$ii] = ltrim($col[$ii], "\x00..\x20");
				if( $col[$ii] == "growright" && $growright_set == 0) {
					$growright_set = 1;
					$maxbytes = $row_all[4];
					$opts_1 = array("DEF:in=$file_rrd:ds0:AVERAGE",
							"DEF:maxin=$file_rrd:ds0:MAX",
							"DEF:out=$file_rrd:ds1:AVERAGE",
							"DEF:maxout=$file_rrd:ds1:MAX",
							"-u 100");
				} elseif($growright_set == 0) {
					$maxbytes = $row_all[4]*8;
					$opts_1 = array("DEF:in0=$file_rrd:ds0:AVERAGE",
							"CDEF:in=in0,8,*",
							"DEF:maxin0=$file_rrd:ds0:MAX",
							"CDEF:maxin=maxin0,8,*",
							"DEF:out0=$file_rrd:ds1:AVERAGE",
							"CDEF:out=out0,8,*",
							"DEF:maxout0=$file_rrd:ds1:MAX",
							"CDEF:maxout=maxout0,8,*");
				}
			}
		} else {
			$opts_1 = array("DEF:in0=$file_rrd:ds0:AVERAGE",
					"CDEF:in=in0,8,*",
					"DEF:maxin0=$file_rrd:ds0:MAX",
					"CDEF:maxin=maxin0,8,*",
					"DEF:out0=$file_rrd:ds1:AVERAGE",
					"CDEF:out=out0,8,*",
					"DEF:maxout0=$file_rrd:ds1:MAX",
					"CDEF:maxout=maxout0,8,*");
			$maxbytes = $row_all[4]*8;
		}

		if( $row_all[10] != "" ) {
			$col = split(",", $row_all[10]);
			$cnt = count($col);
			for($ii=0; $ii<$cnt; $ii++) {
				$value = split("#", $col[$ii]);
				$value_1 = split("#", $col[0]);
				$value_2 = split("#", $col[1]);
			}
			$opts_4 = array("AREA:in#$value_1[1]:In",
					"LINE1:out#$value_2[1]:Out");
		} else {
			$opts_4 = array("AREA:in#00cc00:In",
					"LINE1:out#0000ff:Out");
		}

		if( $row_all[11] != "" ) $ycomment = $row_all[11];
		else $ycomment = "Bits per second";

		$seconds = time();
		if($mode == "week") {
			$oldsec = $seconds-7*86400;
			$param_0 = "-691200";
		} elseif($mode == "month") {
			$oldsec = $seconds-30*86400;
			$param_0 = "-3110400";
		} elseif($mode == "year") {
			$oldsec = $seconds-365*86400;
			$param_0 = "-34214400";
		} else {
			$oldsec = $seconds-86400;
			$param_0 = "-108000";
			$opts_2 = array("-x","HOUR:1:HOUR:6:HOUR:2:0:%-H");
		}

		$opts_final = array( "--start", "$param_0",
				"--lazy",
				"-c", "FONT#000000",
				"-c", "MGRID#000000",
				"-c", "FRAME#000000",
				"-g", "-l 0",
				"-c", "BACK#f5f5f5",
				"-c", "ARROW#000000",
				"-b 1000",
				"-w 400",
				"-h 100",
				"-v $ycomment",
				"PRINT:out:MAX:%.1lf",
				"PRINT:in:MAX:%.1lf",
				"PRINT:out:AVERAGE:%.1lf",
				"PRINT:in:AVERAGE:%.1lf",
				"PRINT:out:LAST:%.1lf",
				"PRINT:in:LAST:%.1lf",
				"HRULE:$maxbytes#cc0000",
				"VRULE:$oldsec#ff0000",
				"VRULE:$seconds#ff0000"
		                );

		$opts = array_merge($opts_1, $opts_2, $opts_final, $opts_4, $opts_3);

		$ret = rrd_graph($file, $opts, count($opts));

		if ( is_array($ret) ) {
			return 0;
		} else {
			$err = rrd_error();
			return 1;
		}
	} else {
		return 1;
	}

}

function formatDateString($stamp) {

	global $MRTGLang;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	$monn = date("n", $stamp);
	$printime = date("d", $stamp)." ".$MRTGViewMon[$monn]." ".date("Y", $stamp)." ".date("H:i:s", $stamp);
	return $printime;

} 

function PrintColorComment($id) {

	global $MRTGLang;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	require "config.php";

	if($SQL_Type == "mysql") {
		$db = @mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or exit('Function PrintColorComment -> Unable to connect to SQL server');
		$sdb = @mysql_select_db($SQL_Base, $db) or exit('Function CreateImg -> Unable to select database');
		$result_all = mysql_query("select mrtg.filename, mrtg.target, mrtg.interface_ip, mrtg.interface_name, mrtg.maxbytes, mrtg.iftype, mrtg.title_ip, mrtg.absmax, mrtg.withpeak, mrtg.options, mrtg.colours, mrtg.ylegend, mrtg.shortlegend, mrtg.legend1, mrtg.legend2, mrtg.legend3, mrtg.legend4, mrtg.legendi, mrtg.legendo from mrtg where id=".$id);
		$row_all = mysql_fetch_row($result_all);
	} else {
		$db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or exit('Function PrintColorComment -> Unable to connect to SQL server');
		$result_all = pg_query($db, "select mrtg.filename, mrtg.target, mrtg.interface_ip, mrtg.interface_name, mrtg.maxbytes, mrtg.iftype, mrtg.title_ip, mrtg.absmax, mrtg.withpeak, mrtg.options, mrtg.colours, mrtg.ylegend, mrtg.shortlegend, mrtg.legend1, mrtg.legend2, mrtg.legend3, mrtg.legend4, mrtg.legendi, mrtg.legendo from mrtg where id=".$id);
		$row_all = pg_fetch_row($result_all);
	}

	if( $row_all[10] != "" ) {

		$col = split(",", $row_all[10]);
		$cnt = count($col);
		print "<BR><TABLE WIDTH=500 BORDER=0 CELLPADDING=4 CELLSPACING=0>";
		for($ii=0; $ii<$cnt; $ii++) {
			$value = split("#", $col[$ii]);
			$col_legend = $row_all[13+$ii];
			if( $col_legend != "" ) {
				print "<TR><TD ALIGN=RIGHT><FONT SIZE=-1 COLOR='$value[1]'><B>$value[0]</B></FONT></TD><TD><FONT SIZE=-1>$col_legend</FONT></TD></TR>";
			} else {
				if( $ii == 0 ) print "<TR><TD ALIGN=RIGHT><FONT SIZE=-1 COLOR='#00cc00'><B>$MRTGMsg[161]</B></FONT></TD><TD><FONT SIZE=-1>$MRTGMsg[163]</FONT></TD></TR>";
				elseif( $ii == 1 ) print "<TR><TD ALIGN=RIGHT><FONT SIZE=-1 COLOR='#0000ff'><B>$MRTGMsg[162]</B></FONT></TD><TD><FONT SIZE=-1>$MRTGMsg[164]</FONT></TD></TR>";
			}
		}
		print "</TABLE><BR><HR><BR>";

	} else {
		if( $row_all[13] != "" ) {
			print "<BR><TABLE WIDTH=500 BORDER=0 CELLPADDING=4 CELLSPACING=0>
			<TR><TD ALIGN=RIGHT><FONT SIZE=-1 COLOR='#00cc00'><B>$MRTGMsg[161]</B></FONT></TD><TD><FONT SIZE=-1>$row_all[13]</FONT></TD></TR>";
		} else {
			print "<BR><TABLE WIDTH=500 BORDER=0 CELLPADDING=4 CELLSPACING=0>
			<TR><TD ALIGN=RIGHT><FONT SIZE=-1 COLOR='#00cc00'><B>$MRTGMsg[161]</B></FONT></TD><TD><FONT SIZE=-1>$MRTGMsg[163]</FONT></TD></TR>";
		}
		if( $row_all[14] != "" ) {
			print "<TR><TD ALIGN=RIGHT><FONT SIZE=-1 COLOR='#0000ff'><B>$MRTGMsg[162]</B></FONT></TD><TD><FONT SIZE=-1>$row_all[14]</FONT></TD></TR>
			</TABLE><BR><HR><BR>";
		} else {
			print "<TR><TD ALIGN=RIGHT><FONT SIZE=-1 COLOR='#0000ff'><B>$MRTGMsg[162]</B></FONT></TD><TD><FONT SIZE=-1>$MRTGMsg[164]</FONT></TD></TR>
			</TABLE><BR><HR><BR>";
		}
	}

}

function Check_Access () {

	global $Allow_Subnet,$Allow_IPAddress,$access;

	$raddress = split("\.", getenv(REMOTE_ADDR));
	$subnet = $raddress[0].".".$raddress[1].".".$raddress[2];
	$cnt_deny_subnet = $cnt_deny_ip = $cnt_allow_subnet = $cnt_allow_ip = 0;

	for ($i=0; $i<count($Allow_Subnet); $i++) {
		if( (substr($Allow_Subnet[$i], 0, -(count($raddress[3])+1)) == $subnet) || $Allow_Subnet[$i] == 'All') $cnt_allow_subnet++;
		else $cnt_deny_subnet++;
	}

	for ($i=0; $i<count($Allow_IPAddress); $i++) {
		if( (getenv(REMOTE_ADDR) == $Allow_IPAddress[$i]) || $Allow_IPAddress[$i] == 'All') $cnt_allow_ip++;
		else $cnt_deny_ip++;
	}

	if ( $cnt_allow_ip > 0 || $cnt_allow_subnet > 0) $access = "Allow";
	else $access = "Deny";

	return $access;

}

function MRTGErrors ($Err) {

	global $MRTGLang;
	require "config.php";

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	HTMLTopPrint($MRTGErrorMsg[0]);

	$IP = $_SERVER['REMOTE_ADDR'];
	$self = $_SERVER['PHP_SELF'];

	print "<br><div align=center><table width=50% cellpadding=4 cellspacing=2 bgcolor='#808080'>
		<tr bgcolor='#F0F0F0' align='center'><td class=red><br><font size='3'><b>$MRTGErrorMsg[$Err]";
	if( $Err == "6" ) print " <font color='#0000FF'>$IP</font> $MRTGErrorMsg[7]";
	print "</b></font><br><br><form method=post ACTION='$self'><input type=submit value='$MRTGMsg[24]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px'></form></td></tr></table></div>";
	exit();


	print "</BODY></HTML>";

}

function Get_Language() {

	require "config.php";

	$lngSignatures = array('ru', 'en','fr');
	$langs = split(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	foreach ($langs as $lang) {
		$lang = substr($lang, 0, 2);
		if (in_array($lang, $lngSignatures)) {
			if ($lang == 'ru') return 'russian';
			elseif ($lang == 'fr') return 'french';
			else return 'english';
		}
	}
	return $MRTGLanguage;

}

function HTMLTopPrint ($Title = "") {

	global $MRTGLang;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	require "config.php";

	if ( defined('IN_ADMIN') ) {
		print "<HTML><HEAD><META http-equiv='Content-Type' content='text/html; charset=windows-1251'>
			<LINK rel='stylesheet' href='./../style.css' type='text/css'>
			<script language='JavaScript' src='./../func.js'></script>
			<title>$MRTGMsg[222]";
	} else {
		print "<HTML><HEAD><META http-equiv='Content-Type' content='text/html; charset=windows-1251'>
			<LINK rel='stylesheet' href='style.css' type='text/css'>
			<script language='JavaScript' src='func.js'></script>
			<title>$MRTGMsg[0]";
	}
	if( $Title != '' ) print "- $Title";
	print "</title></HEAD><BODY bgcolor='#FFFFFF' text='#000000'>";

}

function HTMLBottomPrint () {

	global $MRTGLang;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	require "config.php";

	print "<br><div align=center><span class='copyright'>Powered by <a href='http://www.novell.chel.ru/' target='_userwww' class='copyright'>$MRTGConfVer</a> &copy; 2004 - 2008 <a href='mailto:sleuthhound@gmail.com' target='_top' class='copyright'>NVStat Team</a></span></div>";
	print "</body></html>";

}

// Проверяем настройки системы
function System_Check () {

        require "config.php";
	global $MRTGLang;
	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	$Function_List = array('gd','snmp','RRDTool','pcre');
	$Function_List_DB = array('mysql','pgsql');
	$PHP_Options_List = array('register_globals');
	$PHP_Options_List_Value = array('1');
	$PHP_Options_List_Value_Desc = array(''=>'Off', '0'=>'Off', '1'=>'On');
	$Err = 0;

	for( $i=0; $i < count($Function_List); $i++) {
		if (!extension_loaded($Function_List[$i])) $Err++;
	}

	if( $SQL_Type == "mysql" && !(extension_loaded($Function_List_DB[0])) ) {
		$Err++;
		$DB = $Function_List_DB[0];
	} elseif ( $SQL_Type == "postgres" && !(extension_loaded($Function_List_DB[1])) ) {
		$Err++;
		$DB = $Function_List_DB[1];
	} else {
	        $DB = $SQL_Type;
	}

	for( $i=0; $i < count($PHP_Options_List); $i++) {
		if ( ini_get($PHP_Options_List[$i]) != $PHP_Options_List_Value[$i] ) $Err++;
	}

	if( $Err != 0 ) {

		HTMLTopPrint();

		print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGConfVer</b></td></tr></table>";
		print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><b>$MRTGInstall[0]</b></td></tr></table>
			<br><table width=100% cellpadding=2 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40%><b>$MRTGInstall[1]</b></td><td><b>$MRTGInstall[2]</b></td></tr>";
		print "<tr bgcolor='#F0F0F0'><td align=center>$DB</td>";
		if (!extension_loaded($DB)) {
			$Err++;
			print "<td align=center class='red'>$MRTGInstall[4]</td></tr>";
		} else print "<td align=center class='blue_total'>$MRTGInstall[3]</td></tr>";
		for( $i=0; $i < count($Function_List); $i++) {
			$ver = explode( '.', PHP_VERSION );
			$ver_num = $ver[0];
			if( $Function_List[$i] == "RRDTool" ) print "<tr bgcolor='#F0F0F0'><td align=center>PHP$ver_num-$Function_List[$i]</td>";
			else print "<tr bgcolor='#F0F0F0'><td align=center>$Function_List[$i]</td>";
			if (!extension_loaded($Function_List[$i])) {
				$Err++;
				print "<td align=center class='red'>$MRTGInstall[4]</td></tr>";
			} else print "<td align=center class='blue_total'>$MRTGInstall[3]</td></tr>";
		}
		print "</table>";

		print "<br><br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'>";
		print "<tr bgcolor='#F0F0F0' align=center><td class=blue><b>$MRTGInstall[9]</b></td></tr></table>
			<br><table width=100% cellpadding=2 cellspacing=1 bgcolor='#808080'><tr bgcolor='#AABBCC' align=center><td width=40%><b>$MRTGInstall[10]</b></td><td><b>$MRTGInstall[11]</b></td></tr>";
		for( $i=0; $i < count($PHP_Options_List); $i++) {
			print "<tr bgcolor='#F0F0F0'><td align=center>$PHP_Options_List[$i]</td>";
			print "<td align=center><font color='#0000FF'>".$PHP_Options_List_Value_Desc[ini_get($PHP_Options_List[$i])]."</font> / <font color='#FF0000'>".$PHP_Options_List_Value_Desc[$PHP_Options_List_Value[$i]]."</font></td></tr>";
			$Err++;
		}
		print "</table>";

		print "<br><table width=100% cellpadding=2 cellspacing=1 bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td width=100% class=red><b>$MRTGInstall[100]</b></td></tr></table>";
		print "</BODY></HTML>";
	}

	return $Err;

}

function Check_SNMP () {

	global $MRTGLang;
	global $hid, $ip_address, $ver_snmp, $snmp_username, $snmp_passwd, $auth_snmp_protocol, $MRTGMsg;

        require "config.php";

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	if( $hid == '-2' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><br><b>$MRTGErrorMsg[0] !!!<br><br>$MRTGErrorMsg[8]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
	} elseif ( $hid == '-1' &&  $ip_address == '' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><br><b>$MRTGErrorMsg[0] !!!<br><br>$MRTGErrorMsg[9]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
	} elseif ( $hid == '-1' && $ver_snmp == '0' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><br><b>$MRTGErrorMsg[0] !!!<br><br>$MRTGErrorMsg[10]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
	} elseif ( $hid == '-1' && $ver_snmp == '-3' && $snmp_passwd == '' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><br><b>$MRTGErrorMsg[0] !!!<br><br>$MRTGErrorMsg[11]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
	} elseif ( $hid == '-1' && $ver_snmp == -4 && $snmp_username == '' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><br><b>$MRTGErrorMsg[0] !!!<br><br>$MRTGErrorMsg[12]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
	} elseif ( $hid == '-1' && $ver_snmp == '-5' && ( $snmp_username == '' || $snmp_passwd == '' ) ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><br><b>$MRTGErrorMsg[0] !!!<br><br>$MRTGErrorMsg[13]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
	} elseif ( $hid == '-1' && $ver_snmp == '-5' && $auth_snmp_protocol == '0' ) {
			print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><br><b>$MRTGErrorMsg[0] !!!<br><br>$MRTGErrorMsg[14]</b><br>";
			print "<br><form method=post ACTION='snmp.php'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
			print "</td></tr></table></div>";
			exit;
	} 

}

function gui_select ($id_select, $value_set) {

        require "config.php";
	global $MRTGLang;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

	if($SQL_Type == "mysql") {
		$db = @mysql_connect($SQL_Host, $SQL_User, $SQL_Passwd) or MRTGErrors(3);
		$sdb = mysql_select_db($SQL_Base, $db) or MRTGErrors(3);
		$result_agent = mysql_query("select agent.id, agent.title from agent order by agent.id asc");
		$rows_agent = mysql_num_rows($result_agent);	
	} else {
		$db = @pg_connect('host='.$SQL_Host.' port='.$SQL_Port.' dbname='.$SQL_Base.' user='.$SQL_User.' password='.$SQL_Passwd.'') or MRTGErrors(3);
		$result_agent = pg_query($db, "select agent.id, agent.title from agent order by agent.id asc");
		$rows_agent = pg_num_rows($result_agent);	
	}

	for ($i=0; $i<$rows_agent; $i++) {
		$row_agent = ($SQL_Type == "mysql") ? mysql_fetch_row($result_agent) : pg_fetch_row($result_agent, $i);
		$row_agent_array[] = $row_agent;
	}

	$select = '';
	while (list($displayname, $details) = @each($row_agent_array))
	{
		$selected = ($id_select == $displayname) ? ' selected="selected"' : '';
		$select .= '<option value="' . $value_set . '&hid=' . $displayname .'&newhid=' . $displayname .'"' . $selected . '>' . $displayname . ' - ' . $details[1] . '</option>';
	}
	//$select .= '</select>';

	return $select;

}

function VersionCheck() {

        require "config.php";
	global $MRTGLang;

	if ( defined('IN_ADMIN') ) require "./../lang/$MRTGLang.php";
	else require "./lang/$MRTGLang.php";

/*
	// Table names
	define('CONFIG_TABLE', 'config');

	$sql = "SELECT *
		FROM " . CONFIG_TABLE;
	if( !($result = $db->sql_query($sql)) ) {
		//message_die(CRITICAL_ERROR, "Could not query config information", "", __LINE__, __FILE__, $sql);
	}

	while ( $row = $db->sql_fetchrow($result) ) {
		$framework_config[$row['config_name']] = $row['config_value'];
	}
*/
	// Check for new version
	//$current_version = explode('.', $framework_config['version']);
	$version = "2.20";
	$current_version = explode('.', $version);
	$minor_revision = (int) $current_version[1];

	$errno = 0;
	$errstr = $version_info = '';

	if ($fsock = @fsockopen('www.novell.chel.ru', 80, $errno, $errstr, 10)) {

		@fputs($fsock, "GET /Project/MRTGWebCfg/Updatecheck/21x.txt HTTP/1.1\r\n");
		@fputs($fsock, "HOST: www.novell.chel.ru\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");

		$get_info = false;
		while (!@feof($fsock)) {
			if ($get_info) {
				$version_info .= @fread($fsock, 1024);
			} else {
				if (@fgets($fsock, 1024) == "\r\n") {
					$get_info = true;
				}
			}
		}
		@fclose($fsock);

		$version_info = explode("\n", $version_info);
		$latest_head_revision = (int) $version_info[0];
		$latest_minor_revision = (int) $version_info[1];
		$latest_version = (int) $version_info[0] . '.' . (int) $version_info[1];

		if ($latest_head_revision == 2 && $minor_revision == $latest_minor_revision) {
			$version_info = '<p style="color:green">' . $lang['Version_up_to_date'] . '</p>';
		} else {
			$version_info = '<p style="color:blue">' . $lang['Version_not_up_to_date'];
			$version_info .= '<br />' . sprintf($lang['Latest_version_info'], $latest_version) . ' ' . sprintf($lang['Current_version_info'], $version) . '</p>';
		}
	} else {
		if ($errstr) {
			$version_info = '<p style="color:red">' . sprintf($lang['Connect_socket_error'], $errstr) . '</p>';
		} else {
			$version_info = '<p>' . $lang['Socket_functions_disabled'] . '</p>';
		}
	}
	

	return $version_info;

}

function chmod_R($path) {

    if (!is_dir($path))
        return chmod($path, 0777);

    $dh = opendir($path);
    while ($file = readdir($dh)) {
        if($file != '.' && $file != '..') {
            $fullpath = $path.'/'.$file;
            if(is_link($fullpath))
                return FALSE;
            elseif(!is_dir($fullpath))
            if (!chmod($fullpath, 0777))
                return FALSE;
            elseif(!chmod_R($fullpath, 0777))
                return FALSE;
        }
    }
    closedir($dh);

    if(chmod($path, 0777))
        return TRUE;
    else
        return FALSE;

}

?>
