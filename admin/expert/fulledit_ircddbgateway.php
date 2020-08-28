<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'ircDDBGateway';
$configfile = '/etc/ircddbgateway';
$tempfile = '/tmp/aXJjZGRiZ2F0ZXdheQ.tmp';
$servicenames = array('ircddbgateway.service');

function process_before_saving($key, &$value)
{
    if (isset($key) && !empty($value) && ($key == 'dplusLogin')) {
	$value = str_pad($value, 8, " ");
    }
}

require_once('fulledit_template_nosections.php');

?>
