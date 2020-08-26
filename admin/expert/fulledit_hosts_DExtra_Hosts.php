<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/root/DExtra_Hosts.txt';
$tempfile = '/tmp/hjQK9Yc7xLvdP.tmp';

require_once('fulledit_template.php');

?>
