<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

// Set some Variable
$repeaterid = "";
$slot1tg = "";
$slot2tg = "";
$dmrID = "";

// Check if DMR is Enabled
$testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);

if ( $testMMDVModeDMR == 1 ) {
    // Get the current DMR Master from the config
    $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
    if ( $dmrMasterHost == '127.0.0.1' ) {
	// DMRGateway, need to check each config
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 2']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 3']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 4']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 5']['Id'];
	    }
	}
    }
    else if ( $dmrMasterHost == 'tgif.network' ) {
	// MMDVMHost Connected directly to TGIF, get the ID form here
	if (getConfigItem("DMR", "Id", $_SESSION['MMDVMHostConfigs'])) {
	    $dmrID = getConfigItem("DMR", "Id", $_SESSION['MMDVMHostConfigs']);
	} else {
	    $dmrID = getConfigItem("General", "Id", $_SESSION['MMDVMHostConfigs']);
	}
    }
    
    // Use TGIF API to get information about current TGs
    $jsonContext = stream_context_create(array('http'=>array('timeout' => 2))); // Add Timeout
    $json = json_decode(@file_get_contents("http://tgif.network/RPTRR/index.php?action=getsessions&repeater_id=".$dmrID."", true, $jsonContext));
    $jsonrows = count($json) - 1;
    
    // Pull the information form JSON
    if ((count($json) > 0) && ($dmrID > 0)) {
	for ($counter = 0; $counter <= $jsonrows; $counter++) {
	    $obj = $json[$counter];
	    if ($obj->repeater_id == $dmrID) {
		$repeaterid = $obj->repeater_id;
		if ($obj->tg0 == "4000") { $slot1tg = "None"; } else { $slot1tg = "TG".$obj->tg0; }
		if ($obj->tg  == "4000") { $slot2tg = "None"; } else { $slot2tg = "TG".$obj->tg;  }
	    }
	}
	
	echo '<b>Active TGIF Connections</b>
    <table>
      <tr>
        <th><a class=tooltip href="#">DMR Master<span><b>Connected Master</b></span></a></th>
        <th><a class=tooltip href="#">Repeater ID<span><b>The ID for this Repeater/Hotspot</b></span></a></th>
        <th><a class=tooltip href="#">Slot1 TG<span><b>TG linked to Slot 1</b></span></a></th>
        <th><a class=tooltip href="#">Slot2 TG<span><b>TG linked to Slot 2</b></span></a></th>
      </tr>'."\n";
	
	echo '    <tr>'."\n";
	echo '      <td>tgif.network</td>';
	echo '<td>'.$repeaterid.'</td>';
	echo '<td>'.$slot1tg.'</td>';
	echo '<td>'.$slot2tg.'</td>';
	echo '</tr>'."\n";
	echo '  </table>'."\n";
	echo '  <br />'."\n";
    }
}
?>
