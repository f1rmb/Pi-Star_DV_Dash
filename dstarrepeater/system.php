<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';
$configs = array();

if ($configfile = fopen($gatewayConfigPath,'r')) {
        while ($line = fgets($configfile)) {
                list($key,$value) = preg_split('/=/',$line);
                $value = trim(str_replace('"','',$value));
                if ($key != 'ircddbPassword' && strlen($value) > 0)
                $configs[$key] = $value;
        }

}
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
$rev="20141101";
$MYCALL=strtoupper($callsign);
?>
<?php
$cpuLoad = sys_getloadavg();
$cpuTempCRaw = exec('cat /sys/class/thermal/thermal_zone0/temp');
if ($cpuTempCRaw > 1000) { $cpuTempC = round($cpuTempCRaw / 1000, 1); } else { $cpuTempC = round($cpuTempCRaw, 1); }
$cpuTempF = round(+$cpuTempC * 9 / 5 + 32, 1);
if ($cpuTempC < 50) { $cpuTempHTML = "<td style=\"background: #1d1\">".$cpuTempC."&deg;C/".$cpuTempF."&deg;F</td>\n"; }
if ($cpuTempC >= 50) { $cpuTempHTML = "<td style=\"background: #fa0\">".$cpuTempC."&deg;C/".$cpuTempF."&deg;F</td>\n"; }
if ($cpuTempC >= 69) { $cpuTempHTML = "<td style=\"background: #f00\">".$cpuTempC."&deg;C/".$cpuTempF."&deg;F</td>\n"; }

function getServiceStatusClass($active) {
    echo (($active) ? 'active-service-cell' : 'inactive-service-cell');
}

?>
<table style="table-layout: fixed;">
  <tr>
    <th colspan="7"><?php echo $lang['service_status'];?></th>
  </tr>
  <tr>
    <td class="<?php getServiceStatusClass(isProcessRunning('MMDVMHost')); ?>">MMDVMHost</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('DMRGateway')); ?>">DMRGateway</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('ircddbgatewayd')); ?>">ircDDBGateway</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('YSFGateway')); ?>">YSFGateway</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('P25Gateway')); ?>">P25Gateway</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('APRSGateway')); ?>">APRSGateway</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('DAPNETGateway')); ?>">DAPNETGateway</td>
  </tr>
  <tr>
    <td class="disabled-service-cell"</td>
    <td class="disabled-service-cell"</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('dstarrepeaterd')); ?>">DStarRepeater</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('YSFParrot')); ?>">YSFParrot</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('P25Parrot')); ?>">P25Parrot</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('gpsd')); ?>">GPSd</td>
    <td class="disabled-service-cell"</td>
  </tr>
  <tr>
    <td class="disabled-service-cell"</td>
    <td class="disabled-service-cell"</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('timeserverd')); ?>">TimeServer</td>
    <td class="disabled-service-cell"</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('/usr/local/sbin/pistar-watchdog',true)); ?>">PiStar-Watchdog</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('/usr/local/sbin/pistar-remote',true)); ?>">PiStar-Remote</td>
    <td class="<?php getServiceStatusClass(isProcessRunning('/usr/local/sbin/pistar-keeper',true)); ?>">PiStar-Keeper</td>
  </tr>
</table>
<br />
