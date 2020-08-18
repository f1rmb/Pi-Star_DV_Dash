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
<b><?php echo $lang['hardware_info'];?></b>
<table style="table-layout: fixed;">
  <tr>
    <th><a class="tooltip" href="#"><?php echo $lang['hostname'];?><br /><span><b>System IP Address:<br /><?php echo str_replace(',', ',<br />', exec('hostname -I'));?></b></span></a></th>
    <th><a class="tooltip" href="#"><?php echo $lang['kernel'];?><span><b>Release</b></span></a></th>
    <th colspan="2"><a class="tooltip" href="#"><?php echo $lang['platform'];?><span><b>Uptime:<br /><?php echo str_replace(',', ',<br />', exec('uptime -p'));?></b></span></a></th>
    <th colspan="2"><a class="tooltip" href="#"><?php echo $lang['cpu_load'];?><span><b>CPU Load</b></span></a></th>
    <th><a class="tooltip" href="#"><?php echo $lang['cpu_temp'];?><span><b>CPU Temp</b></span></a></th>
  </tr>
  <tr>
    <td><?php echo php_uname('n');?></td>
    <td><?php echo php_uname('r');?></td>
    <td colspan="2"><?php echo exec('platformDetect.sh');?></td>
    <td colspan="2">1m:<?php echo $cpuLoad[0];?> / 5m:<?php echo $cpuLoad[1];?> / 15m:<?php echo $cpuLoad[2];?></td>
    <?php echo $cpuTempHTML; ?>
  </tr>
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
