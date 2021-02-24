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

$action = isset($_GET['action']) ? $_GET['action'] : '';
$cmdoutput = array();
switch ($action) {
    case "stop":
	$cmdresult = exec('sudo /usr/local/sbin/pistar-services stop', $cmdoutput, $retvalue);
	break;
    case "fullstop":
	$cmdresult = exec('sudo /usr/local/sbin/pistar-services fullstop', $cmdoutput, $retvalue);
	break;
    case "restart":
	$cmdresult = exec('sudo /usr/local/sbin/pistar-services restart', $cmdoutput, $retvalue);
	break;
    case "status":
	$cmdresult = exec('sudo /usr/local/sbin/pistar-services status', $cmdoutput, $retvalue);
	break;
    case "killmmdvmhost":
	$cmdresult = exec('sudo /usr/bin/killall -q -9 MMDVMHost', $cmdoutput, $retvalue);
	break;
    case "enabletheshield":
	$cmdresult = exec('sudo -- /bin/bash -c "mount -o remount,rw /; echo PLACEHOLDER > /etc/theshield.enabled; mount -o remount,ro /;"', $cmdoutput, $retvalue);
	break;
    case "disabletheshield":
	$cmdresult = exec('sudo -- /bin/bash -c "mount -o remount,rw /; rm -f /etc/theshield.enabled; mount -o remount,ro /;"', $cmdoutput, $retvalue);
	break;
    case "updatehostsfiles":
	$cmdresult = exec('sudo -- /bin/bash -c "mount -o remount,rw /; /usr/local/sbin/HostFilesUpdate.sh; /usr/local/sbin/pistar-services restart;"', $cmdoutput, $retvalue);
	break;
    default:
	$cmdoutput = array('error !');
}
echo "<br />";
foreach ($cmdoutput as $l) {
    echo $l."<br />";
}
if ($retvalue == 0) {
    echo "<h2>** Success **</h2>";
}
else {
    echo "<h2>!! Failure !!</h2>";
}
echo "<br />";
?>
