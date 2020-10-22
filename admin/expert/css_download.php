<?php

if (!isset($_SESSION) || !is_array($_SESSION) || (count($_SESSION, COUNT_RECURSIVE) < 10)) {
    session_id('pistardashsess');
    session_start();
}

if (file_exists('/etc/pistar-css.ini'))
{
    $backupDir = "/tmp/css_backup";
    $backupZip = "/tmp/css_backup.zip";
    $hostNameInfo = exec('cat /etc/hostname');
    
    exec("sudo rm -rf $backupZip 2>&1");
    exec("sudo rm -rf $backupDir 2>&1");
    exec("sudo mkdir $backupDir 2>&1");
    exec("sudo cp /etc/pistar-css.ini $backupDir 2>&1");
    exec("sudo zip -j $backupZip $backupDir/* 2>&1");
    
    if (file_exists($backupZip)) {
	$utc_time = gmdate('Y-m-d H:i:s');
	$utc_tz =  new DateTimeZone('UTC');
	$local_tz = new DateTimeZone(date_default_timezone_get ());
	$dt = new DateTime($utc_time, $utc_tz);
	$dt->setTimeZone($local_tz);
	$local_time = $dt->format('d-M-Y');
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	if ($hostNameInfo != "pi-star")
	{
	    header('Content-Disposition: attachment; filename="'.basename("Pi-Star_CSS_".$hostNameInfo."_".$local_time.".zip").'"');
	}
	else
	{
	    header('Content-Disposition: attachment; filename="'.basename("Pi-Star_CSS_$local_time.zip").'"');
	}
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($backupZip));
	ob_clean();
	flush();
	readfile($backupZip);
	exit;
    }
}

?>
