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

$configfile = '/etc/aprsgateway';
$tempfile = '/tmp/oDFuttgksHSRb8.tmp';

//this is the function going to update your ini file
function update_ini_file($data, $filepath) {
    $content = "";
    
    //parse the ini file to get the sections
    //parse the ini file using default parse_ini_file() PHP function
    $parsed_ini = parse_ini_file($filepath, true);
    
    foreach($data as $section=>$values) {
	// UnBreak special cases
	$section = str_replace("_", " ", $section);
	$content .= "[".$section."]\n";
	//append the values
	foreach($values as $key=>$value) {
	    $content .= $key."=".$value."\n";
	}
	$content .= "\n";
    }
    
    // write it into file
    if (!$handle = fopen($filepath, 'w')) {
	return false;
    }
    
    $success = fwrite($handle, $content);
    fclose($handle);
    
    // Updates complete - copy the working file back to the proper location
    exec('sudo mount -o remount,rw /');
    exec('sudo cp /tmp/oDFuttgksHSRb8.tmp /etc/aprsgateway');
    exec('sudo chmod 644 /etc/dapnetgateway');
    exec('sudo chown root:root /etc/dapnetgateway');
    exec('sudo mount -o remount,ro /');
    
    // Reload the affected daemon
    exec('sudo systemctl restart aprsgateway.service');
    return $success;
}

require_once('edit_template.php');

?>
