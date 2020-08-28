<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

// Load the language support
require_once('../config/language.php');
require_once('../config/version.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
    <head>
	<meta name="robots" content="index" />
	<meta name="robots" content="follow" />
	<meta name="language" content="English" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="Author" content="Andrew Taylor (MW0MWZ)" />
	<meta name="Description" content="Pi-Star Expert Editor" />
	<meta name="KeyWords" content="Pi-Star" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<meta http-equiv="Expires" content="0" />
	<title>Pi-Star - Digital Voice Dashboard - Expert Editor</title>
	<script type="text/javascript" src="/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/pistar-css.php" />
    </head>
    <body>
	<div class="container">
	    <?php include './header-menu.inc'; ?>
	    <div class="contentwide">
		
		<?php
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		
		if (strcmp($action, 'stop') == 0) {
		    $action_msg = 'Stopping Services';
		}
		if (strcmp($action, 'fullstop') == 0) {
		    $action_msg = 'Stopping Fully Services';
		}
		else if (strcmp($action, 'restart') == 0) {
		    $action_msg = 'Restarting Services';
		}
		else if (strcmp($action, 'status') == 0) {
		    $action_msg = 'Services Status';
		}
		else {
		    $action_msg = 'Unknown Action';
		}
		?>
		
		<table width="100%">
		    <tr><th><?php echo $action_msg;?></th></tr>
		    <tr><td align="center">
			<?php
			echo '<script type="text/javascript">'."\n";
			echo 'function loadServicesExec(optStr){'."\n";
			echo '  $("#service_result").load("/admin/expert/services_exec.php"+optStr);'."\n";
			echo '  setTimeout(function() { window.location="/admin/expert/index.php";}, ("'.$action.'" == "status" ? 30000: 10000));'."\n";
			echo '}'."\n";
			echo 'setTimeout(loadServicesExec, 100, "?action='.$action.'");'."\n";
			echo '$(window).trigger(\'resize\');'."\n";
			echo '</script>'."\n";
			?>
			<div id="service_result">
			    <br />
			    Please Wait...<br />
			    <br />
			</div>
		    </td></tr>
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
