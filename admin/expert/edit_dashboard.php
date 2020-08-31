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
	<meta name="Author" content="Andrew Taylor (MW0MWZ), Daniel Caujolle-Bert (F1RMB)" />
	<meta name="Description" content="Pi-Star Expert Editor" />
	<meta name="KeyWords" content="MMDVMHost,ircDDBGateway,D-Star,ircDDB,DMRGateway,DMR,YSFGateway,YSF,C4FM,NXDNGateway,NXDN,P25Gateway,P25,Pi-Star,DL5DI,DG9VH,MW0MWZ,F1RMB" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<meta http-equiv="Expires" content="0" />
	<title>Pi-Star - Digital Voice Dashboard - Expert Editor</title>
	<script type="text/javascript" src="/jquery.min.js"></script>
	<script type="text/javascript" src="/css/farbtastic/farbtastic.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/farbtastic/farbtastic.css" />
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/pistar-css.php" />
	<style type="text/css" media="screen">
	 .colorwell {
	     border: 2px solid #fff;
	     width: 6em;
	     text-align: center;
	     cursor: pointer;
	 }
	 body .colorwell-selected {
	     border: 2px solid #000;
	     font-weight: bold;
	 }
	</style>
	<script type="text/javascript">
	 function cssDownload()
	 {
	     window.location.href = "/admin/expert/css_download.php";
	 }
	 
	 function cssUpload()
	 {
	     document.getElementById('fileid').addEventListener('change', submitForm);
	     document.getElementById('fileid').click();
	 }
	 
         function submitForm() {
	     document.getElementById('cssUpload').submit();
         }
	 
	 function cssReset()
	 {
	     if (confirm('WARNING: This will set these settings back to factory defaults.\n\nAre you SURE you want to do this?\n\nPress OK to restore the factory CSS configuration\nPress Cancel to go back.')) {
		 document.getElementById("cssReset").submit();
	     } else {
		 return false;
	     }
	 }

	 $(document).ready(function() {
	     var f = $.farbtastic('#colorpicker');
	     var p = $('#colorpicker').css('opacity', 1).hide();
	     var selected;
	     $('.colorwell')
	         .each(function () { f.linkTo(this); $(this).css('opacity', 1); })
	         .focus(function() {
		     if (selected) {
			 $(selected).removeClass('colorwell-selected');
		     }
		     p.show();
		     f.linkTo(this);
		     $(selected = this).addClass('colorwell-selected');
		 })
		 .blur(function() {
		     if (selected) {
			 $(selected).removeClass('colorwell-selected');
			 seleted = null;
			 p.hide();
			 f.linkTo(function(){});
		     }
		 });
	 });
	 
	</script>
    </head>
    <body>
	<div class="container">
	    <?php include './header-menu.inc'; ?>
	    <div class="contentwide">
		
		<?php

		// Check if we are using the new CSS configuration syntax
		if (file_exists('/etc/pistar-css.ini')) {
		    $dataContent = file_get_contents('/etc/pistar-css.ini');
		    
		    if (! preg_match('/NavPanelColor/', $dataContent))
		    {
			// Reset CSS configuration
			exec('sudo mount -o remount,rw /');                             // Make rootfs writable
			exec('sudo rm -rf /etc/pistar-css.ini');                        // Delete the Config
			exec('sudo mount -o remount,ro /');                             // Make rootfs read-only
			echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},0);</script>';
			die();
		    }
		}

		if (!file_exists('/etc/pistar-css.ini')) {
		    //The source file does not exist, lets create it....
		    $outFile = fopen("/tmp/bW1kd4jg6b3N0DQo.tmp", "w") or die("Unable to open file!"); //#bf0707
		    $fileContent = "[Background]\nPageColor=#edf0f5\nContentColor=#ffffff\nBannersColor=#dd4b39\nNavbarColor=#242d31\nNavbarHoverColor=#a60000\nDropdownColor=#f9f9f9\nDropdownHoverColor=#d0d0d0\nServiceCellActiveColor=#11DD11\nServiceCellInactiveColor=#BB5555\nModeCellDisabledColor=#606060\nModeCellActiveColor=#00BB00\nModeCellInactiveColor=#BB0000\nNavPanelColor=#242d31\n\n";
		    $fileContent .= "[Text]\nTableHeaderColor=#ffffff\nBannersColor=#ffffff\nBannersDropColor=#303030\nNavbarColor=#ffffff\nNavbarHoverColor=#ffffff\nDropdownColor=#000000\nDropdownHoverColor=#000000\nServiceCellActiveColor=#000000\nServiceCellInactiveColor=#000000\nModeCellDisabledColor=#b0b0b0\nModeCellActiveColor=#003300\nModeCellInactiveColor=#550000\n\n";
		    $fileContent .= "[Tables]\nHeadDropColor=#8b0000\nBgEvenColor=#f7f7f7\nBgOddColor=#d0d0d0\n\n";
		    $fileContent .= "[Content]\nTextColor=#000000\n\n";
		    $fileContent .= "[BannerH2]\nEnabled=0\nText=Some Text\n\n";
		    $fileContent .= "[BannerExtText]\nEnabled=0\nText=Some long text entry\n";
		    fwrite($outFile, $fileContent);
		    fclose($outFile);
		    
		    // Put the file back where it should be
		    exec('sudo mount -o remount,rw /');                             // Make rootfs writable
		    exec('sudo cp /tmp/bW1kd4jg6b3N0DQo.tmp /etc/pistar-css.ini');  // Move the file back
		    exec('sudo chmod 644 /etc/pistar-css.ini');                     // Set the correct runtime permissions
		    exec('sudo chown root:root /etc/pistar-css.ini');               // Set the owner
		    exec('sudo mount -o remount,ro /');                             // Make rootfs read-only
		}
		
		//Do some file wrangling...
		exec('sudo cp /etc/pistar-css.ini /tmp/bW1kd4jg6b3N0DQo.tmp');
		exec('sudo chown www-data:www-data /tmp/bW1kd4jg6b3N0DQo.tmp');
		exec('sudo chmod 664 /tmp/bW1kd4jg6b3N0DQo.tmp');
		
		//ini file to open
		$filepath = '/tmp/bW1kd4jg6b3N0DQo.tmp';
		
		//after the form submit
		if($_POST) {
		    $data = $_POST;
		    // CSS Factory Reset Handler Here
		    if (empty($_POST['cssReset']) != TRUE) {
			echo "<br />\n";
			echo "<table>\n";
			echo "<tr><th>CSS Configuration Reset</th></tr>\n";
			echo "<tr><td>Loading fresh configuration file(s)...</td><tr>\n";
			echo "</table>\n";
			unset($_POST);
			//Reset the config
			exec('sudo mount -o remount,rw /');                             // Make rootfs writable
			exec('sudo rm -rf /etc/pistar-css.ini');                        // Delete the Config
			exec('sudo mount -o remount,ro /');                             // Make rootfs read-only
			echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},0);</script>';
			die();
		    }
		    else if (empty($_POST['cssDownload']) != TRUE)
		    {
			// Do nothing, handled in JS function
		    }
		    else if (empty($_POST['cssUpload']) != TRUE)
		    {
			echo "<tr><th colspan=\"2\">CCS Configuration Restore</th></tr>\n";
			
			if (isset($_FILES['cssFile']) && $_FILES['cssFile']['error'] === UPLOAD_ERR_OK)
			{
			    $output = "Uploading your CSS configuration data\n";
			    $target_dir = "/tmp/css_restore/";
			    $okay = false;
			    
			    shell_exec("sudo rm -rf $target_dir 2>&1");
			    shell_exec("mkdir $target_dir 2>&1");
			    
			    if($_FILES["cssFile"]["name"]) {
				$filename = $_FILES["cssFile"]["name"];
	  			$source = $_FILES["cssFile"]["tmp_name"];
				$type = $_FILES["cssFile"]["type"];
				
				$name = explode(".", $filename);
				$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
				
				foreach($accepted_types as $mime_type) {
				    if($mime_type == $type) {
					$okay = true;
					break;
				    }
				}
			    }

			    $continue = false;
			    if (isset($name))
			    {
				$continue = strtolower($name[1]) == 'zip' ? true : false;
			    }
			    
			    if ($okay == false || $continue == false) {
				$output .= "The file you are trying to upload is not a .zip file. Please try again.\n";
				die();
			    }
			    
			    if (isset($filename))
			    {
				$target_path = $target_dir.$filename;
			    }
			    
			    if(isset($target_path) && move_uploaded_file($source, $target_path)) {
				$zip = new ZipArchive();
				$x = $zip->open($target_path);
				if ($x === true) {
			            $zip->extractTo($target_dir); // change this to the correct site path
			            $zip->close();
			            unlink($target_path);
				}
				
				$output .= "Your .zip file was uploaded and unpacked.\n";

				// Make the disk Writable
				shell_exec('sudo mount -o remount,rw / 2>&1');
				
				$output .= "Copying CSS configuration file\n";
				$output .= shell_exec("sudo mv -v -f /tmp/css_restore/pistar-css.ini /etc/ 2>&1")."\n";

				// Make the disk Read-Only
				shell_exec('sudo mount -o remount,ro / 2>&1');
				
				// Complete
				$output .= "Configuration Restore Complete.\n";
				
				echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;}, 4000);</script>';
			    }
			    else {
				$output .= "There was a problem with the upload. Please try again.<br />";
				$output .= "\n".'<button onclick="goBack()">Go Back</button><br />'."\n";
				$output .= '<script>'."\n";
				$output .= 'function goBack() {'."\n";
				$output .= '    window.history.back();'."\n";
				$output .= '}'."\n";
				$output .= '</script>'."\n";
			    }
			    echo "<tr><td align=\"left\"><pre>$output</pre></td></tr>\n";
			}
			die();
		    }
		    else
		    {
			//update ini file, call function
			update_ini_file($data, $filepath);
		    }
		}

		function endsWith($haystack, $needle)
		{
		    $length = strlen($needle);
		    
		    if ($length == 0) {
			return true;
		    }
		    
		    return (strcasecmp(substr($haystack, -$length), $needle) == 0);
		}
		
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
			    if ($value == '') {
				$content .= $key."=none\n";
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
		    exec('sudo mount -o remount,rw /');                             // Make rootfs writable
		    exec('sudo cp /tmp/bW1kd4jg6b3N0DQo.tmp /etc/pistar-css.ini');  // Move the file back
		    exec('sudo chmod 644 /etc/pistar-css.ini');                     // Set the correct runtime permissions
		    exec('sudo chown root:root /etc/pistar-css.ini');               // Set the owner
		    exec('sudo mount -o remount,ro /');                             // Make rootfs read-only
		    
		    return $success;
		}
		
		//parse the ini file using default parse_ini_file() PHP function
		$parsed_ini = parse_ini_file($filepath, true);
		
		echo '<form action="" method="post">'."\n";

		// Colorpicker
		echo '<div style="position: fixed; pointer-events: none; transform: translateX(230%);" >'."\n";
		echo '<div id="colorpicker" style="float: right; margin: 20px; pointer-events: auto;"></div>'."\n";
		echo '</div>'."\n";
		
		foreach($parsed_ini as $section=>$values) {
		    // keep the section as hidden text so we can update once the form submitted
		    echo "<input type=\"hidden\" value=\"$section\" name=\"$section\" />\n";
		    echo "<table>\n";
		    echo "<tr><th colspan=\"2\">$section</th></tr>\n";
		    // print all other values as input fields, so can edit. 
		    // note the name='' attribute it has both section and key
		    foreach($values as $key=>$value) {
			if (endsWith($key, 'Color')) {
			    echo "<tr><td align=\"right\" width=\"30%\">$key</td><td align=\"left\"><input type=\"text\" class=\"colorwell\" name=\"{$section}[$key]\" value=\"$value\" /></td></tr>\n";
			}
			else {
			    echo "<tr><td align=\"right\" width=\"30%\">$key</td><td align=\"left\"><input type=\"text\" name=\"{$section}[$key]\" value=\"$value\" /></td></tr>\n";
			    }
		    }
		    echo "</table>\n";
		    echo '<input type="submit" value="'.$lang['apply'].'" />'."\n";
		    echo "<br />\n";
		}
		echo "</form>";
		echo "<br /><br />\n";
		echo 'If you took it all too far and now it makes you feel sick, click below to reset the values to default.'."\n";
		echo '<form id="cssUpload" action="" method="POST" enctype="multipart/form-data">'."\n";
		echo '  <div><input id="fileid" name="cssFile" type="file" hidden/></div>'."\n";
		echo '  <div><input type="hidden" name="cssUpload" value="1" /></div>'."\n";
		echo '</form>'."\n";
		echo '<form id="cssReset" action="" method="POST">'."\n";
		echo '  <div><input type="hidden" name="cssReset" value="1" /></div>'."\n";
		echo '</form>'."\n";
		echo '<input type="button" onclick="javascript:cssDownload();" value="CSS Download" />'."\n";
		echo '<input type="button" onclick="javascript:cssUpload();" value="CSS Upload" />'."\n";
		echo '<input type="button" onclick="javascript:cssReset();" value="CSS '.$lang['factory_reset'].'" />'."\n";
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
