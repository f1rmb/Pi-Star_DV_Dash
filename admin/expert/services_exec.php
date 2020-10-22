<?php
if (isset($_COOKIE['PHPSESSID']))
{
    session_id($_COOKIE['PHPSESSID']); 
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION)) {
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
