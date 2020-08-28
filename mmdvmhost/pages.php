<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

// Most of the work here contributed by geeks4hire (Ben Horan)
// Skyper decode by Andy Taylor (MW0MWZ)

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

?>
<script type="text/javascript" >
 $(function(){
     $('table.perso-poc-table').floatThead({
	 position: 'fixed',
	 scrollContainer: true
	 //scrollContainer: function($table){
	 //    return $table.closest('.table-container');
	 //}
     });
     $('table.poc-lh-table').floatThead({
	 position: 'fixed',
	 scrollContainer: true
	 //scrollContainer: function($table){
	 //    return $table.closest('.table-container');
	 //}
     });
 });
</script>
<?php

// Get origin of the page loading
$origin = (isset($_GET['origin']) ? $_GET['origin'] : (isset($myOrigin) ? $myOrigin : "unknown"));

// Function to reverse the ROT1 used for Skyper
function un_rot($message) {
    $output = "";
    $messageTextArray = str_split($message);
    
    // ROT -1
    foreach($messageTextArray as $asciiChar) {
	$asciiAsInt = ord($asciiChar);
	$convretedAsciiAsInt = $asciiAsInt -1;
	$convertedAsciiChar = chr($convretedAsciiAsInt);
	$output .= $convertedAsciiChar;
    }
    
    // Return the clear text
    return $output;
}

// Function to handle Skyper Messages
function skyper($message, $pocsagric) {
    $output = "";
    $messageTextArray = str_split($message);
    
    if ($pocsagric == "0002504") {                                      // Skyper OTA TimeSync Messages
	$output = "[Skyper OTA Time] ".$message;
	return $output;
    }
    
    if ($pocsagric == "0004512") {                                      // Skyper Rubric Index
	if (isset($messageTextArray[0])) {                                // This is hard coded to 1 for rubric index
	    unset($messageTextArray[0]);
	}
	if (isset($messageTextArray[1])) {                                // Rubric Number
	    $skyperRubric = ord($messageTextArray[1]) - 31;
	    unset($messageTextArray[1]);
	}
	if (isset($messageTextArray[2])) {                                // Message number, hard coded to 10 for Rubric Index
	    unset($messageTextArray[2]);
	}
	
	if (count($messageTextArray) >= 1) {                              // Check to see if there is a message to decode
	    $output = "[Skyper Index Rubric:$skyperRubric] ".un_rot(implode($messageTextArray));
	}
	else {
	    $output = "[Skyper Index Rubric:$skyperRubric] No Name";
	}
	return $output;
    }
    
    if ($pocsagric == "0004520") {                                      // Skyper Message
	if (isset($messageTextArray[0])) {                                // Rubric Number
	    $skyperRubric = ord($messageTextArray[0]) - 31;
	    unset($messageTextArray[0]);
	}
	if (isset($messageTextArray[1])) {                                // Message number
	    $skyperMsgNr = ord($messageTextArray[1]) - 32;
	    unset($messageTextArray[1]);
	}
	
	if (count($messageTextArray) >= 1) {                              // Check to see if there is a message to decode
	    $output = "[Skyper Rubric:$skyperRubric Msg:$skyperMsgNr] ".un_rot(implode($messageTextArray));
	}
	else {
	    $output = "[Skyper Rubric:$skyperRubric] No Message";
	}
	return $output;
    }
}

//
// Fill table entries with DAPNETGW messages, stops to <MY_RIC> marker if tillMYRIC is true
//
function listDAPNETGWMessages($logLinesDAPNETGateway, $tillMYRIC) {
    foreach($logLinesDAPNETGateway as $dapnetMessageLine) {
	
	if ($tillMYRIC) {
	    // After this, only messages for my RIC are stored
	    if (strcmp($dapnetMessageLine, '<MY_RIC>') == 0) {
		break;
	    }
	}
	
	$dapnetMessageArr = explode(" ", $dapnetMessageLine);
	$dapnetMessageTxtArr = explode('"', $dapnetMessageLine);
	$utc_time = $dapnetMessageArr["0"]." ".substr($dapnetMessageArr["1"],0,-4);
	$utc_tz =  new DateTimeZone('UTC');
	$local_tz = new DateTimeZone(date_default_timezone_get ());
	$dt = new DateTime($utc_time, $utc_tz);
	$dt->setTimeZone($local_tz);
	$local_time = $dt->format('H:i:s M jS');
	$pocsag_timeslot = $dapnetMessageArr["6"];
	$pocsag_ric = str_replace(',', '', $dapnetMessageArr["8"]);
	// Fix incorrectly truncated strings containing double quotes
	unset($dapnetMessageTxtArr[0]);
	if (count($dapnetMessageTxtArr) > 2) {
            unset($dapnetMessageTxtArr[count($dapnetMessageTxtArr)]);
            $pocsag_msg = implode('"', $dapnetMessageTxtArr);
	}
	else {
            $pocsag_msg = $dapnetMessageTxtArr[1];
	}
	
	// Decode Skyper Messages
	if ( ($pocsag_ric == "0004520") || ($pocsag_ric == "0004512") || ($pocsag_ric == "0002504") ) {
            $pocsag_msg = skyper($pocsag_msg, $pocsag_ric);
	}
	
	// Formatting long messages without spaces
	if (strpos($pocsag_msg, ' ') == 0 && strlen($pocsag_msg) >= 45) {
            $pocsag_msg = wordwrap($pocsag_msg, 45, ' ', true);
	}
	echo "<tr>";
	echo "<td style=\"width: 140px; vertical-align: top; text-align: left;\">".$local_time."</td>";
	echo "<td style=\"width: 70px; vertical-align: top; text-align: center;\">Slot ".$pocsag_timeslot."</td>";
	echo "<td style=\"width: 90px; vertical-align: top; text-align: center;\">".$pocsag_ric."</td>";
	echo "<td style=\"width: max-content; vertical-align: top; text-align: left; word-wrap: break-word; white-space: normal !important;\">".$pocsag_msg."</td>";
	echo "</tr>";	
    }
}

//
if ((strcmp($origin, "admin") == 0) && isset($_SESSION['DAPNETAPIKeyConfigs'])) {
    $myRIC = getConfigItem("DAPNETAPI", "MY_RIC", $_SESSION['DAPNETAPIKeyConfigs']);
    
    // Display personnal messages only if RIC has been defined, and some personnal messages are available
    if ($myRIC && (array_search('<MY_RIC>', $logLinesDAPNETGateway) != FALSE)) {
?>
    <div>
	<input type="hidden" name="pocsag-autorefresh" value="OFF" />
	<!-- Personnal messages-->
	<div>
	    <b><?php echo $lang['pocsag_persolist'];?></b>
	    <div class="table-container">
		<table class="table poc-lh-table">
		    <thead>
			<tr>
			    <th style="width: 140px;" ><a class="tooltip" href="#"><?php echo $lang['time'];?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
			    <th style="width: max-content;" ><a class="tooltip" href="#"><?php echo $lang['pocsag_msg'];?><span><b>Message contents</b></span></a></th>
			</tr>
		    </thead>
		    <tbody>
			<?php
			$found = false;
			
			foreach ($logLinesDAPNETGateway as $dapnetMessageLine) {
			    // After this, only messages for my RIC are stored
			    if (!$found && strcmp($dapnetMessageLine, '<MY_RIC>') == 0) {
				$found = true;
				continue;
			    }
			    
			    if ($found) {
				$dapnetMessageArr = explode(" ", $dapnetMessageLine);
				$utc_time = $dapnetMessageArr["0"]." ".substr($dapnetMessageArr["1"],0,-4);
				$utc_tz = new DateTimeZone('UTC');
				$local_tz = new DateTimeZone(date_default_timezone_get ());
				$dt = new DateTime($utc_time, $utc_tz);
				$dt->setTimeZone($local_tz);
				$local_time = $dt->format('H:i:s M jS');
				
				$pos = strpos($dapnetMessageLine, '"');
				$len = strlen($dapnetMessageLine);
				$pocsag_msg = substr($dapnetMessageLine, ($pos - $len) + 1, ($len - $pos) - 2);
				
				// Formatting long messages without spaces
				if (strpos($pocsag_msg, ' ') == 0 && strlen($pocsag_msg) >= 70) {
				    $pocsag_msg = wordwrap($pocsag_msg, 70, ' ', true);
				}
			?>
		                <tr>
				    <td style="width: 140px; vertical-align: top; text-align: left;"><?php echo $local_time; ?></td>
				    <td style="width: max-content; vertical-align: top; text-align: left; word-wrap: break-word; white-space: normal !important;"><?php echo $pocsag_msg; ?></td>
				</tr>
                        <?php
                            } // $found
                        } // foreach
			?>
		    </tbody>
		</table>
	    </div>
	</div>
	<br />

<?php
    } // $myRIC
} // admin
?>

<div>
    <div>
	<!-- Activity -->
	<b><?php echo $lang['pocsag_list'];?></b>	
	<div class="table-container">
	    <table class="table poc-lh-table">
		<thead>
		    <tr>
			<th style="width: 140px;" ><a class="tooltip" href="#"><?php echo $lang['time'];?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
			<th style="width: 70px;" ><a class="tooltip" href="#"><?php echo $lang['pocsag_timeslot'];?><span><b>Message Mode</b></span></a></th>
			<th style="width: 90px;" ><a class="tooltip" href="#"><?php echo $lang['target'];?><span><b>RIC / CapCode of the receiving Pager</b></span></a></th>
			<th style="width: max-content;" ><a class="tooltip" href="#"><?php echo $lang['pocsag_msg'];?><span><b>Message contents</b></span></a></th>
		    </tr>
		</thead>
		<tbody>
		    <?php listDAPNETGWMessages($logLinesDAPNETGateway, ((strcmp($origin, "admin") == 0) ? true : false)); ?>
		</tbody>
	    </table>
	</div>
	<div style="display:inline-block;width: 100%;">
	    <div style="float: right; vertical-align: bottom; padding-top: 5px;">
		<div class="grid-container" style="display: inline-grid; grid-template-columns: auto 40px; padding: 1px; grid-column-gap: 5px;">
		    <div class="grid-item" style="padding-top: 3px;" >Auto Refresh
		    </div>
		    <div class="grid-item" >
			<div> <input id="toggle-pocsag-autorefresh" class="toggle toggle-round-flat" type="checkbox" name="pocsag-autorefresh" value="ON" checked="checked" aria-checked="true" aria-label="POCSAG Auto Refresh" onchange="setPagesAutorefresh(this)" /><label for="toggle-pocsag-autorefresh" ></label>
			</div>
		    </div>
		</div>
	    </div>
	</div>
    </div>
</div>
