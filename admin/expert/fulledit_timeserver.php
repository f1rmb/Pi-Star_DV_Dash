<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'TimerServer';
$configfile = '/etc/timeserver';
$tempfile = '/tmp/dGltZXNlcnZlcg.tmp';
$servicenames = array('timeserver.service');

require_once('fulledit_template_nosections.php');

?>
