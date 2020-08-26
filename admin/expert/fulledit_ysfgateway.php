<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/ysfgateway';
$tempfile = '/tmp/eXNmZ2F0ZXdheQ.tmp';
$servicenames = array('mmdvmhost.service', 'ysfgateway.service');

require_once('fulledit_template.php');

?>
