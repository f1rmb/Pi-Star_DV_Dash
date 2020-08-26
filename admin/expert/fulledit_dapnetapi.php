<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('pistardashsess');
    session_start();
}

$configfile = '/etc/dapnetapi.key';
$tempfile = '/tmp/jsADGHwf9sj294.tmp';
$servicenames = array('dapnetgateway.service');

// Make the bare config if we dont have one
if (! file_exists('/etc/dapnetapi.key')) {
    exec('sudo touch /tmp/jsADGHwf9sj294.tmp');
    exec('sudo chown www-data:www-data /tmp/jsADGHwf9sj294.tmp');
    exec('echo "[DAPNETAPI]" > /tmp/jsADGHwf9sj294.tmp');
    exec('echo "USER=" >> /tmp/jsADGHwf9sj294.tmp');
    exec('echo "PASS=" >> /tmp/jsADGHwf9sj294.tmp');
    exec('echo "TRXAREA=" >> /tmp/jsADGHwf9sj294.tmp');
    exec('echo "MY_RIC=" >> /tmp/jsADGHwf9sj294.tmp');
    exec('sudo chmod 664 /tmp/jsADGHwf9sj294.tmp');
    
    exec('sudo mount -o remount,rw /');
    exec('sudo mv /tmp/jsADGHwf9sj294.tmp /etc/dapnetapi.key');
    exec('sudo chmod 644 /etc/dapnetapi.key');
    exec('sudo chown root:root /etc/dapnetapi.key');
    exec('sudo mount -o remount,ro /');
}

require_once('fulledit_template.php');

?>
