<?

// Russian: ���� ������������ MRTGWebCfg ��� ��������� ����� MRTG
// English: Template for MRTGWebCfg (MRTG config file is generated from it)

// Russian: ��������� ����� Display Errors �� �����
// English: Supress Display Errors
ini_set('display_errors','off');

// Russian: ��������� ������� � ���� ������
// English: Database settings
$SQL_Type = "mysql"; 		// mysql or postgres
$SQL_Host = "localhost";
$SQL_Port = "3306";		// 3306 or 5432
$SQL_Base = "mrtg";
$SQL_User = "mrtg";
$SQL_Passwd = "12345";

// Russian: URL �� ����� ��� ��������� �������� ��� MRTG
// English: root MRTG images URL
$MRTG_Stat_Patch = "images/";

// Russian: URL �� ����� ��� ��������� �������� ��� MRTG Errors
// English: root MRTG Errors images URL
$MRTG_Stat_Patch_Err = "images-err/";

// Russian: ���� ���������� ��������� ����
// English: Where temporarily file is generated to
$TMP_MRTG_CFG_File = "/tmp/mrtg.cfg";

// Russian: ������ ������� ������ ������ � ���� �������� �����
// English: Source configuration file
$MRTG_CFG_File = "/usr/local/etc/mrtg/mrtg.cfg";

// Russian: ���� ������� ������ ������
// English: Destination configuration file
$BACKUP_MRTG_CFG_File = "/usr/local/etc/mrtg/backup/";

// Russian: ������ �������� ��������
// Russian: ���������� � ��������� �����
// Russian: �� ������������� !!!
// English: Settings array
// English: Array is used for Host view
// English: Do not edit !!!
$Settings = array('filename','target','interface_ip','interface_name','maxbytes','iftype','title_ip','absmax','withpeak','options','colours','ylegend','shortlegend','legend1','legend2','legend3','legend4','legendi','legendo','routeruptime','kmg','unscaled');

// Russian: ������ ������ �������� ��������
// Russian: ���������� � ����������/������������/�������������� �����
// Russian: �� ������������� !!!
// English: Full array of settings
// English: Array is used for Host Add/Clone/Edit procedures
// English: Do not edit !!!
$Full_Settings = array('id','ip','title','ver_snmp','filename','target','interface_ip','interface_name','maxbytes','iftype','title_ip','absmax','withpeak','options','colours','ylegend','shortlegend','legend1','legend2','legend3','legend4','legendi','legendo','routeruptime','kmg','unscaled');

// Russian: 0 - ��������� ��������� HID � GID � ���������� ������ Default
// Russian: 1 - ��������� ��������� HID � GID � ���������� ������ Default
// English: 0 - Disallow HID and GID change for members of Default group
// English: 1 - Allow HID and GID change for members of Default group
$SET_Access_Default_Group_Edit = "1";

// Russian: 0 - ��������� �������� ���������� �� ������ Default
// Russian: 1 - ��������� �������� ���������� �� ������ Default
// English: 0 - Disallow Default members deleting
// English: 1 - Allow Default members deleting
$SET_Access_Default_Group_Delete = "1";

// Russian: ������ "�������" (Ÿ ������� ������, ��� �� ��� � ������ "Default")
// Russian: ��� �������� ������� �� ��������� � ��� ������ �
// Russian: ���� agent.trash ��������� �������� �� 0 -> $GID_Trash
// Russian: � ���� $MRTG_CFG_File ������� ������ �� ������� � ������� agent.trash == 0
// Russian: �������� �������� ������� �� ���� ���������� ����� ��� �������� �� "�������"
// Russian: ���� ��������� agent.trash �� $GID_Trash -> 0 ���������� �������������� �������
// English: "Trash" group (may not be deleted, just like "Default" group)
// English: When client is deleted it is placed to this group
// English: and agent.trash field is set to 0 -> $GID_Trash
// English: Only those clients having agent.trash == 0 are put into $MRTG_CFG_File file
// English: Actual deletion of the client is performed after client is removed from "Trash" group
// English: If agent.trash filed is changed $GID_Trash -> 0 than client is undeleted
$GID_Trash = "100";

// Russian: ����� ������� (0 - ����., 1 - ���.)
// English: Debug mode (0 - Off., 1 - On.)
$Debug_Mode = "0";

// Russian: ����� ���������� ����� � Templates GUI
// Russian: 0 - ��������� ������ ����� ������������
// Russian: 1 - ��������� ������ ����� ������������
// English: Templates GUI lines adding mode
// English: 0 - insert a line before current line
// English: 1 - insert a line after current line
$Mode_Add_Rows = "0";

// Russian: ������� ���� ������ � �������� ����������������� ����� ���������� ������
// Russian: ���� �� �������� !!!
// English: Only mentioned login is allowed to access administration page
// English: Not implemented yet !!!
$Close_Admin_Pages = "Admin";

// Russian: ��������� ���������� ����� ������������ MRTG
// Russian: 1 - ���������
// Russian: 0 - ���������
// English: Disallow MRTG config rebuild
// English: 1 - Disallow
// English: 0 - Allow
$Deny_ReBuild_MRTG_File = "0";

// Russian: � ����� IP � �������� �������� ������
// English: IP subnets allowed to access MRTGWebCfg
$Allow_Subnet = array('All');
$Allow_IPAddress = array('All');

// Russian: ���� �������
// Russian: 0 - ������ ��������� ����� �� ������ $MRTGLanguage
// Russian: 1 - ����
// English: System language
// English: 0 - Manual setting based on $MRTGLanguage
// English: 1 - Auto mode
$MRTGAutoLanguage = "1";
// russian, english, french
$MRTGLanguage = "russian";

// Russian: ���������� SNMP ������ �� �������� ����������
// Russian: 0 - �� ����������
// Russian: 1 - ����������
// English: To show or not to show SNMP password on Control Panel
// English: 0 - not to show
// English: 1 - to show
$Show_Community = "0";

// Russian: ������������� ����� �� ����� ��������.
// Russian: �������� ��������������� � 1 ���� ����������� �������� � ������������ ��������.
// Russian: 0 - �� ������������� �����
// Russian: 1 - ������������� �����
// English: 
// English: 
// English: 
$Auto_CHMOD = "1";

// Russian: �������� ���� ������������ �������� � �� ������� ����� �������� �����.
// English: 
$CHMOD_Images_Dir = "/home/www/mrtg/images";
$CHMOD_Images_Dir_Err = "/home/www/mrtg/images-err";

// Russian: ��������� ��������� ����� ������������ MRTGWebCfg
// Russian: 1 - ���������
// Russian: 0 - ���������
// English: Disallow MRTGWebCfg config file save
// English: 1 - Disallow
// English: 0 - Allow
$Deny_Save_Config_File = "0";

?>
