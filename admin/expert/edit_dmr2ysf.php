<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/dmr2ysf';
$tempfile = '/tmp/SpjKbHh9v39we5.tmp';

// this is the function going to update your ini file
function update_ini_file($data, $filepath) {
    $content = "";
    
    // parse the ini file to get the sections
    // parse the ini file using default parse_ini_file() PHP function
    $parsed_ini = parse_ini_file($filepath, true);
    
    foreach($data as $section=>$values) {
	$section = str_replace("_", " ", $section);
	$section = str_replace(".", " ", $section);
	
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
    exec('sudo cp /tmp/SpjKbHh9v39we5.tmp /etc/dmr2ysf');	// Move the file back
    exec('sudo chmod 644 /etc/dmr2ysf');				// Set the correct runtime permissions
    exec('sudo chown root:root /etc/dmr2ysf');			// Set the owner
    exec('sudo mount -o remount,ro /');				// Make rootfs read-only
    
    // Reload the affected daemon
    exec('sudo systemctl restart dmr2ysf.service');		// Reload the daemon
    return $success;
}

require_once('edit_template.php');

?>
