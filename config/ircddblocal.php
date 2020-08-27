<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$logPath='/var/log/pi-star';
$callsign='F1RMB';
$registerURL = '';
$starLogPath = $logPath . '/STARnet.log';
$linkLogPath = $logPath . '/Links.log';
$hdrLogPath = $logPath . '/Headers.log';
$ddmode_log = $logPath . '/DDMode.log';
$configPath='/etc';
$gatewayConfigPath = '/etc/ircddbgateway';
$defaultConfPath = '/etc/default';
$sharedFilesPath = '/usr/local/etc';
$sysConfigPath = '/etc/sysconfig';
?>

