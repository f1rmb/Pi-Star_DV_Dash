<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'ircDDBGateway';
$configfile = '/etc/ircddbgateway';
$tempfile = '/tmp/aXJjZGRiZ2F0ZXdheQ.tmp';
$servicenames = array('ircddbgateway.service');

require_once('edit_template_nosections.php');
?>
