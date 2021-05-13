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
require_once('config/language.php');
require_once('config/version.php');

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/live_modem_log.php") {
    
    // Sanity Check Passed.
    header('Cache-Control: no-cache');
    
    if (!isset($_GET['ajax'])) {
	unset($_SESSION['offset']);
	//$_SESSION['offset'] = 0;
    }
    
    if (isset($_GET['ajax'])) {
	if (file_exists('/etc/dstar-radio.mmdvmhost')) {
	    $logfile = "/var/log/pi-star/MMDVM-".gmdate('Y-m-d').".log";
	}
	else if (file_exists('/etc/dstar-radio.dstarrepeater')) {
	    if (file_exists("/var/log/pi-star/DStarRepeater-".gmdate('Y-m-d').".log")) {
		$logfile = "/var/log/pi-star/DStarRepeater-".gmdate('Y-m-d').".log";
	    }
	    else if (file_exists("/var/log/pi-star/dstarrepeaterd-".gmdate('Y-m-d').".log")) {
		$logfile = "/var/log/pi-star/dstarrepeaterd-".gmdate('Y-m-d').".log";
	    }
	}
	
	if (empty($logfile) || !file_exists($logfile)) {
	    exit();
	}
	
	$handle = fopen($logfile, 'rb');
	if (isset($_SESSION['offset'])) {
	    fseek($handle, 0, SEEK_END);
	    if ($_SESSION['offset'] > ftell($handle)) { //log rotated/truncated
		$_SESSION['offset'] = 0; //continue at beginning of the new log
	    }
	    $data = stream_get_contents($handle, -1, $_SESSION['offset']);
	    $_SESSION['offset'] += strlen($data);
	    echo nl2br($data);
	}
	else {
	    fseek($handle, 0, SEEK_END);
	    $_SESSION['offset'] = ftell($handle);
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
    <meta name="Description" content="Pi-Star Live Modem Log" />
    <meta name="KeyWords" content="MMDVMHost,ircDDBGateway,D-Star,ircDDB,DMRGateway,DMR,YSFGateway,YSF,C4FM,NXDNGateway,NXDN,P25Gateway,P25,Pi-Star,DL5DI,DG9VH,MW0MWZ,F1RMB" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <meta http-equiv="Expires" content="0" />
    <title>Pi-Star - <?php echo $lang['digital_voice']." ".$lang['dashboard']." - ".$lang['live_logs'];?></title>
    <link rel="stylesheet" type="text/css" href="/css/pistar-css.php" />
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <script type="text/javascript" src="/jquery.min.js"></script>
    <script type="text/javascript" src="/jquery-timing.min.js"></script>
    <script type="text/javascript">
    $(function() {
      $.repeat(1000, function() {
        $.get('/admin/live_modem_log.php?ajax', function(data) {
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
	  <div class="header">
	      <div style="font-size: 8px; text-align: left; padding-left: 8px; float: left;">Hostname: <?php echo exec('cat /etc/hostname'); ?></div><div style="font-size: 8px; text-align: right; padding-right: 8px;">Pi-Star:<?php echo $_SESSION['PiStarRelease']['Pi-Star']['Version']?> / <?php echo $lang['dashboard'].": ".$version; ?></div>
	      <h1>Pi-Star <?php echo $lang['digital_voice']." - ".$lang['live_logs'];?></h1>
	      <p>
		  <div class="navbar">
		      <a class="menuconfig" href="/admin/configure.php"><?php echo $lang['configuration'];?></a>
		      <a class="menubackup" href="/admin/config_backup.php"><?php echo $lang['backup_restore'];?></a>
		      <a class="menupower" href="/admin/power.php"><?php echo $lang['power'];?></a>
		      <a class="menuadmin" href="/admin/"><?php echo $lang['admin'];?></a>
		      <a class="menudashboard" href="/"><?php echo $lang['dashboard'];?></a>
		  </div>
	      </p>
	  </div>
	  <div class="contentwide">
	      <table width="100%">
		  <tr><th><?php echo $lang['live_logs'];?></th></tr>
		  <tr><td align="left"><div id="tail">Starting logging, please wait...<br /></div></td></tr>
		  <tr><th align="right">
		      <button class="button" onclick="location.href='/admin/download_modem_log.php'" style="margin:2px 5px;">Download This Log</button>
		      <button class="button" onclick="location.href='/admin/download_all_logs.php'" style="margin:2px 5px;">Download All Logs</button>
		  </th></tr>
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
