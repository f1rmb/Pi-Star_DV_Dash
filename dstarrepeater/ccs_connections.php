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

include_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';

if (exec('grep "CCS link" '.$linkLogPath.' | wc -l') >= 1) {
?>
    <b>Active CCS Connections</b>
    <table>
	<tr>
	    <th><a class=tooltip href="#">Repeater<span><b>Callsign of connected repeater</b></span></a></th>
	    <th><a class=tooltip href="#">Linked to<span><b>Actual link status</b></span></a></th>
	    <th><a class=tooltip href="#">Protocol<span><b>Protocol</b></span></a></th>
	    <th><a class=tooltip href="#">Direction<span><b>Direction</b>incoming or outgoing</span></a></th>
	    <th><a class=tooltip href="#">Last Change (<?php echo date('T')?>)<span><b>Timestamp of last change</b><?php echo date('T')?></span></a></th>
	</tr>
	<?php
	
	$ci = 0;
	if ($linkLog = fopen($linkLogPath,'r')) {
	    $i = 0;
	    while ($linkLine = fgets($linkLog)) {
		// 2013-02-27 19:49:27: CCS link - Rptr: DB0LJ  B Remote: DL5DI    Dir: Incoming
		if(preg_match_all('/^(.{19}).*(C[A-Za-z]*).*Rptr: (.{8}).*Remote: (.{8}).*Dir: (.{8})$/',$linkLine,$linx) > 0) {
		    $utc_time = $linx[1][0];
		    $utc_tz =  new DateTimeZone('UTC');
		    $local_tz = new DateTimeZone(date_default_timezone_get ());
		    $dt = new DateTime($utc_time, $utc_tz);
		    $dt->setTimeZone($local_tz);
		    $local_time = $dt->format('H:i:s M jS');
		    $linkDate = $local_time;
		    $linkType = $linx[2][0];
		    $linkRptr = $linx[3][0];
		    $linkRem = $linx[4][0];
		    $linkDir = $linx[5][0];
		    $ci++;
		    if($ci > 1) {
			$ci = 0;
		    }
		    print "<tr>";
		    print "<td>$linkRptr</td>";
		    print "<td>$linkRem</td>";
		    print "<td>CCS</td>";
		    print "<td>$linkDir</td>";
		    print "<td>$linkDate</td>";
		    print "</tr>\n";
		}
	    }
	    fclose($linkLog);
	}
	?>
    </table>
    <br />
<?php
}

$stn_is_set = 0;
for($i = 1;$i < 6; $i++) {
    $param="starNetCallsign" . $i;
    if(isset($_SESSION['ircDDBConfigs'][$param]) && !empty($_SESSION['ircDDBConfigs'][$param])) {
	$stn_is_set = 1;
	break;
    }
}
if($stn_is_set > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'].'/dstarrepeater/active_starnet_groups.php';
}
?>

	
