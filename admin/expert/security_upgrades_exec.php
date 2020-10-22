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

$cmdoutput = array();
# Avoid that FS is remounted RO while upgrading, the process could take some time to finish
exec('systemctl stop pistar-watchdog.timer > /dev/null 2>&1');
exec('systemctl stop pistar-watchdog.service > /dev/null 2>&1');
exec('sudo mount -o remount,rw /');
exec('sudo apt-get update > /dev/null 2<&1');
$cmdresult = exec('sudo /usr/bin/unattended-upgrade > /dev/null 2<&1', $cmdoutput, $retvalue);
exec('sudo mount -o remount,ro /');
exec('systemctl start pistar-watchdog.service > /dev/null 2>&1');
exec('systemctl start pistar-watchdog.timer > /dev/null 2>&1');

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
