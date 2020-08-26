<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'DStarRepeater';
$configfile = '/etc/dstarrepeater';
$tempfile = '/tmp/ZHN0YXJyZXBlYXRlcg.tmp';
$servicenames = array('dstarrepeater.service');

require_once('edit_template_nosections.php');
?>
