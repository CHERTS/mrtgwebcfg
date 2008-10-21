<?

// Russian: Файл конфигурации MRTGWebCfg для генерации файла ошибок на интерфейсах MRTG
// English: MRTGWebCfg configuration file, is used for error's file generation on MRTG interfaces

// Russian: Как происходит генерация:
// English: How generation is being performed:
// ifInErrors:	.1.3.6.1.2.1.2.2.1.14.SNMP-IFIndex
// ifOutErrors: .1.3.6.1.2.1.2.2.1.20.SNMP-IFIndex
// Target["Host-Desc"]: .1.3.6.1.2.1.2.2.1.14.SNMP-IFIndex&.1.3.6.1.2.1.2.2.1.20.SNMP-IFIndex:SNMP-Community@IPAddress

// Russian: Куда генерируем временный файл
// English: Where temporarily file is generated to
$TMP_MRTG_CFG_File_Err = "/tmp/mrtg.error.cfg";

// Russian: Откуда бэкапим старый конфиг и куда заливаем новый
// English: Source configuration file
$MRTG_CFG_File_Err = "/usr/local/etc/mrtg/mrtg.error.cfg";

// Russian: Куда бэкапим старый конфиг
// English: Destination configuration file
$BACKUP_MRTG_CFG_File_Err = "/usr/local/etc/mrtg/backup-err/";

?>
