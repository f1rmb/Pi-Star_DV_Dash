<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'GPSd';
$configfile = '/etc/default/gpsd';
$tempfile = '/tmp/zmh2nHP4qgkwgv.tmp';
$servicenames = array('gpsd.service');

require_once('fulledit_template.php');

?>
