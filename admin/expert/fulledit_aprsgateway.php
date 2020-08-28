<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'APRSGateway';
$configfile = '/etc/aprsgateway';
$tempfile = '/tmp/oDFuttgksHSRb8.tmp';
$servicenames = array('aprsgateway.service');

require_once('fulledit_template.php');

?>
