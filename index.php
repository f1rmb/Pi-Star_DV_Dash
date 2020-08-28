<?php
session_set_cookie_params(0, "/");
session_name("PiStar Dashboard Session");
session_id('pistardashsess');
session_start();

require_once('config/config.php');
require_once('config/version.php');
require_once('mmdvmhost/functions.php');
require_once('config/ircddblocal.php');
require_once('config/language.php');


$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
$rev = $version;
$MYCALL = strtoupper($callsign);
$_SESSION['MYCALL'] = $MYCALL;

// Clear session data (page {re}load);
unset($_SESSION['BMAPIKey']);
unset($_SESSION['DAPNETAPIKey']);
unset($_SESSION['YSF2DMRConfigs']);
unset($_SESSION['YSF2NXDNConfigs']);
unset($_SESSION['YSF2P25Configs']);
unset($_SESSION['DMR2YSFConfigs']);
unset($_SESSION['DMR2NXDNConfigs']);
unset($_SESSION['APRSGatewayConfigs']);
unset($_SESSION['NXDNGatewayConfigs']);
unset($_SESSION['P25GatewayConfigs']);


// Load a bunch of config data.
if (file_exists('/etc/bmapi.key')) {
    $configBMapi = parse_ini_file('/etc/bmapi.key', true);
    if (isset($configBMapi['key']['apikey']) && !empty($configBMapi['key']['apikey'])) {
	$_SESSION['BMAPIKey'] = $configBMapi['key']['apikey'];
	// Check the BM API Key
	if ( strlen($_SESSION['BMAPIKey']) <= 20 ) { unset($_SESSION['BMAPIKey']); }
    }
}
if (file_exists('/etc/dapnetapi.key')) {
    $_SESSION['DAPNETAPIKey'] = parse_ini_file('/etc/dapnetapi.key', true);
}
$_SESSION['PiStarRelease'] = parse_ini_file('/etc/pistar-release', true);
$_SESSION['MMDVMHostConfigs'] = getMMDVMConfigContent();
$_SESSION['ircDDBConfigs'] = getNoSectionsConfigContent($gatewayConfigPath);
$_SESSION['DStarRepeaterConfigs'] = getNoSectionsConfigContent('/etc/dstarrepeater');
$_SESSION['DMRGatewayConfigs'] = parse_ini_file('/etc/dmrgateway', true);
$_SESSION['YSFGatewayConfigs'] = parse_ini_file('/etc/ysfgateway', true);
$_SESSION['DAPNETGatewayConfigs'] = parse_ini_file('/etc/dapnetgateway', true);
if (file_exists('/etc/ysf2dmr')) {
    $_SESSION['YSF2DMRConfigs'] = parse_ini_file('/etc/ysf2dmr', true);
}
if (file_exists('/etc/ysf2nxdn')) {
    $_SESSION['YSF2NXDNConfigs'] = parse_ini_file('/etc/ysf2nxdn', true);
}
if (file_exists('/etc/ysf2p25')) {
    $_SESSION['YSF2P25Configs'] = parse_ini_file('/etc/ysf2p25', true);
}
if (file_exists('/etc/dmr2ysf')) {
    $_SESSION['DMR2YSFConfigs'] = parse_ini_file('/etc/dmr2ysf', true);
}
if (file_exists('/etc/dmr2nxdn')) {
    $_SESSION['DMR2NXDNConfigs'] = parse_ini_file('/etc/dmr2nxdn', true);
}
if (file_exists('/etc/aprsgateway')) {
    $_SESSION['APRSGatewayConfigs'] = parse_ini_file('/etc/aprsgateway', true);
}
if (file_exists('/etc/nxdngateway')) {
    $_SESSION['NXDNGatewayConfigs'] = parse_ini_file('/etc/nxdngateway', true);
}
if (file_exists('/etc/p25gateway')) {
    $_SESSION['P25GatewayConfigs'] = parse_ini_file('/etc/p25gateway', true);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
<head>
    <meta name="robots" content="index" />
    <meta name="robots" content="follow" />
    <meta name="language" content="English" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo "<meta name=\"generator\" content=\"$progname $rev\" />\n"; ?>
    <meta name="Author" content="Hans-J. Barthen (DL5DI), Kim Huebel (DG9VH) and Andy Taylor (MW0MWZ)" />
    <meta name="Description" content="Pi-Star Dashboard" />
    <meta name="KeyWords" content="MW0MWZ,MMDVMHost,ircDDBGateway,D-Star,ircDDB,Pi-Star,Blackwood,Wales,DL5DI,DG9VH" />
    <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="pragma" content="no-cache" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <title><?php echo "$MYCALL"." - ".$lang['digital_voice']." ".$lang['dashboard'];?></title>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
<?php include_once "config/browserdetect.php"; ?>
    <script type="text/javascript" src="/jquery.min.js"></script>
    <script type="text/javascript" src="/jquery-floatThead.min.js"></script>
    <script type="text/javascript" src="/functions.js"></script>
    <script type="text/javascript">
      $.ajaxSetup({ cache: false });
    </script>
    <link href="/featherlight.css" type="text/css" rel="stylesheet" />
    <script src="/featherlight.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
    <div class="container">
	<div class="header">
	    <div style="font-size: 8px; text-align: left; padding-left: 8px; float: left;">Hostname: <?php echo exec('cat /etc/hostname'); ?></div><div style="font-size: 8px; text-align: right; padding-right: 8px;">Pi-Star:<?php echo $_SESSION['PiStarRelease']['Pi-Star']['Version']?> / <?php echo $lang['dashboard'].": ".$version; ?></div>
	    <h1>Pi-Star <?php echo $lang['digital_voice']." ".$lang['dashboard_for']." ".$_SESSION['MYCALL']; ?></h1>
	    
	    <p>
 		<div class="navbar">
		    <a class="menuconfig" href="/admin/configure.php"><?php echo $lang['configuration'];?></a>
		    <?php if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
			echo ' <a class="menuupdate" href="/admin/update.php">'.$lang['update'].'</a>'."\n";
			echo ' <a class="menupower" href="/admin/power.php">'.$lang['power'].'</a>'."\n";
			echo ' <a class="menusysinfo" href="/admin/sysinfo.php">Sysinfo</a>'."\n";
			echo ' <a class="menulogs" href="/admin/live_modem_log.php">'.$lang['live_logs'].'</a>'."\n";
		    } ?>
		    <a class="menuadmin" href="/admin/"><?php echo $lang['admin'];?></a>
		    <a class="menudashboard" href="/"><?php echo $lang['dashboard'];?></a>
		</div> 
	    </p>
	</div>
	
<?php

// Output some default features
if ($_SERVER["PHP_SELF"] == "/index.php")
{
    echo '<div class="contentwide">'."\n";
    echo '<script type="text/javascript">'."\n";
    echo 'function reloadHwInfo(){'."\n";
    echo '  $("#hwInfo").load("/dstarrepeater/hw_info.php",function(){ setTimeout(reloadHwInfo, 15000) });'."\n";
    echo '}'."\n";
    echo 'setTimeout(reloadHwInfo, 15000);'."\n";
    echo '$(window).trigger(\'resize\');'."\n";
    echo '</script>'."\n";
    echo '<div id="hwInfo">'."\n";
    include 'dstarrepeater/hw_info.php';
    echo '</div>'."\n";
    echo '</div>'."\n";
    echo '<br />'."\n";
}
else if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
    echo '<div class="contentwide">'."\n";
    echo '<script type="text/javascript">'."\n";
    echo 'function reloadHwInfo(){'."\n";
    echo '  $("#hwInfo").load("/dstarrepeater/hw_info.php",function(){ setTimeout(reloadHwInfo, 15000) });'."\n";
    echo '}'."\n";
    echo 'function reloadSysInfo(){'."\n";
    echo '  $("#sysInfo").load("/dstarrepeater/system.php",function(){ setTimeout(reloadSysInfo,15000) });'."\n";
    echo '}'."\n";
    echo 'setTimeout(reloadHwInfo,15000);'."\n";
    echo 'setTimeout(reloadSysInfo,15000);'."\n";
    echo '$(window).trigger(\'resize\');'."\n";
    echo '</script>'."\n";
    echo '<div id="hwInfo">'."\n";
    include 'dstarrepeater/hw_info.php';			// Basic System Info
    echo '</div>'."\n";
    echo '<div id="sysInfo">'."\n";
    include 'dstarrepeater/system.php';				// Basic System Info
    echo '</div>'."\n";
    echo '</div>'."\n";
}

// First lets figure out if we are in MMDVMHost mode, or dstarrepeater mode;
if (file_exists('/etc/dstar-radio.mmdvmhost')) {
//include 'config/config.php';					// MMDVMDash Config
//	include_once 'mmdvmhost/tools.php';				// MMDVMDash Tools

	echo '<div class="nav">'."\n";					// Start the Side Menu
	echo '<script type="text/javascript">'."\n";
	echo 'function reloadRepeaterInfo(){'."\n";
	echo '  $("#repeaterInfo").load("/mmdvmhost/repeaterinfo.php",function(){ setTimeout(reloadRepeaterInfo,1000) });'."\n";
	echo '}'."\n";
	echo 'setTimeout(reloadRepeaterInfo,1000);'."\n";
	echo '$(window).trigger(\'resize\');'."\n";
	echo '</script>'."\n";
	echo '<div id="repeaterInfo">'."\n";
	include 'mmdvmhost/repeaterinfo.php';				// MMDVMDash Repeater Info
	echo '</div>'."\n";
	echo '</div>'."\n";

	echo '<div class="content">'."\n";

	$testMMDVModeDSTARnet = getConfigItem("D-Star Network", "Enable", $_SESSION['MMDVMHostConfigs']);
        if ( $testMMDVModeDSTARnet == 1 ) {				// If D-Star network is enabled, add these extra features.

	if ($_SERVER["PHP_SELF"] == "/admin/index.php") { 		// Admin Only Option
		echo '<script type="text/javascript">'."\n";
		echo 'function reloadrefLinks(){'."\n";
		echo '  $("#refLinks").load("/dstarrepeater/active_reflector_links.php",function(){ setTimeout(reloadrefLinks,2500) });'."\n";
		echo '}'."\n";
		echo 'setTimeout(reloadrefLinks,2500);'."\n";
		echo '$(window).trigger(\'resize\');'."\n";
		echo '</script>'."\n";
		echo '<div id="refLinks">'."\n";
		include 'dstarrepeater/active_reflector_links.php';	// dstarrepeater gateway config
	        echo '</div>'."\n";
	        echo '<br />'."\n";

		include 'dstarrepeater/link_manager.php';		// D-Star Link Manager
		echo "<br />\n";
		}

        echo '<script type="text/javascript">'."\n";
        echo 'function reloadcssConnections(){'."\n";
        echo '  $("#cssConnects").load("/dstarrepeater/css_connections.php",function(){ setTimeout(reloadcssConnections,15000) });'."\n";
        echo '}'."\n";
        echo 'setTimeout(reloadcssConnections,15000);'."\n";
	echo '$(window).trigger(\'resize\');'."\n";
        echo '</script>'."\n";
        echo '<div id="cssConnects">'."\n";
	include 'dstarrepeater/css_connections.php';			// dstarrepeater gateway config
	echo '</div>'."\n";
	}

	if ($_SERVER["PHP_SELF"] == "/admin/index.php") { 		// Admin Only Option
		echo '<script type="text/javascript">'."\n";
        	echo 'function reloadbmConnections(){'."\n";
        	echo '  $("#bmConnects").load("/mmdvmhost/bm_links.php",function(){ setTimeout(reloadbmConnections,15000) });'."\n";
        	echo '}'."\n";
        	echo 'setTimeout(reloadbmConnections,15000);'."\n";
		echo '$(window).trigger(\'resize\');'."\n";
        	echo '</script>'."\n";
        	echo '<div id="bmConnects">'."\n";
		include 'mmdvmhost/bm_links.php';                       // BM Links
		echo '</div>'."\n";
	}
	if ($_SERVER["PHP_SELF"] == "/admin/index.php") {               // Admin Only Options
                include 'mmdvmhost/bm_manager.php';                     // BM DMR Link Manager
        }

	if ($_SERVER["PHP_SELF"] == "/admin/index.php") { 		// Admin Only Option
		echo '<script type="text/javascript">'."\n";
        	echo 'function reloadtgifConnections(){'."\n";
        	echo '  $("#tgifConnects").load("/mmdvmhost/tgif_links.php",function(){ setTimeout(reloadtgifConnections,15000) });'."\n";
        	echo '}'."\n";
        	echo 'setTimeout(reloadtgifConnections,15000);'."\n";
		echo '$(window).trigger(\'resize\');'."\n";
        	echo '</script>'."\n";
        	echo '<div id="tgifConnects">'."\n";
		include 'mmdvmhost/tgif_links.php';			// TGIF Links
		echo '</div>'."\n";
	}
	if ($_SERVER["PHP_SELF"] == "/admin/index.php") {               // Admin Only Options
                include 'mmdvmhost/tgif_manager.php';			// TGIF DMR Link Manager
        }

	$testMMDVModeYSFnet = getConfigItem("System Fusion Network", "Enable", $_SESSION['MMDVMHostConfigs']);
        if ( $testMMDVModeYSFnet == 1 ) {				// If YSF network is enabled, add these extra features.
		if ($_SERVER["PHP_SELF"] == "/admin/index.php") { 	// Admin Only Option
			include 'mmdvmhost/ysf_manager.php';		// YSF Links
		}
	}
	$testMMDVModeP25net = getConfigItem("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
        if ( $testMMDVModeP25net == 1 ) {				// If P25 network is enabled, add these extra features.
		if ($_SERVER["PHP_SELF"] == "/admin/index.php") { 	// Admin Only Option
			include 'mmdvmhost/p25_manager.php';		// P25 Links
		}
	}
	$testMMDVModeNXDNnet = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
        if ( $testMMDVModeNXDNnet == 1 ) {				// If NXDN network is enabled, add these extra features.
		if ($_SERVER["PHP_SELF"] == "/admin/index.php") { 	// Admin Only Option
			include 'mmdvmhost/nxdn_manager.php';		// NXDN Links
		}
	}

	echo '<script type="text/javascript">'."\n";
	echo 'var lhto;'."\n";
	echo 'var ltxto'."\n";

	echo 'function reloadLastHeard(){'."\n";
	echo '  $("#lastHeard").load("/mmdvmhost/lh.php",function(){ lhto = setTimeout(reloadLastHeard,1500) });'."\n";
	echo '}'."\n";

	echo 'function reloadLocalTX(){'."\n";
	echo '  $("#localTxs").load("/mmdvmhost/localtx.php",function(){ ltxto = setTimeout(reloadLocalTX,1500) });'."\n";
	echo '}'."\n";

    	echo 'function setLHAutorefresh(obj) {'."\n";
    	echo '    if (obj.checked) {'."\n";
    	echo '        lhto = setTimeout(reloadLastHeard,1500);'."\n";
    	echo '    }'."\n";
    	echo '    else {'."\n";
    	echo '        clearTimeout(lhto);'."\n";
    	echo '    }'."\n";
    	echo '}'."\n";

	echo 'function setLocalTXAutorefresh(obj) {'."\n";
    	echo '    if (obj.checked) {'."\n";
    	echo '        ltxto = setTimeout(reloadLocalTX,1500);'."\n";
    	echo '    }'."\n";
    	echo '    else {'."\n";
    	echo '        clearTimeout(ltxto);'."\n";
    	echo '    }'."\n";
    	echo '}'."\n";
	
	echo 'lhto = setTimeout(reloadLastHeard,1500);'."\n";
	echo 'ltxto = setTimeout(reloadLocalTX,1500);'."\n";
	echo '$(window).trigger(\'resize\');'."\n";
	echo '</script>'."\n";
	echo '<div id="lastHeard">'."\n";
	include 'mmdvmhost/lh.php';					// MMDVMDash Last Herd
	echo '</div>'."\n";
	echo "<br />\n";

	echo '<div id="localTxs">'."\n";
	include 'mmdvmhost/localtx.php';				// MMDVMDash Local Trasmissions
	echo '</div>'."\n";
	
	// If POCSAG is enabled, show the information pannel
	$testMMDVModePOCSAG = getConfigItem("POCSAG Network", "Enable", $_SESSION['MMDVMHostConfigs']);
	if ( $testMMDVModePOCSAG == 1 ) {
		if ($_SERVER["PHP_SELF"] == "/admin/index.php") {  // Admin Only Options
			echo "<br />\n";
            echo '<div id="dapnetMsgr">'."\n";
            include 'mmdvmhost/dapnet_messenger.php';
            echo '</div>'."\n";
        }
		$myOrigin = ($_SERVER["PHP_SELF"] == "/admin/index.php" ? "admin" : "other");

		echo '<script type="text/javascript">'."\n";
		echo 'var pagesto;'."\n";
		echo 'function setPagesAutorefresh(obj) {'."\n";
	        echo '    if (obj.checked) {'."\n";
	        echo '        pagesto = setTimeout(reloadPages, 5000, "?origin='.$myOrigin.'");'."\n";
	        echo '    }'."\n";
	        echo '    else {'."\n";
	        echo '        clearTimeout(pagesto);'."\n";
	        echo '    }'."\n";
                echo '}'."\n";
		echo 'function reloadPages(OptStr){'."\n";
		echo '    $("#Pages").load("/mmdvmhost/pages.php"+OptStr, function(){ pagesto = setTimeout(reloadPages, 5000, "?origin='.$myOrigin.'") });'."\n";
		echo '}'."\n";
		echo 'pagesto = setTimeout(reloadPages, 5000, "?origin='.$myOrigin.'");'."\n";
		echo '$(window).trigger(\'resize\');'."\n";
		echo '</script>'."\n";
		echo "<br />\n";
		echo '<div id="Pages">'."\n";
		include 'mmdvmhost/pages.php';				// POCSAG Messages
		echo '</div>'."\n";
	}

}
else if (file_exists('/etc/dstar-radio.dstarrepeater')) {
        if (file_exists('/etc/aprsgateway')) {
	    $aprsGatewayConfigFile = '/etc/aprsgateway';
	    if (fopen($aprsGatewayConfigFile,'r')) { $configaprsgateway = parse_ini_file($aprsGatewayConfigFile, true); }
	}
    
        echo '<div class="contentwide">'."\n";
	include 'dstarrepeater/gateway_software_config.php';		// dstarrepeater gateway config
	echo '<script type="text/javascript">'."\n";
	echo 'function reloadrefLinks(){'."\n";
	echo '  $("#refLinks").load("/dstarrepeater/active_reflector_links.php",function(){ setTimeout(reloadrefLinks,2500) });'."\n";
	echo '}'."\n";
	echo 'setTimeout(reloadrefLinks,2500);'."\n";
	echo '$(window).trigger(\'resize\');'."\n";
	echo '</script>'."\n";
        echo '<br />'."\n";
	echo '<div id="refLinks">'."\n";
	include 'dstarrepeater/active_reflector_links.php';		// dstarrepeater gateway config
        echo '</div>'."\n";
        echo '<br />'."\n";
	if ($_SERVER["PHP_SELF"] == "/admin/index.php") {		// Admin Only Options
		include 'dstarrepeater/link_manager.php';		// D-Star Link Manager
		echo "<br />\n";
		}

	echo '<script type="text/javascript">'."\n";
        echo 'function reloadcssConnections(){'."\n";
        echo '  $("#cssConnects").load("/dstarrepeater/css_connections.php",function(){ setTimeout(reloadcssConnections,15000) });'."\n";
        echo '}'."\n";
        echo 'setTimeout(reloadcssConnections,15000);'."\n";
	echo '$(window).trigger(\'resize\');'."\n";
        echo '</script>'."\n";
        echo '<div id="cssConnects">'."\n";
	include 'dstarrepeater/css_connections.php';			// dstarrepeater gateway config
	echo '</div>'."\n";

	echo '<script type="text/javascript">'."\n";
	echo 'function reloadLocalTx(){'."\n";
	echo '  $("#localTx").load("/dstarrepeater/local_tx.php",function(){ setTimeout(reloadLocalTx,3000) });'."\n";
	echo '}'."\n";
	echo 'setTimeout(reloadLocalTx,3000);'."\n";
	echo 'function reloadLastHeard(){'."\n";
	echo '  $("#lhdstar").load("/dstarrepeater/last_herd.php",function(){ setTimeout(reloadLastHeard,3000) });'."\n";
	echo '}'."\n";
	echo 'setTimeout(reloadLastHeard,3000);'."\n";
	echo '$(window).trigger(\'resize\');'."\n";
	echo '</script>'."\n";
	echo '<div id="lhdstar">'."\n";
	include 'dstarrepeater/last_herd.php';				//dstarrepeater Last Herd
        echo '</div>'."\n";
	echo "<br />\n";
	echo '<div id="localTx">'."\n";
	include 'dstarrepeater/local_tx.php';				//dstarrepeater Local Transmissions
        echo '</div>'."\n";
        echo '<br />'."\n";

} else {
	echo '<div class="contentwide">'."\n";
	//We dont know what mode we are in - fail...
	echo "<H1>No Mode Defined...</H1>\n";
	echo "<p>I don't know what mode I am in, you probaly just need to configure me.</p>\n";
	echo "<p>You will be re-directed to the configuration portal in 10 secs</p>\n";
	echo "<p>In the mean time, you might want to register on the support<br />\n";
	echo "page here: <a href=\"https://www.facebook.com/groups/pistarusergroup/\" target=\"_new\">https://www.facebook.com/groups/pistarusergroup/</a><br />\n";
	echo "or the Support forum here: <a href=\"https://forum.pistar.uk/\" target=\"_new\">https://forum.pistar.uk/</a></p>\n";
	echo '<script type="text/javascript">setTimeout(function() { window.location="/admin/configure.php";},10000);</script>'."\n";
}
?>
</div>

<div class="footer">
    <?php if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
	echo 'Pi-Star web config, &copy; Andy Taylor (MW0MWZ) 2014-'.date("Y").'<br />'."\n";
	echo 'Need help? Click <a style="color: #ffffff;" href="https://www.facebook.com/groups/pistarusergroup/" target="_new">here for the Support Group</a><br />'."\n";
	echo 'or Click <a style="color: #ffffff;" href="https://forum.pistar.uk/" target="_new">here to join the Support Forum</a><br />'."\n";
    }
    else {
	echo 'Pi-Star / Pi-Star Dashboard, &copy; Andy Taylor (MW0MWZ) 2014-'.date("Y").'<br />'."\n";
	echo 'ircDDBGateway Dashboard by Hans-J. Barthen (DL5DI),<br />'."\n";
	echo 'MMDVMDash developed by Kim Huebel (DG9VH), <br />'."\n";
	echo 'Need help? Click <a style="color: #ffffff;" href="https://www.facebook.com/groups/pistarusergroup/" target="_new">here for the Facebook Group</a><br />'."\n";
	echo 'or Click <a style="color: #ffffff;" href="https://forum.pistar.uk/" target="_new">here to join the Support Forum</a><br />'."\n";
	echo 'Get your copy of Pi-Star from <a style="color: #ffffff;" href="http://www.pistar.uk/downloads/" target="_new">here</a>.<br />'."\n";
    } ?>
</div>

</div>
</body>
</html>
