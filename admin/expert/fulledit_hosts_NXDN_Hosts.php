<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/root/NXDNHosts.txt';
$tempfile = '/tmp/Hiy8EiH5FoEhc.tmp';

require_once('fulledit_template.php');

?>
