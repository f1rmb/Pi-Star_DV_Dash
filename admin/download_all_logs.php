<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

if ($_SERVER["PHP_SELF"] == "/admin/download_all_logs.php") {
    $backupDir = "/tmp/logs_backup";
    $backupZip = "/tmp/logs_backup.zip";
    
    exec("sudo rm -rf ".$backupZip." 2>&1");
    exec("sudo rm -rf ".$backupDir." 2>&1");
    exec("sudo mkdir -p ".$backupDir."/pi-star ".$backupDir."/nginx 2>&1");
    exec("sudo cp /var/log/pi-star/* ".$backupDir."/pi-star 2>&1");
    exec("sudo cp /var/log/nginx/error.log ".$backupDir."/nginx 2>&1");
    exec("sudo /bin/sh -c 'cd ".$backupDir." && zip -r9 ".$backupZip." ./' 2>&1");

    if (file_exists($backupZip)) {
	$hostNameInfo = exec('cat /etc/hostname');
	$utc_time = gmdate('Y-m-d H:i:s');
	$utc_tz =  new DateTimeZone('UTC');
	$local_tz = new DateTimeZone(date_default_timezone_get());
	$dt = new DateTime($utc_time, $utc_tz);
	$dt->setTimeZone($local_tz);
	$local_time = $dt->format('Y-m-d_H-i-s');
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	if ($hostNameInfo != "pi-star") {
	    header('Content-Disposition: attachment; filename="'.basename("Pi-Star_Logs_".$hostNameInfo."_".$local_time.".zip").'"');
	}
	else {
	    header('Content-Disposition: attachment; filename="'.basename("Pi-Star_Logs_$local_time.zip").'"');
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
else {
    die();
}
?>
