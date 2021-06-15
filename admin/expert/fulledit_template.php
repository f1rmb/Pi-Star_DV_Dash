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
	<meta name="Author" content="Andrew Taylor (MW0MWZ), Daniel Caujolle-Bet (F1RMB)" />
	<meta name="Description" content="Pi-Star Expert Editor" />
	<meta name="KeyWords" content="MMDVMHost,ircDDBGateway,D-Star,ircDDB,DMRGateway,DMR,YSFGateway,YSF,C4FM,NXDNGateway,NXDN,P25Gateway,P25,Pi-Star,DL5DI,DG9VH,MW0MWZ,F1RMB" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
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
		if(isset($_POST['data'])) {
		    // File Wrangling
		    exec('sudo cp '.$configfile.' '.$tempfile);
		    exec('sudo chown www-data:www-data '.$tempfile);
		    exec('sudo chmod 664 '.$tempfile);
		    
		    // Open the file and write the data
		    $filepath = $tempfile;
		    $fh = fopen($filepath, 'w');
		    $data = str_replace("\r", "", $_POST['data']);

		    if (function_exists('process_before_saving')) {
			process_before_saving($data);
		    }
		    			
		    fwrite($fh, $data);;
		    fclose($fh);
		    
		    exec('sudo mount -o remount,rw /');
		    exec('sudo cp '.$tempfile.' '.$configfile);
		    exec('sudo chmod 644 '.$configfile);
		    exec('sudo chown root:root '.$configfile);
		    exec('sudo sync && sudo sync && sudo sync && sudo mount -o remount,ro /');
		    
		    // Reload the affected daemon
		    if (isset($servicenames) && (count($servicenames) > 0)) {
			foreach($servicenames as $servicename) {
			    exec('sudo systemctl restart '.$servicename); // Reload the daemon
			}
		    }
		    
		    // Re-open the file and read it
		    $fh = fopen($filepath, 'r');
		    $theData = fread($fh, filesize($filepath));
		    
		}
		else {
		    // File Wrangling
		    exec('sudo cp '.$configfile.' '.$tempfile);
		    exec('sudo chown www-data:www-data '.$tempfile);
		    exec('sudo chmod 664 '.$tempfile);
		    
		    // Open the file and read it
		    $filepath = $tempfile;
		    $fh = fopen($filepath, 'r');
		    $theData = fread($fh, filesize($filepath));
		}
		fclose($fh);
		
		?>
		
		<form name="test" method="post" action="">
		    <label for="data" class="header" style="display:block;text-align:center;" ><?php echo $editorname ?></label> 
		    <textarea id="data" name="data" cols="80" rows="45"><?php echo $theData; ?></textarea><br />
		    <input type="submit" name="submit" value="<?php echo $lang['apply']; ?>" />
		</form>
		
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
