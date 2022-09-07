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
	// OK this is Brandmeister, get some config and output the HTML

	// If there is a BM API Key
	$bmAPIkey = $_SESSION['BMAPIKey'];

	// Check the BM API Key
	if (isset($bmAPIkey) && strlen($bmAPIkey) <= 20) {
	    unset($bmAPIkey);
	}
	else if (isset($bmAPIkey) && strlen($bmAPIkey) >= 200) {
	    $bmAPIkeyV2 = $bmAPIkey;
	    unset($bmAPIkey);
	}


	if ( !empty($_POST) && ( isset($_POST["dropDyn"]) || isset($_POST["dropQso"]) || isset($_POST["tgSubmit"]))) {  // Data has been posted for this page
	    if (isset($bmAPIkey)) {
		$bmAPIurl = 'https://api.brandmeister.network/v1.0/repeater/';
		// Are we a repeater
		if ( getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == "0" ) {
		    unset($_POST["TS"]);
		    $targetSlot = "0";
		}
		else {
		    $targetSlot = $_POST["TS"];
		}
		// Figure out what has been posted
		if (isset($_POST["dropDyn"])) { $bmAPIurl = $bmAPIurl."setRepeaterTarantool.php?action=dropDynamicGroups&slot=".$targetSlot."&q=".$dmrID; }
		if (isset($_POST["dropQso"])) { $bmAPIurl = $bmAPIurl."setRepeaterDbus.php?action=dropCallRoute&slot=".$targetSlot."&q=".$dmrID; }
		if ( (isset($_POST["TGmgr"])) && ($_POST["TGmgr"] == "ADD") && (isset($_POST["tgSubmit"])) ) { $bmAPIurl = $bmAPIurl."talkgroup/?action=ADD&id=".$dmrID; }
		if ( (isset($_POST["TGmgr"])) && ($_POST["TGmgr"] == "DEL") && (isset($_POST["tgSubmit"])) ) { $bmAPIurl = $bmAPIurl."talkgroup/?action=DEL&id=".$dmrID; }
		if ( (isset($_POST["tgNr"])) && (isset($_POST["tgSubmit"])) ) { $targetTG = preg_replace("/[^0-9]/", "", $_POST["tgNr"]); }
		// Build the Data
		if ( (!isset($_POST["dropDyn"])) && (!isset($_POST["dropQso"])) && isset($targetTG) ) {
		    if (isset($_POST["tgSubmit"])) {
			$postDataTG = array(
			    'talkgroup' => $targetTG,
			    'timeslot' => $targetSlot,
			);
		    }
		}
		// Build the Query
		$postData = '';
		if (isset($_POST["tgSubmit"])) { $postData = http_build_query($postDataTG); }
		$postHeaders = array(
		    'Content-Type: application/x-www-form-urlencoded',
		    'Content-Length: '.strlen($postData),
		    'Authorization: Basic '.base64_encode($bmAPIkey.':'),
		    'User-Agent: Pi-Star '.$_SESSION['PiStarRelease']['Pi-Star']['Version'].'-f1rmb Dashboard for '.$dmrID,
		);

		$opts = array(
		    'http' => array(
			'header'  => $postHeaders,
			'method'  => 'POST',
			'content' => $postData,
			'password' => '',
			'success' => '',
			'timeout' => 2,
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

		$context = stream_context_create($opts);
		$result = @file_get_contents($bmAPIurl, false, $context);
		$feeback=json_decode($result);
		// Output to the browser
		echo '<b>BrandMeister Manager</b>'."\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td>";
		//echo "Sending command to BrandMeister API";
		if (isset($feeback)) {
		    print "BrandMeister APIv1: ".$feeback->{'message'};
		}
		else {
		    print "BrandMeister APIv1: No Responce";
		}
		echo "</td></tr>\n</table>\n";
		echo "<br />\n";
		// Clean up...
		unset($_POST);
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
	    }
	    else if (isset($bmAPIkeyV2)) {
		$bmAPIurl = 'https://api.brandmeister.network/v2/device/';
		// Are we a repeater
		if ( getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == "0" ) {
		    unset($_POST["TS"]);
		    $targetSlot = "0";
		}
		else {
		    $targetSlot = $_POST["TS"];
		}

		// Set the API URLs
		if (isset($_POST["dropDyn"])) { $bmAPIurl = $bmAPIurl.$dmrID."/action/dropDynamicGroups/".$targetSlot; $method = "GET"; }
		if (isset($_POST["dropQso"])) { $bmAPIurl = $bmAPIurl.$dmrID."/action/dropCallRoute/".$targetSlot; $method = "GET"; }
		if ( (isset($_POST["tgNr"])) && (isset($_POST["tgSubmit"])) ) { $targetTG = preg_replace("/[^0-9]/", "", $_POST["tgNr"]); }
		if ( ($_POST["TGmgr"] == "ADD") && (isset($_POST["tgSubmit"])) ) { $bmAPIurl = $bmAPIurl.$dmrID."/talkgroup/"; $method = "POST"; }
		if ( ($_POST["TGmgr"] == "DEL") && (isset($_POST["tgSubmit"])) ) { $bmAPIurl = $bmAPIurl.$dmrID."/talkgroup/".$targetSlot."/".$targetTG; $method = "DELETE"; }
		// Build the Data
		if ( (!isset($_POST["dropDyn"])) && (!isset($_POST["dropQso"])) && isset($targetTG) && $_POST["TGmgr"] == "ADD" ) {
		    $postDataTG = array(
			'slot' => $targetSlot,
			'group' => $targetTG
		    );
		}
		// Build the Query
		$postData = '';
		if ($_POST["TGmgr"] == "ADD") { $postData = json_encode($postDataTG); }
		$postHeaders = array(
		    'Content-Type: accept: application/json',
		    'Content-Length: '.strlen($postData),
		    'Authorization: Bearer '.$bmAPIkeyV2,
		    'User-Agent: Pi-Star '.$_SESSION['PiStarRelease']['Pi-Star']['Version'].'-f1rmb Dashboard for '.$dmrID,
		);

		$opts = array(
		    'http' => array(
			'header'  => $postHeaders,
			'method'  => $method,
			'content' => $postData,
			'password' => '',
			'success' => '',
			'timeout' => 2,
		    ),
		);

		if ($_SESSION['PiStarRelease']['Pi-Star']['Version'] < "4.1")
		{
		    $nossl = array(
			'ssl' => array(
			    'verify_peer' => false,
			    'verify_peer_name' => false,
			),
		    );

		    $opts = array_merge($opts, $nossl);
		}

		$context = stream_context_create($opts);
		$result = @file_get_contents($bmAPIurl, false, $context);
		$feeback=json_decode($result);
		// Output to the browser
		echo '<b>BrandMeister Manager</b>'."\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td>";
		//if (isset($feeback)) {
		//    print "BrandMeister APIv2: ".$feeback->{'message'};
		//}
		//else {
		//    print "BrandMeister APIv2: No Responce";
		//}
		if (isset($feeback)) {
		    print "BrandMeister APIv2: OK";
		}
		else {
		    print "BrandMeister APIv2: No Responce";
		}

		echo "</td></tr>\n</table>\n";
		echo "<br />\n";
		// Clean up...
		unset($_POST);
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
	    }
	}
	else { // Do this when we are not handling post data
	    if (isset($_SESSION['BMAPIKey'])) {
		echo '<a href="https://brandmeister.network/?page=hotspot&amp;id='.$dmrID.'" target="_new" style="color:inherit;" ><b>BrandMeister Manager</b></a>'."\n";
		echo '<form action="'.htmlentities($_SERVER['PHP_SELF']).'" method="post">'."\n";
		echo '<table>
      <tr>
        <th colspan="4"><a class=tooltip href="#">Tools<span><b>Tools</b></span></a></th>
      </tr>'."\n";
		echo '    <tr>';
		echo '<td colspan="4"><input style="width: 180px; margin-right: 20px; padding: 0px;" type="submit" value="Drop QSO" title="Drop current QSO" name="dropQso" /><input style="width: 180px; margin-left: 20px; padding: 0px;" type="submit" value="Drop All Dyn." title="Drop all dynamic groups" name="dropDyn" /></td>';
		echo '</tr>'."\n";
		echo '<tr>
        <th><a class=tooltip href="#">Static Talkgroup<span><b>Enter the Talkgroup number</b></span></a></th>
        <th><a class=tooltip href="#">Slot<span><b>Where to link/unlink</b></span></a></th>
        <th><a class=tooltip href="#">Add / Remove<span><b>Add or Remove</b></span></a></th>
        <th><a class=tooltip href="#">Action<span><b>Take Action</b></span></a></th>
      </tr>'."\n";
		echo '    <tr>';
		echo '<td><input type="text" id="tgNr" name="tgNr" size="10" maxlength="7" oninput="enableOnNonEmpty(\'tgNr\', \'tgSubmit\', \'tgAdd\', \'tgDel\'); return false;"/></td>';
		echo '<td><input type="radio" id="ts1" name="TS" value="1" '.((getConfigItem("General", "Duplex", $_SESSION['MMDVMHostConfigs']) == "1") ? '' : 'disabled').'/><label for="ts1"/>TS1</label> <input type="radio" id="ts2" name="TS" value="2" checked="checked"/><label for="ts2"/>TS2</td>';
		echo '<td><input type="radio" id="tgAdd" name="TGmgr" value="ADD" checked="checked" disabled/><label for="tgAdd">Add</label> <input type="radio" id="tgDel" name="TGmgr" value="DEL" disabled /><label for="tgDel">Delete</label></td>';
		echo '<td><input type="submit" value="Modify Static" id="tgSubmit" name="tgSubmit" disabled/></td>';
		echo '</tr>'."\n";
		echo '  </table>'."\n";
		echo '  <br />'."\n";
	    }
	}
    }
}

?>
