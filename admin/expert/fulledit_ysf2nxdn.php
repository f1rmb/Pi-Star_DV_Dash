<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

$editorname = 'YSF2NXDN';
$configfile = '/etc/ysf2nxdn';
$tempfile = '/tmp/7kjuNZfirZGXqR.tmp';
$servicenames = array('mmdvmhost.service', 'nxdngateway.service', 'ysf2nxdn.service');

require_once('fulledit_template.php');

?>
