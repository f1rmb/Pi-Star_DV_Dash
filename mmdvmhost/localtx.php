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
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
$localTXList = $lastHeard;

?>
<script type="text/javascript" >
 $(function() {
     $('table.local-tx-table').floatThead({
	 position: 'fixed',
	 scrollContainer: true
	 //scrollContainer: function($table){
	 //    return $table.closest('.table-container');
	 //}
     });
 });
</script>

<input type="hidden" name="localtx-autorefresh" value="OFF" />
<b><?php echo $lang['local_tx_list'];?></b>
<div>
    <div class="table-container">
	<table class="table local-tx-table">
	    <thead>
		<tr>
		    <th><a class="tooltip" href="#"><?php echo $lang['time'];?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
		    <th><a class="tooltip" href="#"><?php echo $lang['mode'];?><span><b>Transmitted Mode</b></span></a></th>
		    <th style="min-width:14ch"><a class="tooltip" href="#"><?php echo $lang['callsign'];?><span><b>Callsign</b></span></a></th>
		    <th><a class="tooltip" href="#"><?php echo $lang['target'];?><span><b>Target, D-Star Reflector, DMR Talk Group etc</b></span></a></th>
		    <th><a class="tooltip" href="#"><?php echo $lang['src'];?><span><b>Received from source</b></span></a></th>
		    <th><a class="tooltip" href="#"><?php echo $lang['dur'];?>(s)<span><b>Duration in Seconds</b></span></a></th>
		    <th style="min-width:5ch"><a class="tooltip" href="#"><?php echo $lang['ber'];?><span><b>Bit Error Rate</b></span></a></th>
		    <th style="min-width:8ch"><a class="tooltip" href="#">RSSI<span><b>Received Signal Strength Indication</b></span></a></th>
		</tr>
	    </thead>
	    <tbody>
<?php
$counter = 0;
$i = 0;
for ($i = 0; $i < count($localTXList); $i++) {
    $listElem = $localTXList[$i];
    if ($listElem[5] == "RF" && ($listElem[1] == "D-Star" || startsWith($listElem[1], "DMR") || $listElem[1] == "YSF" || $listElem[1]== "P25" || $listElem[1]== "NXDN")) {
	if ($counter < 40) { //last 40 calls
	    $utc_time = $listElem[0];
            $utc_tz =  new DateTimeZone('UTC');
            $local_tz = new DateTimeZone(date_default_timezone_get ());
            $dt = new DateTime($utc_time, $utc_tz);
            $dt->setTimezone($local_tz);
            $local_time = $dt->format('H:i:s M jS');
	    
	    echo"<tr>";
	    echo"<td align=\"left\">$local_time</td>";
	    echo"<td align=\"left\">$listElem[1]</td>";

	    if (is_numeric($listElem[2]) || strpos($listElem[2], "openSPOT") !== FALSE) {
		echo "<td align=\"left\">$listElem[2]</td>";
	    }
	    else if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $listElem[2])) {
		echo "<td align=\"left\">$listElem[2]</td>";
	    }
	    else {
		if (strpos($listElem[2],"-") > 0) { $listElem[2] = substr($listElem[2], 0, strpos($listElem[2],"-")); }
		if ($listElem[3] && $listElem[3] != '    ' ) {
		    echo "<td align=\"left\"><a href=\"https://www.qrz.com/db/$listElem[2]\" target=\"_blank\">$listElem[2]</a>/$listElem[3]</td>";
		}
		else {
		    echo "<td align=\"left\"><a href=\"https://www.qrz.com/db/$listElem[2]\" target=\"_blank\">$listElem[2]</a></td>";
		}
	    }
	    
	    if (strlen($listElem[4]) == 1) { $listElem[4] = str_pad($listElem[4], 8, " ", STR_PAD_LEFT); }
	    echo"<td align=\"left\">".str_replace(" ","&nbsp;", $listElem[4])."</td>";
	    
	    if ($listElem[5] == "RF"){
		echo "<td style=\"background:#1d1;\">RF</td>";
	    }
	    else {
		echo "<td>$listElem[5]</td>";
	    }

	    if ($listElem[6] == null) {
		// Live duration
                $utc_time = $listElem[0];
                $utc_tz =  new DateTimeZone('UTC');
                $now = new DateTime("now", $utc_tz);
                $dt = new DateTime($utc_time, $utc_tz);
                $duration = $now->getTimestamp() - $dt->getTimestamp();
                $duration_string = $duration<999 ? "&asymp; " . round($duration) . "s" : "&infin;";
                echo "<td colspan=\"3\" style=\"background:#f33;\">TX " . $duration_string . "</td>";
	    }
	    else if ($listElem[6] == "DMR Data") {
		echo "<td colspan=\"3\" style=\"background:#1d1;\">DMR Data</td>";
	    }
	    else {
		echo"<td>$listElem[6]</td>"; //duration
		
		// Colour the BER Field
		if (floatval($listElem[8]) == 0) {
		    echo "<td>$listElem[8]</td>";
		}
		else if (floatval($listElem[8]) >= 0.0 && floatval($listElem[8]) <= 1.9) {
		    echo "<td style=\"background:#1d1;\">$listElem[8]</td>";
		}
		else if (floatval($listElem[8]) >= 2.0 && floatval($listElem[8]) <= 4.9) {
		    echo "<td style=\"background:#fa0;\">$listElem[8]</td>";
		}
		else {
		    echo "<td style=\"background:#f33;\">$listElem[8]</td>";
		}
		
		echo"<td>$listElem[9]</td>"; //rssi
	    }
	    
	    echo "</tr>\n";
	    $counter++;
	}
    }
}

?>
	    </tbody>
	</table>
    </div>
    <div style="float: right; vertical-align: bottom; padding-top: 5px;">
	<div class="grid-container" style="display: inline-grid; grid-template-columns: auto 40px; padding: 1px; grid-column-gap: 5px;">
	    <div class="grid-item" style="padding-top: 3px;" >Auto Refresh
	    </div>
	    <div class="grid-item" >
		<div> <input id="toggle-localtx-autorefresh" class="toggle toggle-round-flat" type="checkbox" name="localtx-autorefresh" value="ON" checked="checked" aria-checked="true" aria-label="Auto Refresh" onchange="setLocalTXAutorefresh(this)" /><label for="toggle-localtx-autorefresh" ></label>
		</div>
	    </div>
	</div>
    </div>
    <br />
</div>
