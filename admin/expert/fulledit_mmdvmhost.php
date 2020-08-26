<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/mmdvmhost';
$tempfile = '/tmp/bW1kdm1ob3N0DQo.tmp';
$servicenames = array('mmdvmhost.service');

require_once('fulledit_template.php');

?>
