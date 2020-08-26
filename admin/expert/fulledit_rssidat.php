<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/usr/local/etc/RSSI.dat';
$tempfile = '/tmp/yAw432GHs5.tmp';
$servicenames = array();

require_once('fulledit_template.php');

?>
