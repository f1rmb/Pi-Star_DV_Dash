<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'DCS Hosts';
$configfile = '/root/DCS_Hosts.txt';
$tempfile = '/tmp/hjuTqB75YtgCn.tmp';

require_once('fulledit_template.php');

?>
