<?

// Russian: Файл конфигурации MRTGWebCfg для генерации файла MRTG
// English: Template for MRTGWebCfg (MRTG config file is generated from it)

// Russian: Запрещаем вывод Display Errors на экран
// English: Supress Display Errors
ini_set('display_errors','off');

// Russian: Настройки доступа к базе данных
// English: Database settings
$SQL_Type = "mysql"; 		// mysql or postgres
$SQL_Host = "localhost";
$SQL_Port = "3306";		// 3306 or 5432
$SQL_Base = "mrtg";
$SQL_User = "mrtg";
$SQL_Passwd = "12345";

// Russian: URL от корня где проживают картинки для MRTG
// English: root MRTG images URL
$MRTG_Stat_Patch = "images/";

// Russian: URL от корня где проживают картинки для MRTG Errors
// English: root MRTG Errors images URL
$MRTG_Stat_Patch_Err = "images-err/";

// Russian: Куда генерируем временный файл
// English: Where temporarily file is generated to
$TMP_MRTG_CFG_File = "/tmp/mrtg.cfg";

// Russian: Откуда бэкапим старый конфиг и куда заливаем новый
// English: Source configuration file
$MRTG_CFG_File = "/usr/local/etc/mrtg/mrtg.cfg";

// Russian: Куда бэкапим старый конфиг
// English: Destination configuration file
$BACKUP_MRTG_CFG_File = "/usr/local/etc/mrtg/backup/";

// Russian: Массив названий настроек
// Russian: Учавствует в Просмотре хоста
// Russian: Не редактировать !!!
// English: Settings array
// English: Array is used for Host view
// English: Do not edit !!!
$Settings = array('filename','target','interface_ip','interface_name','maxbytes','iftype','title_ip','absmax','withpeak','options','colours','ylegend','shortlegend','legend1','legend2','legend3','legend4','legendi','legendo','routeruptime','kmg','unscaled');

// Russian: Полный массив названий настроек
// Russian: Учавствует в Добавлении/Клонировании/Редактировании хоста
// Russian: Не редактировать !!!
// English: Full array of settings
// English: Array is used for Host Add/Clone/Edit procedures
// English: Do not edit !!!
$Full_Settings = array('id','ip','title','ver_snmp','filename','target','interface_ip','interface_name','maxbytes','iftype','title_ip','absmax','withpeak','options','colours','ylegend','shortlegend','legend1','legend2','legend3','legend4','legendi','legendo','routeruptime','kmg','unscaled');

// Russian: 0 - Запретить изменение HID и GID у участников группы Default
// Russian: 1 - Разрешить изменение HID и GID у участников группы Default
// English: 0 - Disallow HID and GID change for members of Default group
// English: 1 - Allow HID and GID change for members of Default group
$SET_Access_Default_Group_Edit = "1";

// Russian: 0 - Запретить удаление участников из группы Default
// Russian: 1 - Разрешить удаление участников из группы Default
// English: 0 - Disallow Default members deleting
// English: 1 - Allow Default members deleting
$SET_Access_Default_Group_Delete = "1";

// Russian: Группа "Корзина" (Её удалить нельзя, так же как и группу "Default")
// Russian: При удалении клиента он заносится в эту группу и
// Russian: поле agent.trash принимает значение из 0 -> $GID_Trash
// Russian: В файл $MRTG_CFG_File пишутся только те клиенты у которых agent.trash == 0
// Russian: Реальное удаление клиента из базы происходит после его удаления из "Корзины"
// Russian: Путём изменения agent.trash из $GID_Trash -> 0 происходит восстановление клиента
// English: "Trash" group (may not be deleted, just like "Default" group)
// English: When client is deleted it is placed to this group
// English: and agent.trash field is set to 0 -> $GID_Trash
// English: Only those clients having agent.trash == 0 are put into $MRTG_CFG_File file
// English: Actual deletion of the client is performed after client is removed from "Trash" group
// English: If agent.trash filed is changed $GID_Trash -> 0 than client is undeleted
$GID_Trash = "100";

// Russian: Режим отладки (0 - Выкл., 1 - Вкл.)
// English: Debug mode (0 - Off., 1 - On.)
$Debug_Mode = "0";

// Russian: Режим добавления строк в Templates GUI
// Russian: 0 - Добавляем строку перед существующей
// Russian: 1 - Добавляем строку после существующей
// English: Templates GUI lines adding mode
// English: 0 - insert a line before current line
// English: 1 - insert a line after current line
$Mode_Add_Rows = "0";

// Russian: Закрыть всем доступ к странице администрирования кроме указанного логина
// Russian: Пока не работает !!!
// English: Only mentioned login is allowed to access administration page
// English: Not implemented yet !!!
$Close_Admin_Pages = "Admin";

// Russian: Запретить пересборку файла конфигурации MRTG
// Russian: 1 - Запретить
// Russian: 0 - Разрешить
// English: Disallow MRTG config rebuild
// English: 1 - Disallow
// English: 0 - Allow
$Deny_ReBuild_MRTG_File = "0";

// Russian: С каких IP и Подсетей разрешён доступ
// English: IP subnets allowed to access MRTGWebCfg
$Allow_Subnet = array('All');
$Allow_IPAddress = array('All');

// Russian: Язык системы
// Russian: 0 - Ручная установка языка на основе $MRTGLanguage
// Russian: 1 - Авто
// English: System language
// English: 0 - Manual setting based on $MRTGLanguage
// English: 1 - Auto mode
$MRTGAutoLanguage = "1";
// russian, english, french
$MRTGLanguage = "russian";

// Russian: Показывать SNMP пароль на Странице управления
// Russian: 0 - не показывать
// Russian: 1 - показывать
// English: To show or not to show SNMP password on Control Panel
// English: 0 - not to show
// English: 1 - to show
$Show_Community = "0";

// Russian: Устанавливать права на файлы картинок.
// Russian: Параметр устанавливается в 1 если наблюдаются проблемы с перерисовкой графиков.
// Russian: 0 - Не устанавливать права
// Russian: 1 - Устанавливать права
// English: 
// English: 
// English: 
$Auto_CHMOD = "1";

// Russian: Каталоги куда генерируются картинки и на которые нужно изменить права.
// English: 
$CHMOD_Images_Dir = "/home/www/mrtg/images";
$CHMOD_Images_Dir_Err = "/home/www/mrtg/images-err";

// Russian: Запретить изменение файла конфигурации MRTGWebCfg
// Russian: 1 - Запретить
// Russian: 0 - Разрешить
// English: Disallow MRTGWebCfg config file save
// English: 1 - Disallow
// English: 0 - Allow
$Deny_Save_Config_File = "0";

?>
