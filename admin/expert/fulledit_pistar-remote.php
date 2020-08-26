<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/pistar-remote';
$tempfile = '/tmp/fmehg65934eg.tmp';
$servicenames = array('pistar-remote.service');

require_once('fulledit_template.php');

?>
