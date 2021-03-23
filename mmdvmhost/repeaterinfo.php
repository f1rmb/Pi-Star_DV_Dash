<?php
if (isset($_COOKIE['PHPSESSID']))
{
    session_id($_COOKIE['PHPSESSID']); 
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION) || (count($_SESSION, COUNT_RECURSIVE) < 10)) {
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
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
require_once($_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php');


function FillConnectionStatus(&$destArray, $remoteEnabled, $remotePort) {
    if (($remoteEnabled == 1) && ($remotePort != 0)) {
	$remoteOutput = null;
	$remoteRetval = null;
	exec('/usr/local/bin/RemoteCommand '.$remotePort.' status', $remoteOutput, $remoteRetval);
	if (($remoteRetval == 0) && (count($remoteOutput) >= 2)) {
	    $tok = strtok($remoteOutput[1], " \n\t");
	    while ($tok !== false) {
		$keysValues = explode(":", $tok);
		$destArray[$keysValues[0]] = $keysValues[1];
		$tok = strtok(" \n\t");
	    }
	}
    }
}

function GetActiveConnectionStyle($masterStates, $key) {
    if (count($masterStates)) {
	if (isset($masterStates[$key])) {
	    if (($masterStates[$key] == "n/a") || ($masterStates[$key] == "disc")) {
		return "style=\"background: #b00; color: #500;\"";
	    }
	}
    }
    return "style=\"background: #ffffff;\"";
}


//
// Grab networks status from MMDVMHost and DMRGateway
//
$remoteMMDVMResults = [];
$remoteDMRGResults = [];

if (isProcessRunning("MMDVMHost")) {
    $cfgItemEnabled = getConfigItem("Remote Control", "Enable", $_SESSION['MMDVMHostConfigs']);
    $cfgItemPort = getConfigItem("Remote Control", "Port", $_SESSION['MMDVMHostConfigs']);
    FillConnectionStatus($remoteMMDVMResults, (isset($cfgItemEnabled) ? $cfgItemEnabled : 0), (isset($cfgItemPort) ? $cfgItemPort : 0));
}

if (isProcessRunning("DMRGateway")) {
    $remoteCommandEnabled = (isset($_SESSION['DMRGatewayConfigs']['Remote Control']) ? $_SESSION['DMRGatewayConfigs']['Remote Control']['Enable'] : 0);
    $remoteCommandPort = (isset($_SESSION['DMRGatewayConfigs']['Remote Control']) ? $_SESSION['DMRGatewayConfigs']['Remote Control']['Port'] : 0);
    FillConnectionStatus($remoteDMRGResults, $remoteCommandEnabled, $remoteCommandPort);
}

?>
<table>
    <tr><th colspan="2"><?php echo $lang['modes_enabled'];?></th></tr>
    <tr><?php showMode("D-Star", $_SESSION['MMDVMHostConfigs']);?><?php showMode("DMR", $_SESSION['MMDVMHostConfigs']);?></tr>
    <tr><?php showMode("System Fusion", $_SESSION['MMDVMHostConfigs']);?><?php showMode("P25", $_SESSION['MMDVMHostConfigs']);?></tr>
    <tr><?php showMode("YSF XMode", $_SESSION['MMDVMHostConfigs']);?><?php showMode("NXDN", $_SESSION['MMDVMHostConfigs']);?></tr>
    <tr><?php showMode("DMR XMode", $_SESSION['MMDVMHostConfigs']);?><?php showMode("POCSAG", $_SESSION['MMDVMHostConfigs']);?></tr>
</table>
<br />

<table>
    <tr><th colspan="2"><?php echo $lang['net_status'];?></th></tr>
    <tr><?php showMode("D-Star Network", $_SESSION['MMDVMHostConfigs']);?><?php showMode("DMR Network", $_SESSION['MMDVMHostConfigs']);?></tr>
    <tr><?php showMode("System Fusion Network", $_SESSION['MMDVMHostConfigs']);?><?php showMode("P25 Network", $_SESSION['MMDVMHostConfigs']);?></tr>
    <tr><?php showMode("YSF2DMR Network", $_SESSION['MMDVMHostConfigs']);?><?php showMode("NXDN Network", $_SESSION['MMDVMHostConfigs']);?></tr>
    <tr><?php showMode("YSF2NXDN Network", $_SESSION['MMDVMHostConfigs']);?><?php showMode("YSF2P25 Network", $_SESSION['MMDVMHostConfigs']);?></tr>
    <tr><?php showMode("DMR2NXDN Network", $_SESSION['MMDVMHostConfigs']);?><?php showMode("DMR2YSF Network", $_SESSION['MMDVMHostConfigs']);?></tr>
    <!--  <tr><td style="background:#606060; color:#b0b0b0;"></td><?php showMode("POCSAG Network", $_SESSION['MMDVMHostConfigs']);?></tr> -->
    <tr><?php showMode("", $_SESSION['MMDVMHostConfigs']);?></td><?php showMode("POCSAG Network", $_SESSION['MMDVMHostConfigs']);?></tr>
</table>
<br />

<table>
    <tr><th colspan="2"><?php echo $lang['radio_info'];?></th></tr>
    <tr><th>Trx</th>
	<?php
	// TRX Status code
	if (isset($lastHeard[0])) {
	    $isTXing = false;
	    
	    // Go through the whole LH array, backward, looking for transmission.
	    for (end($lastHeard); (($currentKey = key($lastHeard)) !== null); prev($lastHeard)) {
		$listElem = current($lastHeard);
		
		if ($listElem[2] && ($listElem[6] == null) && ($listElem[5] !== 'RF')) {
		    $isTXing = true;
		    
		    // Get rid of 'Slot x' for DMR, as it is meaningless, when 2 slots are txing at the same time.
		    $txMode = preg_split('#\s+#', $listElem[1])[0];
		    echo "<td style=\"background:#f33;\">TX $txMode</td>";
		    break;
		}
	    }
	    
	    if ($isTXing == false) {
		$listElem = $lastHeard[0];
		
	        if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'idle') {
	            echo "<td style=\"background:#0b0; color:#030;\">Listening</td>";
	        }
	        else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === NULL) {
	            if (isProcessRunning("MMDVMHost")) {
			echo "<td style=\"background:#0b0; color:#030;\">Listening</td>";
		    }
		    else {
			echo "<td style=\"background:#606060; color:#b0b0b0;\">OFFLINE</td>";
		    }
	        }
	        else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'D-Star') {
	            echo "<td style=\"background:#4aa361;\">RX D-Star</td>";
	        }
	        else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'D-Star') {
	            echo "<td style=\"background:#ade;\">Listening D-Star</td>";
	        }
	        else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'DMR') {
	            echo "<td style=\"background:#4aa361;\">RX DMR</td>";
	        }
	        else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'DMR') {
	            echo "<td style=\"background:#f93;\">Listening DMR</td>";
	        }
	        else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'YSF') {
	            echo "<td style=\"background:#4aa361;\">RX YSF</td>";
	        }
	        else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'YSF') {
	            echo "<td style=\"background:#ff9;\">Listening YSF</td>";
	        }
	        else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'P25') {
        	    echo "<td style=\"background:#4aa361;\">RX P25</td>";
        	}
        	else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'P25') {
        	    echo "<td style=\"background:#f9f;\">Listening P25</td>";
        	}
		else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'NXDN') {
        	    echo "<td style=\"background:#4aa361;\">RX NXDN</td>";
        	}
        	else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'NXDN') {
        	    echo "<td style=\"background:#c9f;\">Listening NXDN</td>";
        	}
		else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'POCSAG') {
        	    echo "<td style=\"background:#4aa361;\">POCSAG</td>";
        	}
        	else {
        	    echo "<td>".getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs'])."</td>";
        	}
	    }
	}
	else {
	    echo "<td style=\"background:#0b0; color:#030;\">Listening</td>";
	}
	?>
        </tr>
	<tr><th>Tx</th><td style="background: #ffffff;"><?php echo getMHZ(getConfigItem("Info", "TXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></td></tr>
	<tr><th>Rx</th><td style="background: #ffffff;"><?php echo getMHZ(getConfigItem("Info", "RXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></td></tr>
	<?php
	if (isset($_SESSION['DvModemFWVersion'])) {
	    echo '<tr><th>FW</th><td style="background: #ffffff;">'.$_SESSION['DvModemFWVersion'].'</td></tr>'."\n";
	}
	?>
	<?php
	if ($_SESSION['DvModemTCXOFreq']) {
	    echo '<tr><th>TCXO</th><td style="background: #ffffff;">'.$_SESSION['DvModemTCXOFreq'].'</td></tr>'."\n";
	} ?>
</table>

	<?php

	$testMMDVModeDSTAR = getConfigItem("D-Star", "Enable", $_SESSION['MMDVMHostConfigs']);
	if ( $testMMDVModeDSTAR == 1 ) { //Hide the D-Star Reflector information when D-Star Network not enabled.
	    $linkedTo = getActualLink($reverseLogLinesMMDVM, "D-Star");
	    echo "<br />\n";
	    echo "<table>\n";
	    echo "<tr><th colspan=\"2\">".$lang['dstar_repeater']."</th></tr>\n";
	    echo "<tr><th>RPT1</th><td style=\"background: #ffffff;\">".str_replace(' ', '&nbsp;', $_SESSION['DStarRepeaterConfigs']['callsign'])."</td></tr>\n";
	    echo "<tr><th>RPT2</th><td style=\"background: #ffffff;\">".str_replace(' ', '&nbsp;', $_SESSION['DStarRepeaterConfigs']['gateway'])."</td></tr>\n";
	    echo "<tr><th colspan=\"2\">".$lang['dstar_net']."</th></tr>\n";
	    echo "<tr><th>APRS</th><td style=\"background: #ffffff;\" title=\"".$_SESSION['APRSGatewayConfigs']['APRS-IS']['Server']."\">".substr($_SESSION['APRSGatewayConfigs']['APRS-IS']['Server'], 0, 16)."</td></tr>\n";
	    echo "<tr><th>IRC</th><td style=\"background: #ffffff;\" title=\"".$_SESSION['ircDDBConfigs']['ircddbHostname']."\">".substr($_SESSION['ircDDBConfigs']['ircddbHostname'], 0 ,16)."</td></tr>\n";
	    echo "<tr><td colspan=\"2\" ".GetActiveConnectionStyle($remoteMMDVMResults, "dstar")." title=\"".$linkedTo."\">".$linkedTo."</td></tr>\n";
	    echo "</table>\n";
	}
	
	$testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);
	if ( $testMMDVModeDMR == 1 ) { //Hide the DMR information when DMR mode not enabled.
	    $dmrMasterFile = fopen("/usr/local/etc/DMR_Hosts.txt", "r");
	    $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
	    $dmrMasterPort = getConfigItem("DMR Network", "Port", $_SESSION['MMDVMHostConfigs']);
	    $xlxMasterIP = 'x.x.x.x';

	    if ($dmrMasterHost == '127.0.0.1') {
		if (isset($_SESSION['DMRGatewayConfigs']['XLX Network']['Startup'])) {
		    $xlxMasterHost1 = 'XLX_'.$_SESSION['DMRGatewayConfigs']['XLX Network']['Startup'];
		}
		else {
		    $xlxMasterHost1 = "XLX Unknown";
		}
		$dmrMasterHost1 = str_replace('_', ' ', $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Name']);
		$dmrMasterHost2 = str_replace('_', ' ', $_SESSION['DMRGatewayConfigs']['DMR Network 2']['Name']);
		$dmrMasterHost3 = str_replace('_', ' ', $_SESSION['DMRGatewayConfigs']['DMR Network 3']['Name']);
		if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Name'])) {
		    $dmrMasterHost4 = str_replace('_', ' ', $_SESSION['DMRGatewayConfigs']['DMR Network 4']['Name']);
		}
		if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Name'])) {
		    $dmrMasterHost5 = str_replace('_', ' ', $_SESSION['DMRGatewayConfigs']['DMR Network 5']['Name']);
		}

		if ((isset($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'])) && ($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'] == 1)) {
		    while (!feof($dmrMasterFile)) {
			$dmrMasterLine = fgets($dmrMasterFile);
			$dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
		    	if ((count($dmrMasterHostF) >= 2) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
			    if ((strpos($dmrMasterHostF[0], 'XLX_') === 0) && ($xlxMasterHost1 == $dmrMasterHostF[0])) {
				$xlxMasterHost1 = str_replace('_', '', $dmrMasterHostF[0]); // Next server name, grabbed from log, won't have underscore sep.
			    	$xlxMasterIP = $dmrMasterHostF[2];
			    	break;
			    }
		        }
		    }
		}
		
		$xlxMasterHost1Tooltip = $xlxMasterHost1.' ('.$xlxMasterIP.')';
		$dmrMasterHost1Tooltip = $dmrMasterHost1.' ('.$_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'].')';
		$dmrMasterHost2Tooltip = $dmrMasterHost2.' ('.$_SESSION['DMRGatewayConfigs']['DMR Network 2']['Address'].')';
		$dmrMasterHost3Tooltip = $dmrMasterHost3.' ('.$_SESSION['DMRGatewayConfigs']['DMR Network 3']['Address'].')';
		if (isset($dmrMasterHost4)) {
		    $dmrMasterHost4Tooltip = $dmrMasterHost4.' ('.$_SESSION['DMRGatewayConfigs']['DMR Network 4']['Address'].')';;
		}
		if (isset($dmrMasterHost5)) {
		    $dmrMasterHost5Tooltip = $dmrMasterHost5.' ('.$_SESSION['DMRGatewayConfigs']['DMR Network 5']['Address'].')';;
		}

		if (strlen($xlxMasterHost1) > 19) {
		    $xlxMasterHost1 = substr($xlxMasterHost1, 0, 17) . '..';
		}
		if (strlen($dmrMasterHost1) > 19) {
		    $dmrMasterHost1 = substr($dmrMasterHost1, 0, 17) . '..';
		}
		if (strlen($dmrMasterHost2) > 19) {
		    $dmrMasterHost2 = substr($dmrMasterHost2, 0, 17) . '..';
		}
		if (strlen($dmrMasterHost3) > 19) {
		    $dmrMasterHost3 = substr($dmrMasterHost3, 0, 17) . '..';
		}
		if (isset($dmrMasterHost4)) {
		    if (strlen($dmrMasterHost4) > 19) {
			$dmrMasterHost4 = substr($dmrMasterHost4, 0, 17) . '..';
		    }
		}
		if (isset($dmrMasterHost5)) {
		    if (strlen($dmrMasterHost5) > 19) {
			$dmrMasterHost5 = substr($dmrMasterHost5, 0, 17) . '..';
		    }
		}
	    }
	    else {
		while (!feof($dmrMasterFile)) {
		    $dmrMasterLine = fgets($dmrMasterFile);
                    $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
		    if ((count($dmrMasterHostF) >= 4) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
			if (($dmrMasterHost == $dmrMasterHostF[2]) && ($dmrMasterPort == $dmrMasterHostF[4])) {
			    $dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]);
			    break;
			}
		    }
		}
		$dmrMasterHostTooltip = $dmrMasterHost;
		if (strlen($dmrMasterHost) > 19) {
		    $dmrMasterHost = substr($dmrMasterHost, 0, 17) . '..';
		}
	    }
	    fclose($dmrMasterFile);
	    
	    echo "<br />\n";
	    echo "<table>\n";
	    echo "<tr><th colspan=\"2\">".$lang['dmr_repeater']."</th></tr>\n";
	    echo "<tr><th>DMR ID</th><td style=\"background: #ffffff;\">".getConfigItem("General", "Id", $_SESSION['MMDVMHostConfigs'])."</td></tr>\n";
	    echo "<tr><th>DMR CC</th><td style=\"background: #ffffff;\">".getConfigItem("DMR", "ColorCode", $_SESSION['MMDVMHostConfigs'])."</td></tr>\n";
	    echo "<tr><th>TS1</th>";
	    
	    if (getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == 1) {
		echo "<td class=\"active-mode-cell\">enabled</td></tr>\n";
	    }
	    else {
		echo "<td class=\"inactive-mode-cell\">disabled</td></tr>\n";
	    }
	    echo "<tr><th>TS2</th>";
	    if (getConfigItem("DMR Network", "Slot2", $_SESSION['MMDVMHostConfigs']) == 1) {
		echo "<td class=\"active-mode-cell\">enabled</td></tr>\n";
	    }
	    else {
		echo "<td class=\"inactive-mode-cell\">disabled</td></tr>\n";
	    }
	    echo "<tr><th colspan=\"2\">".$lang['dmr_master']."</th></tr>\n";
	    if (getEnabled("DMR Network", $_SESSION['MMDVMHostConfigs']) == 1) {
		if ($dmrMasterHost == '127.0.0.1') {
		    if ((isset($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'])) && ($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'] == 1)) {
			echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "xlx")." colspan=\"2\" title=\"".$xlxMasterHost1Tooltip."\">".$xlxMasterHost1."</td></tr>\n";
		    }
                    if ( !isset($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled']) && isset($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled']) && $_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'] == 1) {
			if (file_exists("/var/log/pi-star/DMRGateway-".gmdate("Y-m-d").".log")) {
			    $xlxMasterHost1 = exec('grep \'XLX, Linking\|Unlinking\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d").'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\'');
			}
                        else {
			    $xlxMasterHost1 = exec('grep \'XLX, Linking\|Unlinking\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d", time() - 86340).'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\'');
			}
			//$xlxMasterHost1 = exec('grep \'XLX, Linking\|Unlinking\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d").'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\'');
			if ( strpos($xlxMasterHost1, 'Linking') !== false ) {
			    $xlxMasterHost1 = str_replace('Linking ', '', $xlxMasterHost1);
			}
			else if ( strpos($xlxMasterHost1, 'Unlinking') !== false ) {
			    $xlxMasterHost1 = "XLX Not Linked";
			}
			echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "xlx")." colspan=\"2\" title=\"".$xlxMasterHost1Tooltip."\">".$xlxMasterHost1."</td></tr>\n";
                    }
		    if ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'] == 1) {
			echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "net1")." colspan=\"2\" title=\"".$dmrMasterHost1Tooltip."\">".$dmrMasterHost1."</td></tr>\n";
		    }
		    if ($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Enabled'] == 1) {
			echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "net2")." colspan=\"2\" title=\"".$dmrMasterHost2Tooltip."\">".$dmrMasterHost2."</td></tr>\n";
		    }
		    if ($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Enabled'] == 1) {
			echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "net3")." colspan=\"2\" title=\"".$dmrMasterHost3Tooltip."\">".$dmrMasterHost3."</td></tr>\n";
		    }
		    if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Enabled'])) {
			if ($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Enabled'] == 1) {
                            echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "net4")." colspan=\"2\" title=\"".$dmrMasterHost4Tooltip."\">".$dmrMasterHost4."</td></tr>\n";
			}
		    }
		    if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Enabled'])) {
			if ($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Enabled'] == 1) {
                            echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "net5")." colspan=\"2\" title=\"".$dmrMasterHost5Tooltip."\">".$dmrMasterHost5."</td></tr>\n";
			}
		    }
		}
		else {
		    echo "<tr><td ".GetActiveConnectionStyle($remoteDMRGResults, "dmr")." colspan=\"2\" title=\"".$dmrMasterHostTooltip."\">".$dmrMasterHost."</td></tr>\n";
		}
	    }
	    else {
		echo "<tr><td colspan=\"2\" style=\"background:#606060; color:#b0b0b0;\">No DMR Network</td></tr>\n";
	    }
	    echo "</table>\n";
	}
	
	$testMMDVModeYSF = getConfigItem("System Fusion Network", "Enable", $_SESSION['MMDVMHostConfigs']);
	if ( isset($_SESSION['DMR2YSFConfigs']['Enabled']['Enabled']) ) {
	    $testDMR2YSF = $_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'];
	}
	if ( $testMMDVModeYSF == 1 || (isset($testDMR2YSF) && $testDMR2YSF == 1) ) { //Hide the YSF information when System Fusion Network mode not enabled.
            $ysfLinkedTo = getActualLink($reverseLogLinesYSFGateway, "YSF");
	    
	    if ($ysfLinkedTo == 'Not Linked' || $ysfLinkedTo == 'Service Not Started') {
                $ysfLinkedToTxt = $ysfLinkedTo;
		$ysfLinkState = '';
		$ysfLinkStateTooltip = $ysfLinkedTo;
	    }
	    else {
                $ysfHostFile = fopen("/usr/local/etc/YSFHosts.txt", "r");
                $ysfLinkedToTxt = "null";
                while (!feof($ysfHostFile)) {
                    $ysfHostFileLine = fgets($ysfHostFile);
                    $ysfRoomTxtLine = preg_split('/;/', $ysfHostFileLine);
		    
		    if (empty($ysfRoomTxtLine[0]) || empty($ysfRoomTxtLine[1]))
			continue;
		    
                    if (($ysfRoomTxtLine[0] == $ysfLinkedTo) || ($ysfRoomTxtLine[1] == $ysfLinkedTo)) {
                        $ysfLinkedToTxt = $ysfRoomTxtLine[1];
                        break;
                    }
                }
		
		if ($ysfLinkedToTxt != "null") {
		    $ysfLinkState = ' [Room]';
		    $ysfLinkStateTooltip = 'Room: ';
		}
		else {
		    $ysfLinkedToTxt = $ysfLinkedTo;
		    $ysfLinkState = ' [Lnkd]';
		    $ysfLinkStateTooltip = 'Linked to ';
		}
		
                $ysfLinkedToTxt = str_replace('_', ' ', $ysfLinkedToTxt);
            }

	    $ysfLinkedToTooltip = $ysfLinkStateTooltip.$ysfLinkedToTxt;
            if (strlen($ysfLinkedToTxt) > 19) {
		$ysfLinkedToTxt = substr($ysfLinkedToTxt, 0, 17) . '..';
	    }
            echo "<br />\n";
            echo "<table>\n";
	    echo "<tr><th colspan=\"2\">".$lang['ysf_net']."".$ysfLinkState."</th></tr>\n";
	    echo "<tr><td colspan=\"2\" ".GetActiveConnectionStyle($remoteMMDVMResults, "ysf")." title=\"".$ysfLinkedToTooltip."\">".$ysfLinkedToTxt."</td></tr>\n";
            echo "</table>\n";
	}
	
	$testYSF2DMR = 0;
	if ( isset($_SESSION['YSF2DMRConfigs']['Enabled']['Enabled']) ) {
	    $testYSF2DMR = $_SESSION['YSF2DMRConfigs']['Enabled']['Enabled'];
	}
	if ($testYSF2DMR == 1) { //Hide the YSF2DMR information when YSF2DMR Network mode not enabled.
            $dmrMasterFile = fopen("/usr/local/etc/DMR_Hosts.txt", "r");
            $dmrMasterHost = $_SESSION['YSF2DMRConfigs']['DMR Network']['Address'];
            while (!feof($dmrMasterFile)) {
                $dmrMasterLine = fgets($dmrMasterFile);
                $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
                if ((count($dmrMasterHostF) >= 2) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
                    if ($dmrMasterHost == $dmrMasterHostF[2]) {
			$dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]);
			break;
		    }
                }
            }
	    $dmrMasterHostTooltip = $dmrMasterHost;
            if (strlen($dmrMasterHost) > 19) {
		$dmrMasterHost = substr($dmrMasterHost, 0, 17) . '..';
	    }
            fclose($dmrMasterFile);
	    
            echo "<br />\n";
            echo "<table>\n";
            echo "<tr><th colspan=\"2\">YSF2DMR</th></tr>\n";
	    echo "<tr><th>DMR ID</th><td style=\"background: #ffffff;\">".$_SESSION['YSF2DMRConfigs']['DMR Network']['Id']."</td></tr>\n";
	    echo "<tr><th colspan=\"2\">YSF2".$lang['dmr_master']."</th></tr>\n";
            echo "<tr><td colspan=\"2\"style=\"background: #ffffff;\" title=\"".$dmrMasterHostTooltip."\">".$dmrMasterHost."</td></tr>\n";
            echo "</table>\n";
	}
	
	$testMMDVModeP25 = getConfigItem("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
	if ( isset($_SESSION['YSF2P25Configs']['Enabled']['Enabled']) ) { $testYSF2P25 = $_SESSION['YSF2P25Configs']['Enabled']['Enabled']; }
	if ( $testMMDVModeP25 == 1 || $testYSF2P25 ) { //Hide the P25 information when P25 Network mode not enabled.
	    echo "<br />\n";
	    echo "<table>\n";
	    if (getConfigItem("P25", "NAC", $_SESSION['MMDVMHostConfigs'])) {
		echo "<tr><th colspan=\"2\">".$lang['p25_radio']."</th></tr>\n";
		echo "<tr><th style=\"width:70px\">NAC</th><td>".getConfigItem("P25", "NAC", $_SESSION['MMDVMHostConfigs'])."</td></tr>\n";
	    }
	    echo "<tr><th colspan=\"2\">".$lang['p25_net']."</th></tr>\n";
	    echo "<tr><td colspan=\"2\" ".GetActiveConnectionStyle($remoteMMDVMResults, "p25").">".getActualLink($logLinesP25Gateway, "P25")."</td></tr>\n";
	    echo "</table>\n";
	}
	
	$testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
	if ( isset($_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled']) ) {
	    if ($_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'] == 1) {
		$testYSF2NXDN = 1;
	    }
	}
	if ( isset($_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled']) ) {
	    if ($_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'] == 1) {
		$testDMR2NXDN = 1;
	    }
	}
	if ( $testMMDVModeNXDN == 1 || isset($testYSF2NXDN) || isset($testDMR2NXDN) ) { //Hide the NXDN information when NXDN Network mode not enabled.
	    echo "<br />\n";
	    echo "<table>\n";
	    if (getConfigItem("NXDN", "RAN", $_SESSION['MMDVMHostConfigs'])) {
		echo "<tr><th colspan=\"2\">".$lang['nxdn_radio']."</th></tr>\n";
		echo "<tr><th style=\"width:70px\">RAN</th><td>".getConfigItem("NXDN", "RAN", $_SESSION['MMDVMHostConfigs'])."</td></tr>\n";
	    }
	    echo "<tr><th colspan=\"2\">".$lang['nxdn_net']."</th></tr>\n";
	    if (file_exists('/etc/nxdngateway')) {
		echo "<tr><td colspan=\"2\" ".GetActiveConnectionStyle($remoteMMDVMResults, "nxdn")." >".getActualLink($logLinesNXDNGateway, "NXDN")."</td></tr>\n";
	    }
	    else {
		echo "<tr><td colspan=\"2\" ".GetActiveConnectionStyle($remoteMMDVMResults, "nxdn")." >TG65000</td></tr>\n";
	    }
	    echo "</table>\n";
	}
	
	$testMMDVModePOCSAG = getConfigItem("POCSAG Network", "Enable", $_SESSION['MMDVMHostConfigs']);
	if ( $testMMDVModePOCSAG == 1 ) { //Hide the POCSAG information when POCSAG Network mode not enabled.
	    echo "<br />\n";
	    echo "<table>\n";
	    echo "<tr><th colspan=\"2\">POCSAG</th></tr>\n";
	    echo "<tr><th>Tx</th><td>".getMHZ(getConfigItem("POCSAG", "Frequency", $_SESSION['MMDVMHostConfigs']))."</td></tr>\n";
	    if (isset($_SESSION['DAPNETGatewayConfigs']['DAPNET']['Address'])) {
		$dapnetGatewayRemoteAddr = $_SESSION['DAPNETGatewayConfigs']['DAPNET']['Address'];
	        $dapnetGatewayRemoteTooltip = $dapnetGatewayRemoteAddr;
		if (strlen($dapnetGatewayRemoteAddr) > 19) {
		    $dapnetGatewayRemoteAddr = substr($dapnetGatewayRemoteAddr, 0, 17) . '..';
		}
		echo "<tr><th colspan=\"2\">POCSAG Master</th></tr>\n";
		echo "<tr><td colspan=\"2\"style=\"background: #ffffff;\" title=\"".$dapnetGatewayRemoteTooltip."\">".$dapnetGatewayRemoteAddr."</td></tr>\n";
	    }
	    echo "</table>\n";
	}
	?>
