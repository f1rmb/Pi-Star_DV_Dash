<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$editorname = 'DPlus Hosts';
$configfile = '/root/DPlus_Hosts.txt';
$tempfile = '/tmp/xmVwKAY7H4X42.tmp';

require_once('fulledit_template.php');

?>
