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
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

include_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';

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
