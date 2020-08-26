<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/root/DMR_Hosts.txt';
$tempfile = '/tmp/2SD3BhQpkuEUM.tmp';

require_once('fulledit_template.php');

?>
