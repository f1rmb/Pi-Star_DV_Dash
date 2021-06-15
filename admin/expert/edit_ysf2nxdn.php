<?php
if (isset($_COOKIE['PHPSESSID']))
{
    session_id($_COOKIE['PHPSESSID']); 
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION) || (count($_SESSION, COUNT_RECURSIVE) < 10)) {
    session_id('pistardashsess');
    session_start();
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

$configfile = '/etc/ysf2nxdn';
$tempfile = '/tmp/7kjuNZfirZGXqR.tmp';

// this is the function going to update your ini file
function update_ini_file($data, $filepath) {
    $content = "";
    
    // parse the ini file to get the sections
    // parse the ini file using default parse_ini_file() PHP function
    $parsed_ini = parse_ini_file($filepath, true);
    
    foreach($data as $section=>$values) {
	// UnBreak special cases
	if (strpos($section, 'aprs') !== false) { $section = str_replace("_", ".", $section); }
	else { $section = str_replace("_", " ", $section); $section = str_replace(".", " ", $section); }
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
    exec('sudo mount -o remount,rw /');				// Make rootfs writable
    exec('sudo cp /tmp/7kjuNZfirZGXqR.tmp /etc/ysf2nxdn');	// Move the file back
    exec('sudo chmod 644 /etc/ysf2nxdn');				// Set the correct runtime permissions
    exec('sudo chown root:root /etc/ysf2nxdn');			// Set the owner
    exec('sudo sync && sudo sync && sudo sync && sudo mount -o remount,ro /');				// Make rootfs read-only
    
    // Reload the affected daemon
    exec('sudo systemctl restart ysf2nxdn.service');		// Reload the daemon
    return $success;
}

require_once('edit_template.php');

?>
