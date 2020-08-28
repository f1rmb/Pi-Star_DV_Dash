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

// Load the language support
require_once('../config/language.php');
require_once('../config/version.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
    <head>
	<meta name="robots" content="index" />
	<meta name="robots" content="follow" />
	<meta name="language" content="English" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="Author" content="Andrew Taylor (MW0MWZ)" />
	<meta name="Description" content="Pi-Star Editor" />
	<meta name="KeyWords" content="Pi-Star" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<meta http-equiv="Expires" content="0" />
	<title>Pi-Star - Digital Voice Dashboard - Expert Editor</title>
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/pistar-css.php" />
    </head>
    <body>
	<div class="container">
	    <?php include './header-menu.inc'; ?>
	    <div class="contentwide">
		
		<?php
		//Do some file wrangling...
		exec('sudo cp '.$configfile.' '.$tempfile);
		exec('sudo chown www-data:www-data '.$tempfile);
		exec('sudo chmod 664 '.$tempfile);
		
		//ini file to open
		$filepath = $tempfile;
		
		//after the form submit
		if($_POST) {
		    $data = $_POST;
		    
		    if (function_exists('process_before_saving')) {
			process_before_saving($data);
		    }
		    
		    //update ini file, call function
		    update_ini_file($data, $filepath);
		}

		//parse the ini file using default parse_ini_file() PHP function
		$parsed_ini = parse_ini_file($filepath, true);
		
		echo '<form action="" method="post">'."\n";
		foreach($parsed_ini as $section=>$values) {
		    // keep the section as hidden text so we can update once the form submitted
		    echo "<input type=\"hidden\" value=\"$section\" name=\"$section\" />\n";
		    echo "<table>\n";
		    echo "<tr><th colspan=\"2\">$section</th></tr>\n";
		    // print all other values as input fields, so can edit. 
		    // note the name='' attribute it has both section and key
		    foreach($values as $key=>$value) {
			echo "<tr><td align=\"right\" width=\"30%\">$key</td><td align=\"left\"><input type=\"text\" name=\"{$section}[$key]\" value=\"$value\" /></td></tr>\n";
		    }
		    echo "</table>\n";
		    echo '<input type="submit" value="'.$lang['apply'].'" />'."\n";
		    echo "<br />\n";
		}
		echo "</form>";
		?>
	    </div>
	    
	    <div class="footer">
		Pi-Star web config, &copy; Andy Taylor (MW0MWZ) 2014-<?php echo date("Y"); ?>.<br />
		&copy; Daniel Caujolle-Bert (F1RMB) 2017-<?php echo date("Y"); ?>.<br />
		Need help? Click <a style="color: #ffffff;" href="https://www.facebook.com/groups/pistarusergroup/" target="_new">here for the Support Group</a><br />
		or Click <a style="color: #ffffff;" href="https://forum.pistar.uk/" target="_new">here to join the Support Forum</a><br />
	    </div>
	</div>
    </body>
</html>
