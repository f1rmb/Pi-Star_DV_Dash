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

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

// Check if DMR is Enabled
$testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);

if ( $testMMDVModeDMR == 1 ) {
    $bmEnabled = true;

    // Get the current DMR Master from the config
    $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
    if ( $dmrMasterHost == '127.0.0.1' ) {
	$dmrMasterHost = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'];
	$bmEnabled = ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'] != "0" ? true : false);
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Id'])) { $dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Id']; }
    }
    else if (getConfigItem("DMR", "Id", $_SESSION['MMDVMHostConfigs'])) {
	$dmrID = getConfigItem("DMR", "Id", $_SESSION['MMDVMHostConfigs']);
    }
    else {
	$dmrID = getConfigItem("General", "Id", $_SESSION['MMDVMHostConfigs']);
    }

    // Store the DMR Master IP, we will need this for the JSON lookup
    $dmrMasterHostIP = $dmrMasterHost;

    // Make sure the master is a BrandMeister Master
    if (($dmrMasterFile = fopen("/usr/local/etc/DMR_Hosts.txt", "r")) != FALSE) {
	while (!feof($dmrMasterFile)) {
            $dmrMasterLine = fgets($dmrMasterFile);
            $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
            if ((strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
		if ($dmrMasterHost == $dmrMasterHostF[2]) { $dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]); }
            }
	}
	fclose($dmrMasterFile);
    }

    if ((substr($dmrMasterHost, 0, 3) == "BM ") && ($bmEnabled == true) && isset($_SESSION['BMAPIKey'])) {
	$bmAPIkey = $_SESSION['BMAPIKey'];
	// Check the BM API Key
	if (isset($bmAPIkey) && strlen($bmAPIkey) <= 20) {
	    unset($bmAPIkey);
	}
	else if (isset($bmAPIkey) && strlen($bmAPIkey) >= 200) {
	    $bmAPIkeyV2 = $bmAPIkey;
	    unset($bmAPIkey);
	}

	$opts = array(
	    'http' => array(
		'timeout' => 2,
		'header'  => 'User-Agent: Pi-Star '.$_SESSION['PiStarRelease']['Pi-Star']['Version'].'-f1rmb Dashboard for '.$dmrID
	    ),
	);

	// Hack for old Jessie
	if ($_SESSION['PiStarRelease']['Pi-Star']['Version'] < "4.1") {
	    $noverify = array(
		'ssl' => array(
		    'verify_peer' => false,
		    'verify_peer_name' => false,
		),
	    );

	    $opts = array_merge($opts, $noverify);
	}

	// Use BM API to get information about current TGs
	$jsonContext = stream_context_create($opts); // Add Timout and User Agent to include DMRID
	if (isset($bmAPIkeyV2)) {
	    $json = json_decode(@file_get_contents("https://api.brandmeister.network/v2/device/$dmrID/profile", true, $jsonContext));
	}
	else {
	    $json = json_decode(@file_get_contents("https://api.brandmeister.network/v1.0/repeater/?action=PROFILE&q=$dmrID", true, $jsonContext));
	}

	// Set some Variable
	$bmStaticTGList = "";
	$bmDynamicTGList = "";

	// Pull the information form JSON
	if (isset($json->staticSubscriptions)) { $bmStaticTGListJson = $json->staticSubscriptions;
            foreach($bmStaticTGListJson as $staticTG) {
                if (getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) && $staticTG->slot == "1") {
                    $bmStaticTGList .= "TG".$staticTG->talkgroup."(".$staticTG->slot.") ";
                }
                else if (getConfigItem("DMR Network", "Slot2", $_SESSION['MMDVMHostConfigs']) && $staticTG->slot == "2") {
                    $bmStaticTGList .= "TG".$staticTG->talkgroup."(".$staticTG->slot.") ";
                }
                else if (getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == "0" && getConfigItem("DMR Network", "Slot2", $_SESSION['MMDVMHostConfigs']) && $staticTG->slot == "0") {
                    $bmStaticTGList .= "TG".$staticTG->talkgroup." ";
                }
            }
            $bmStaticTGList = wordwrap($bmStaticTGList, 15, "<br />\n");
            if (preg_match('/TG/', $bmStaticTGList) == false) { $bmStaticTGList = "None"; }
        }
	else { $bmStaticTGList = "None"; }
	if (isset($json->dynamicSubscriptions)) { $bmDynamicTGListJson = $json->dynamicSubscriptions;
            foreach($bmDynamicTGListJson as $dynamicTG) {
                if (getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) && $dynamicTG->slot == "1") {
                    $bmDynamicTGList .= "TG".$dynamicTG->talkgroup."(".$dynamicTG->slot.") ";
                }
                else if (getConfigItem("DMR Network", "Slot2", $_SESSION['MMDVMHostConfigs']) && $dynamicTG->slot == "2") {
                    $bmDynamicTGList .= "TG".$dynamicTG->talkgroup."(".$dynamicTG->slot.") ";
                }
                else if (getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == "0" && getConfigItem("DMR Network", "Slot2", $_SESSION['MMDVMHostConfigs']) && $dynamicTG->slot == "0") {
                    $bmDynamicTGList .= "TG".$dynamicTG->talkgroup." ";
                }
            }
            $bmDynamicTGList = wordwrap($bmDynamicTGList, 15, "<br />\n");
            if (preg_match('/TG/', $bmDynamicTGList) == false) { $bmDynamicTGList = "None"; }
        } else { $bmDynamicTGList = "None"; }

	echo '<b>Active BrandMeister Connections</b>
  <table>
    <tr>
      <th><a class=tooltip href="#">'.$lang['bm_master'].'<span><b>Connected Master</b></span></a></th>
      <th><a class=tooltip href="#">Repeater ID<span><b>The ID for this Repeater/Hotspot</b></span></a></th>
      <th><a class=tooltip href="#">Static TGs<span><b>Statically linked talkgroups</b></span></a></th>
      <th><a class=tooltip href="#">Dynamic TGs<span><b>Dynamically linked talkgroups</b></span></a></th>
    </tr>'."\n";

	echo '    <tr>'."\n";
	echo '     <td>'.$dmrMasterHost.'</td>';
	echo '     <td>'.$dmrID.'</td>';
	echo '     <td>'.$bmStaticTGList.'</td>';
	echo '     <td>'.$bmDynamicTGList.'</td>';
	echo '    </tr>'."\n";
	echo '  </table>'."\n";
	echo '  <br />'."\n";
    }
}
?>
