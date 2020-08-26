<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/dmrgateway';
$tempfile = '/tmp/fmehg65694eg.tmp';
$servicenames = array('mmdvmhost.service', 'dmrgateway.service');

require_once('fulledit_template.php');

?>
