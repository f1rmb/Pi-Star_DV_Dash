<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/wpa_supplicant/wpa_supplicant.conf';
$tempfile = '/tmp/k45s7h5s9k3.tmp';
$servicenames = array();

require_once('fulledit_template.php');

?>
