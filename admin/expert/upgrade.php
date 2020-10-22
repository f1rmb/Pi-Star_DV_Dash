<?php
if (isset($_COOKIE['PHPSESSID']))
{
    session_id($_COOKIE['PHPSESSID']); 
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

// Load the language support
require_once('../config/language.php');
require_once('../config/version.php');

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/expert/upgrade.php") {
    
    if (!isset($_GET['ajax'])) {
	system('sudo touch /var/log/pi-star/pi-star_upgrade.log > /dev/null 2>&1 &');
	system('sudo echo "" > /var/log/pi-star/pi-star_upgrade.log > /dev/null 2>&1 &');
	system('sudo /usr/local/sbin/pistar-upgrade > /dev/null 2>&1 &');
    }
    
    // Sanity Check Passed.
    header('Cache-Control: no-cache');
    
    if (!isset($_GET['ajax'])) {
	//unset($_SESSION['update_offset']);
	if (file_exists('/var/log/pi-star/pi-star_upgrade.log')) {
	    $_SESSION['update_offset'] = filesize('/var/log/pi-star/pi-star_upgrade.log');
	}
	else {
	    $_SESSION['update_offset'] = 0;
	}
    }
    
    if (isset($_GET['ajax'])) {
	if (!file_exists('/var/log/pi-star/pi-star_upgrade.log')) {
	    exit();
	}
	
	if (($handle = fopen('/var/log/pi-star/pi-star_upgrade.log', 'rb')) != FALSE) {
	    if (isset($_SESSION['update_offset'])) {
		fseek($handle, 0, SEEK_END);
		if ($_SESSION['update_offset'] > ftell($handle)) { //log rotated/truncated
		    $_SESSION['update_offset'] = 0; //continue at beginning of the new log
		}
		$data = stream_get_contents($handle, -1, $_SESSION['update_offset']);
		$_SESSION['update_offset'] += strlen($data);
		echo nl2br($data);
	    }
	    else {
		fseek($handle, 0, SEEK_END);
		$_SESSION['update_offset'] = ftell($handle);
	    }
	    fclose($handle);
	}
	exit();
    }
    
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
	<meta name="Description" content="Pi-Star Upgrade" />
e	<meta name="KeyWords" content="MMDVMHost,ircDDBGateway,D-Star,ircDDB,DMRGateway,DMR,YSFGateway,YSF,C4FM,NXDNGateway,NXDN,P25Gateway,P25,Pi-Star,DL5DI,DG9VH,MW0MWZ,F1RMB" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<meta http-equiv="Expires" content="0" />
	<title>Pi-Star - <?php echo $lang['digital_voice']." ".$lang['dashboard']." - ".$lang['update'];?></title>
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/pistar-css.php" />
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
	<script type="text/javascript" src="/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="/jquery-timing.min.js"></script>
	<script type="text/javascript">
	 $(function() {
	     $.repeat(1000, function() {
		 $.get('/admin/expert/upgrade.php?ajax', function(data) {
		     if (data.length < 1) return;
		     var objDiv = document.getElementById("tail");
		     var isScrolledToBottom = objDiv.scrollHeight - objDiv.clientHeight <= objDiv.scrollTop + 1;
		     $('#tail').append(data);
		     if (isScrolledToBottom)
			 objDiv.scrollTop = objDiv.scrollHeight;
		 });
	     });
	 });
	</script>
    </head>
    <body>
	<div class="container">
	    <?php include './header-menu.inc'; ?>
	    <div class="contentwide">
		<table width="100%">
		    <tr><th>Upgrade is Running</th></tr>
		    <tr><td align="left"><div id="tail">Starting upgrade, please wait...<br /></div></td></tr>
		</table>
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

<?php
}
?>
