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
}

// Load the language support
require_once('../config/config.php');
require_once('../mmdvmhost/functions.php');
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
	<meta name="Author" content="Andrew Taylor (MW0MWZ), Daniel Caujolle-Bet (F1RMB)" />
	<meta name="Description" content="Pi-Star Expert Editor" />
	<meta name="KeyWords" content="MMDVMHost,ircDDBGateway,D-Star,ircDDB,DMRGateway,DMR,YSFGateway,YSF,C4FM,NXDNGateway,NXDN,P25Gateway,P25,Pi-Star,DL5DI,DG9VH,MW0MWZ,F1RMB" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
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
		$command = isset($_GET['command']) ? $_GET['command'] : '';
		$arg = isset($_GET['arg']) ? $_GET['arg'] : '';
		$port = $_SESSION['DMRGatewayConfigs']['Remote Control']['Port'];
		$command_msg = '';
		$arg_msg = '';
		$message = '';
		$has_args = TRUE;
		$command_url = '?command='.$command.'&port='.$port;

		if (!empty($arg)) {
		    $command_url = $command_url.'&arg='.$arg;
		}

		if (!empty($command)) {

		    if (strcmp($command, 'status') == 0) {
			$command_msg = 'DMRGateway Status';
			$has_args = FALSE;
		    }
		    else if (strcmp($command, 'hosts') == 0) {
			$command_msg = 'DMRGateway Hosts';
			$has_args = FALSE;
		    }
		    else if (strcmp($command, 'enable') == 0) {
			$command_msg = 'Enable';
		    }
		    else if (strcmp($command, 'disable') == 0) {
			$command_msg = 'Disable';
		    }

		    if ($has_args == TRUE && !empty($arg)) {
			if (strcmp($arg, 'xlx') == 0) {
			    $arg_msg = 'xlx';
			}
			else if (strcmp($arg, 'net1') == 0) {
			    $arg_msg = 'net1';
			}
			else if (strcmp($arg, 'net2') == 0) {
			    $arg_msg = 'net2';
			}
			else if (strcmp($arg, 'net3') == 0) {
			    $arg_msg = 'net3';
			}
			else if (strcmp($arg, 'net4') == 0) {
			    $arg_msg = 'net4';
			}
			else if (strcmp($arg, 'net5') == 0) {
			    $arg_msg = 'net5';
			}
		    }

		    $message = $command_msg;
		    if ($has_args == TRUE && !empty($arg_msg)) {
			$message = $message.' '.$arg_msg;
		    }
		}
		?>

		<table width="100%">
		    <tr><th><?php echo $message;?></th></tr>
		    <tr><td align="center">
			<?php
			echo '<script type="text/javascript">'."\n";
			echo 'function executeDMRGatewayRemoteCommand(optStr){'."\n";
			echo '  $("#command_result").load("/admin/expert/dmrgateway_remote_exec.php"+optStr);'."\n";
			echo '  setTimeout(function() { window.location="/admin/expert/index.php";}, ("'.$command.'" == "status" || "'.$command.'" == "hosts" ? 5000: 2000));'."\n";
			echo '}'."\n";
			echo 'setTimeout(executeDMRGatewayRemoteCommand, 100, "'.$command_url.'");'."\n";
			echo '$(window).trigger(\'resize\');'."\n";
			echo '</script>'."\n";
			?>
			<div id="command_result">
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
