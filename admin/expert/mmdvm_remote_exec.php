<?php
if (isset($_COOKIE['PHPSESSID']))
{
    session_id($_COOKIE['PHPSESSID']);
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
}

if (!isset($_SESSION) || !is_array($_SESSION) || (count($_SESSION, COUNT_RECURSIVE) < 10)) {
    session_id('pistardashsess');
    session_start();
}

$command = isset($_GET['command']) ? $_GET['command'] : '';
$arg = isset($_GET['arg']) ? $_GET['arg'] : '';
$port = getConfigItem("Remote Control", "Port", $_SESSION['MMDVMHostConfigs']);

$cmdoutput = array();

if (!empty($command)) {
    $execCommand = "sudo /usr/local/bin/RemoteCommand ".$port." ".$command;

    if (!empty($arg)) {
	$execCommand = $execCommand.' '.$arg;
    }

    $cmdresult = exec($execCommand, $cmdoutput, $retvalue);
}
else {
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
