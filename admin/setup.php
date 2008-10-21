<?

define('IN_ADMIN', true);

require "./../config.php";
require "./../config-err.php";
require "./../function.php";

$MRTGLang = ($MRTGAutoLanguage == '1') ? Get_Language() : $MRTGLanguage;
require "./../lang/$MRTGLang.php";

if (Check_Access() != "Allow") MRTGErrors(6);

HTMLTopPrint($MRTGMsg[80]);

$self = $_SERVER['PHP_SELF'];

print "<table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[222]</b></td></tr>";
print "<tr bgcolor='#F0F0F0' align=center><td class=red><b>$MRTGMsg[224]</b></td></tr></table>";

if ( $p == '') $p = 1;

if($Deny_Save_Config_File == "1")  {
	print "<br><div align=center><table cellpadding=4 cellspacing=2 width=40% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=red><br><b>$MRTGMsg[268]</b><br>";
	print "<form method=post ACTION='index.php'><input type=hidden name=p value='$p'><input type=submit value=\"$MRTGMsg[24]\" style=\"color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px\"></form>";
	print "</td></tr></table></div>";
    HTMLBottomPrint();
    exit;
}

if ($update_okey == "yes") {

	$a_update_config = str_replace('"',"'",$a_update_config);
	$a_update_config  = "<?\r\n\n";
	$a_update_config .= "\$SQL_Type = \"$SQL_Type_\";\r\n";
	$a_update_config .= "\$SQL_Host = \"$SQL_Host_\";\r\n";
	$a_update_config .= "\$SQL_Port = \"$SQL_Port_\";\r\n";
	$a_update_config .= "\$SQL_Base = \"$SQL_Base_\";\r\n";
	$a_update_config .= "\$SQL_User = \"$SQL_User_\";\r\n";
	$a_update_config .= "\$SQL_Passwd = \"$SQL_Passwd_\";\r\n";
	$a_update_config .= "\$SQL_Base = \"$SQL_Base_\";\r\n";
	$a_update_config .= "\$MRTG_Stat_Patch = \"$MRTG_Stat_Patch_\";\r\n";
	$a_update_config .= "\$MRTG_Stat_Patch_Err = \"$MRTG_Stat_Patch_Err_\";\r\n";
	$a_update_config .= "\$TMP_MRTG_CFG_File = \"$TMP_MRTG_CFG_File_\";\r\n";
	$a_update_config .= "\$MRTG_CFG_File = \"$MRTG_CFG_File_\";\r\n";
	$a_update_config .= "\$BACKUP_MRTG_CFG_File = \"$BACKUP_MRTG_CFG_File_\";\r\n";
	$Settings_ = "array('" . str_replace(";", "','", $Settings_Text_) . "');";
	$a_update_config .= "\$Settings = $Settings_\r\n";
	$Full_Settings_ = "array('" . str_replace(";", "','", $Full_Settings_Text_) . "');";
	$a_update_config .= "\$Full_Settings = $Full_Settings_\r\n";
	$a_update_config .= "\$SET_Access_Default_Group_Edit = \"$SET_Access_Default_Group_Edit_\";\r\n";
	$a_update_config .= "\$SET_Access_Default_Group_Delete = \"$SET_Access_Default_Group_Delete_\";\r\n";
	$a_update_config .= "\$GID_Trash = \"$GID_Trash_\";\r\n";
	$a_update_config .= "\$Debug_Mode = \"$Debug_Mode_\";\r\n";
	$a_update_config .= "\$Mode_Add_Rows = \"$Mode_Add_Rows_\";\r\n";
	$a_update_config .= "\$Close_Admin_Pages = \"$Close_Admin_Pages_\";\r\n";
	$a_update_config .= "\$Deny_ReBuild_MRTG_File = \"$Deny_ReBuild_MRTG_File_\";\r\n";
	$Allow_Subnet_ = "array('" . str_replace(";", "','", $Allow_Subnet_Text_) . "');";
	$a_update_config .= "\$Allow_Subnet = $Allow_Subnet_\r\n";
	$Allow_IPAddress_ = "array('" . str_replace(";", "','", $Allow_IPAddress_Text_) . "');";
	$a_update_config .= "\$Allow_IPAddress = $Allow_IPAddress_\r\n";
	$a_update_config .= "\$MRTGAutoLanguage = \"$MRTGAutoLanguage_\";\r\n";
	$a_update_config .= "\$MRTGLanguage = \"$MRTGLanguage_\";\r\n";
	$a_update_config .= "\$Show_Community = \"$Show_Community_\";\r\n";
	$a_update_config .= "\$Auto_CHMOD = \"$Auto_CHMOD_\";\r\n";
	$a_update_config .= "\$CHMOD_Images_Dir = \"$CHMOD_Images_Dir_\";\r\n";
	$a_update_config .= "\$CHMOD_Images_Dir_Err = \"$CHMOD_Images_Dir_Err_\";\r\n";
	$a_update_config .= "\n?>";
	$u_update_config = fopen("./../config.php","w+");
	$a_update_config = str_replace('&lt;',"<",$a_update_config);
	$a_update_config = str_replace('&gt;',">",$a_update_config);
	fputs ($u_update_config,$a_update_config);

    print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><b>$MRTGMsg[260]</b></td></tr></table>";

}

if ($update_okey_err == "yes") {

	$a_update_config_err = str_replace('"',"'",$a_update_config_err);
	$a_update_config_err = "<?\r\n\n";
   	$a_update_config_err.= "\$TMP_MRTG_CFG_File_Err = \"$TMP_MRTG_CFG_File_Err_\";\r\n";
   	$a_update_config_err.= "\$MRTG_CFG_File_Err = \"$MRTG_CFG_File_Err_\";\r\n";
   	$a_update_config_err.= "\$BACKUP_MRTG_CFG_File_Err = \"$BACKUP_MRTG_CFG_File_Err_\";\r\n";
	$a_update_config_err.= "\n?>";
	$u_update_config_err = fopen("./../config-err.php","w+");
	$a_update_config_err = str_replace('&lt;',"<",$a_update_config_err);
	$a_update_config_err = str_replace('&gt;',">",$a_update_config_err);
	fputs ($u_update_config_err,$a_update_config_err);

    print "<br><table cellpadding=4 cellspacing=1 width=100% bgcolor='#808080'><tr bgcolor='#F0F0F0' align=center><td class=blue><b>$MRTGMsg[261]</b></td></tr></table>";

}

# ------------ config.php -----------------------

print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'>";
print "<form action=setup.php method=get><input type=hidden name=update_okey value=yes>";
print "<tr bgcolor='#F0F0F0' align=center><td colspan=3 class=red><b>$MRTGMsg[263]</b></td></tr></tr>";
print "<tr bgcolor='#AABBCC' align=center><td width=35%>$MRTGMsg[225]</td><td width=500px>$MRTGMsg[226]</td><td>$MRTGMsg[227]</td></tr></tr>";

for($i=0; $i<2; $i++) $SQL_Type_Set[$i]="";
if($SQL_Type=="mysql") { $SQL_Type_Set[0]="selected"; $SQL_Type_Set[1]=""; }
if($SQL_Type=="postgres") { $SQL_Type_Set[1]="selected"; $SQL_Type_Set[0]=""; }
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[228]:</b></td><td><SELECT class='setup' name=\"SQL_Type_\"><option $SQL_Type_Set[0] value=\"postgres\">PostgreSQL<option $MRTGAutoLanguage_Set[1] value=\"mysql\">MySQL</SELECT></td><td></td></tr>";

print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[229]:</b></td><td><input class='setup' type=text name=SQL_Host_ value=\"$SQL_Host\" size=70 ></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[230]:</b></td><td><input class='setup' type=text name=SQL_Port_ value=\"$SQL_Port\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[231]:</b></td><td><input class='setup' type=text name=SQL_Base_ value=\"$SQL_Base\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[232]:</b></td><td><input class='setup' type=text name=SQL_User_ value=\"$SQL_User\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[233]:</b></td><td><input class='setup' type=password name=SQL_Passwd_ value=\"$SQL_Passwd\" size=70></td><td></td></tr>";

print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[234]:</b></td><td><input class='setup' type=text name=MRTG_Stat_Patch_ value=\"$MRTG_Stat_Patch\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[235]:</b></td><td><input class='setup' type=text name=MRTG_Stat_Patch_Err_ value=\"$MRTG_Stat_Patch_Err\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[236]:</b></td><td><input class='setup' type=text name=TMP_MRTG_CFG_File_ value=\"$TMP_MRTG_CFG_File\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[237]:</b></td><td><input class='setup' type=text name=MRTG_CFG_File_ value=\"$MRTG_CFG_File\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[238]:</b></td><td><input class='setup' type=text name=BACKUP_MRTG_CFG_File_ value=\"$BACKUP_MRTG_CFG_File\" size=70></td><td></td></tr>";

$Settings_Text="";
for($i=0; $i<count($Settings); $i++) $Settings_Text = $Settings_Text.";".$Settings[$i];
$Settings_Text = ltrim($Settings_Text, ';');
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[239]:</b></td><td><input class='setup' type=text name=Settings_Text_ value=\"$Settings_Text\"></td><td>&nbsp;&nbsp;$MRTGMsg[257]<br>&nbsp;&nbsp;$MRTGMsg[262]</td></tr>";

$Full_Settings_Text="";
for($i=0; $i<count($Full_Settings); $i++) $Full_Settings_Text = $Full_Settings_Text.";".$Full_Settings[$i];
$Full_Settings_Text = ltrim($Full_Settings_Text, ';');
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[240]:</b></td><td><input class='setup' type=text name=Full_Settings_Text_ value=\"$Full_Settings_Text\"></td><td>&nbsp;&nbsp;$MRTGMsg[257]<br>&nbsp;&nbsp;$MRTGMsg[262]</td></tr>";

print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[241]:</b></td><td><input class='setup' type=text name=SET_Access_Default_Group_Edit_ value=\"$SET_Access_Default_Group_Edit\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[242]:</b></td><td><input class='setup' type=text name=SET_Access_Default_Group_Delete_ value=\"$SET_Access_Default_Group_Delete\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[243]:</b></td><td><input class='setup' type=text name=GID_Trash_ value=\"$GID_Trash\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[244]:</b></td><td><input class='setup' type=text name=Debug_Mode_ value=\"$Debug_Mode\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[245]:</b></td><td><input class='setup' type=text name=Mode_Add_Rows_ value=\"$Mode_Add_Rows\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[246]:</b></td><td><input class='setup' type=text name=Close_Admin_Pages_ value=\"$Close_Admin_Pages\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[247]:</b></td><td><input class='setup' type=text name=Deny_ReBuild_MRTG_File_ value=\"$Deny_ReBuild_MRTG_File\" size=70></td><td></td></tr>";

$Allow_Subnet_Text="";
for($i=0; $i<count($Allow_Subnet); $i++) $Allow_Subnet_Text = $Allow_Subnet_Text.";".$Allow_Subnet[$i];
$Allow_Subnet_Text = ltrim($Allow_Subnet_Text, ';');
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[248]:</b></td><td><input class='setup' type=text name=Allow_Subnet_Text_ value=\"$Allow_Subnet_Text\" size=70><td>&nbsp;&nbsp;$MRTGMsg[256]</td></tr>";

$Allow_IPAddress_Text="";
for($i=0; $i<count($Allow_IPAddress); $i++) $Allow_IPAddress_Text = $Allow_IPAddress_Text.";".$Allow_IPAddress[$i];
$Allow_IPAddress_Text = ltrim($Allow_IPAddress_Text, ';');
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[249]:</b></td><td><input class='setup' type=text name=Allow_IPAddress_Text_ value=\"$Allow_IPAddress_Text\" size=70><td>&nbsp;&nbsp;$MRTGMsg[256]</td></tr>";

for($i=0; $i<2; $i++) $MRTGAutoLanguage_Set[$i]="";
if($MRTGAutoLanguage=="0") { $MRTGAutoLanguage_Set[0]="selected"; $MRTGAutoLanguage_Set[1]=""; }
if($MRTGAutoLanguage=="1") { $MRTGAutoLanguage_Set[1]="selected"; $MRTGAutoLanguage_Set[0]=""; }
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[250]:</b></td><td><SELECT class='setup' name=\"MRTGAutoLanguage_\"><option $MRTGAutoLanguage_Set[0] value=\"0\">$MRTGMsg[259]<option $MRTGAutoLanguage_Set[1] value=\"1\">$MRTGMsg[258]</SELECT></td><td></td></tr>";

for($i=0; $i<3; $i++) $MRTGLanguage_Set[$i]="";
if($MRTGLanguage=="russian") { $MRTGLanguage_Set[0]="selected"; $MRTGLanguage_Set[1]=""; $MRTGLanguage_Set[2]=""; }
if($MRTGLanguage=="english") { $MRTGLanguage_Set[1]="selected"; $MRTGLanguage_Set[0]=""; $MRTGLanguage_Set[2]=""; }
if($MRTGLanguage=="french") { $MRTGLanguage_Set[2]="selected"; $MRTGLanguage_Set[0]=""; $MRTGLanguage_Set[1]=""; }
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[251]:</b></td><td><SELECT class='setup' name=\"MRTGLanguage_\"><option $MRTGLanguage_Set[0] value=\"russian\">Russian<option $MRTGLanguage_Set[1] value=\"english\">English<option $MRTGLanguage_Set[2] value=\"french\">French</SELECT></td><td></td></tr>";

print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[252]:</b></td><td><input class='setup' type=text name=Show_Community_ value=\"$Show_Community\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[253]:</b></td><td><input class='setup' type=text name=Auto_CHMOD_ value=\"$Auto_CHMOD\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[254]:</b></td><td><input class='setup' type=text name=CHMOD_Images_Dir_ value=\"$CHMOD_Images_Dir\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[255]:</b></td><td><input class='setup' type=text name=CHMOD_Images_Dir_Err_ value=\"$CHMOD_Images_Dir_Err\" size=70></td><td></td></tr>";

print "<tr bgcolor='#F0F0F0'><td colspan=3 align=center><input type=\"submit\" name=\"update\" value=$MRTGMsg[43] style=\"color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width:90px\"></td></tr></form></table>";

# ------------ config-err.php -----------------------

print "<br><table width=100% align=center cellpadding=2 cellspacing=1 bgcolor='#808080'>";
print "<form action=setup.php method=get><input type=hidden name=update_okey_err value=yes>";
print "<tr bgcolor='#F0F0F0' align=center><td colspan=3 class=red><b>$MRTGMsg[264]</b></td></tr></tr>";
print "<tr bgcolor='#AABBCC' align=center><td width=35%>$MRTGMsg[225]</td><td width=500px>$MRTGMsg[226]</td><td>$MRTGMsg[227]</td></tr></tr>";

print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[265]:</b></td><td><input class='setup' type=text name=TMP_MRTG_CFG_File_Err_ value=\"$TMP_MRTG_CFG_File_Err\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[266]:</b></td><td><input class='setup' type=text name=MRTG_CFG_File_Err_ value=\"$MRTG_CFG_File_Err\" size=70></td><td></td></tr>";
print "<tr bgcolor='#F0F0F0'><td align=right><b>$MRTGMsg[267]:</b></td><td><input class='setup' type=text name=BACKUP_MRTG_CFG_File_Err_ value=\"$BACKUP_MRTG_CFG_File_Err\" size=70></td><td></td></tr>";

print "<tr bgcolor='#F0F0F0'><td colspan=3 align=center><input type=\"submit\" name=\"update\" value=$MRTGMsg[43] style=\"color:blue;border:1x solid red;background-color:#EDEEEE;font-size:12px;width:90px\"></td></tr></form></table>";

# ---------------------------------------------------

print "<div align=center><form method=post ACTION='index.php'><input type=submit value='$MRTGMsg[24]' style='color:#0000FF;border:1x solid red;background-color:#EDEEEE;font-size:13px;width:100px'></form></div>";

HTMLBottomPrint();

?>
