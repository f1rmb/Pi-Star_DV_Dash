<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/dmrgateway';
$tempfile = '/tmp/fmehg65694eg.tmp';

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
	    if ((strncmp($section, "DMR Network", 11) == 0) && ($key == "Options" || $key == "Password" || $key == "URL") && $value) {
		$value = str_replace('"', "", $value);
		$content .= $key."=\"".$value."\"\n";
	    }
	    else {
		$content .= $key."=".$value."\n";
	    }
	}
	$content .= "\n";
    }
    
    //write it into file
    if (!$handle = fopen($filepath, 'w')) {
	return false;
    }
    
    $success = fwrite($handle, $content);
    fclose($handle);
    
    // Updates complete - copy the working file back to the proper location
    exec('sudo mount -o remount,rw /');				                // Make rootfs writable
    exec('sudo cp /tmp/fmehg65694eg.tmp /etc/dmrgateway');	  // Move the file back
    exec('sudo chmod 644 /etc/dmrgateway');				            // Set the correct runtime permissions
    exec('sudo chown root:root /etc/dmrgateway');			        // Set the owner
    exec('sudo mount -o remount,ro /');				                // Make rootfs read-only
    
    // Reload the affected daemon
    exec('sudo systemctl restart dmrgateway.service');		    // Reload the daemon
    return $success;
}

require_once('edit_template.php');

?>
