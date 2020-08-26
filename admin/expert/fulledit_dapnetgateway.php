<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/dapnetgateway';
$tempfile = '/tmp/cVKu8oJJKWqe.tmp';
$servicenames = array('dapnetgateway.service');

require_once('fulledit_template.php');

?>
