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

$command = isset($_GET['command']) ? $_GET['command'] : '';
$arg = isset($_GET['arg']) ? $_GET['arg'] : '';
$port = isset($_GET['port']) ? $_GET['port'] : '';

$cmdoutput = array();

if (!empty($command) && !empty($port)) {
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
