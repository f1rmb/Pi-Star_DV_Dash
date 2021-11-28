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
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

// Load the language support
require_once('config/language.php');
require_once('config/version.php');
include_once('mmdvmhost/tools.php');

// Retrieve server information
//$system = system_information();

function getStatusClass($status, $disabled = false) {
    if ($status) {
	echo '<td class="active-mode-cell" align="left">';
    }
    else {
	if ($disabled)
	    echo '<td class="disabled-mode-cell" align="left">';
	else
	    echo '<td class="inactive-mode-cell" align="left">';
    }
}

function system_information() {
    @list($system, $host, $kernel) = preg_split('/[\s,]+/', php_uname('a'), 5);
    $meminfo = false;
    if (@is_readable('/proc/meminfo')) {
        $data = explode("\n", file_get_contents("/proc/meminfo"));
        $meminfo = array();
        foreach ($data as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $val) = explode(":", $line);
                $meminfo[$key] = 1024 * floatval( trim( str_replace( ' kB', '', $val ) ) );
            }
        }
    }
    return array('date' => date('Y-m-d H:i:s T'),
                 'mem_info' => $meminfo,
                 'partitions' => disk_list()
    );
}

function disk_list() {
    $partitions = array();
    // Fetch partition information from df command
    // I would have used disk_free_space() and disk_total_space() here but
    // there appears to be no way to get a list of partitions in PHP?
    $output = array();
    @exec('df --block-size=1', $output);
    foreach($output as $line) {
        $columns = array();
        foreach(explode(' ', $line) as $column) {
            $column = trim($column);
            if($column != '') $columns[] = $column;
        }
        
        // Only process 6 column rows
        // (This has the bonus of ignoring the first row which is 7)
        if(count($columns) == 6) {
            $partition = $columns[5];
            $partitions[$partition]['Temporary']['bool'] = in_array($columns[0], array('tmpfs', 'devtmpfs'));
            $partitions[$partition]['Partition']['text'] = $partition;
            $partitions[$partition]['FileSystem']['text'] = $columns[0];
            if(is_numeric($columns[1]) && is_numeric($columns[2]) && is_numeric($columns[3])) {
                $partitions[$partition]['Size']['value'] = $columns[1];
                $partitions[$partition]['Free']['value'] = $columns[3];
                $partitions[$partition]['Used']['value'] = $columns[2];
            }
            else {
                // Fallback if we don't get numerical values
                $partitions[$partition]['Size']['text'] = $columns[1];
                $partitions[$partition]['Used']['text'] = $columns[2];
                $partitions[$partition]['Free']['text'] = $columns[3];
            }
        }
    }
    return $partitions;
}

function formatSize( $bytes ) {
    $types = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
    for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
    <head>
	<meta name="robots" content="index" />
	<meta name="robots" content="follow" />
	<meta name="language" content="English" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="Author" content="Andrew Taylor (MW0MWZ), Daniel Caujolle-Bert (F1RMB)" />
	<meta name="Description" content="Pi-Star SysInfo" />
	<meta name="KeyWords" content="MMDVMHost,ircDDBGateway,D-Star,ircDDB,DMRGateway,DMR,YSFGateway,YSF,C4FM,NXDNGateway,NXDN,P25Gateway,P25,Pi-Star,DL5DI,DG9VH,MW0MWZ,F1RMB" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	<meta http-equiv="Expires" content="0" />
	<title>Pi-Star - <?php echo $lang['digital_voice']." ".$lang['dashboard']." - ".$lang['update'];?></title>
	<link rel="stylesheet" type="text/css" href="/css/pistar-css.php" />
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
	<script type="text/javascript" src="/jquery.min.js"></script>
	<script type="text/javascript" src="/jquery-timing.min.js"></script>
	<style>  
	 .progress .bar + .bar {
	     -webkit-box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
	     -moz-box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
	     box-shadow: inset 1px 0 0 rgba(0, 0, 0, 0.15), inset 0 -1px 0 rgba(0, 0, 0, 0.15);
	 }
	 .progress-info .bar, .progress .bar-info {
	     background-color: #4bb1cf;
	     background-image: -moz-linear-gradient(top, #5bc0de, #339bb9);
	     background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#5bc0de), to(#339bb9));
	     background-image: -webkit-linear-gradient(top, #5bc0de, #339bb9);
	     background-image: -o-linear-gradient(top, #5bc0de, #339bb9);
	     background-image: linear-gradient(to bottom, #5bc0de, #339bb9);
	     background-repeat: repeat-x;
	     filter: progid: DXImageTransform.Microsoft.gradient(startColorstr='#ff5bc0de', endColorstr='#ff339bb9', GradientType=0);
	 }
	</style>
	<script type="text/javascript">
	 function refreshTable () {
	     $("#infotable").load(" #infotable > *");
	 }

	 var timer = setInterval(function(){refreshTable()}, 15000);
	</script>
    </head>
    <body>
	<div class="container">
	    <div class="header">
		<div style="font-size: 8px; text-align: left; padding-left: 8px; float: left;">Hostname: <?php echo exec('cat /etc/hostname'); ?></div><div style="font-size: 8px; text-align: right; padding-right: 8px;">Pi-Star:<?php echo $_SESSION['PiStarRelease']['Pi-Star']['Version']?> / <?php echo $lang['dashboard'].": ".$version; ?></div>
		<h1>Pi-Star <?php echo $lang['digital_voice']." ".$lang['dashboard']." - SysInfo";?></h1>
		<p>
		    <div class="navbar">
			<a class="menuconfig" href="/admin/configure.php"><?php echo $lang['configuration'];?></a>
			<a class="menuupdate" href="/admin/update.php"><?php echo $lang['update'];?></a>
			<a class="menupower" href="/admin/power.php"><?php echo $lang['power'];?></a>
			<a class="menuadmin" href="/admin/"><?php echo $lang['admin'];?></a>
			<a class="menudashboard" href="/"><?php echo $lang['dashboard'];?></a>
		    </div> 
		</p>
	    </div>
	    <div class="contentwide">
		<table id="infotable" width="100%" border="0">
		    <tr><th colspan="2">Pi-Star System Information</th></tr>
		    <?php
		    // Retrieve server information
		    $system = system_information();

		    // Ram information
		    if ($system['mem_info']) {
			echo "  <tr><td><b>Memory</b></td><td><b>Stats</b></td></tr>\n";
			$sysRamUsed = $system['mem_info']['MemTotal'] - $system['mem_info']['MemFree'] - $system['mem_info']['Buffers'] - $system['mem_info']['Cached'];
			$sysRamPercent = sprintf('%.2f',($sysRamUsed / $system['mem_info']['MemTotal']) * 100);
			echo "  <tr><td align=\"left\">RAM</td><td align=\"left\"><div class='progress progress-info' style='margin-bottom: 0;'><div class='bar' style='width: ".$sysRamPercent."%;'>Used&nbsp;".$sysRamPercent."%</div></div>";
			echo "  <b>Total:</b> ".formatSize($system['mem_info']['MemTotal'])."<b> Used:</b> ".formatSize($sysRamUsed)."<b> Free:</b> ".formatSize($system['mem_info']['MemTotal'] - $sysRamUsed)."</td></tr>\n";
		    }
		    // Filesystem Information
		    if (count($system['partitions']) > 0) {
			echo "  <tr><td><b>Mount</b></td><td><b>Stats</b></td></tr>\n";
			foreach($system['partitions'] as $fs) {
			    if ($fs['Used']['value'] > 0 && $fs['FileSystem']['text']!= "none" && $fs['FileSystem']['text']!= "udev") {
				$diskFree = $fs['Free']['value'];
				$diskTotal = $fs['Size']['value'];
				$diskUsed = $fs['Used']['value'];
				$diskPercent = sprintf('%.2f',($diskUsed / $diskTotal) * 100);
				
				echo "  <tr><td align=\"left\">".$fs['Partition']['text']."</td><td align=\"left\"><div class='progress progress-info' style='margin-bottom: 0;'><div class='bar' style='width: ".$diskPercent."%;'>Used&nbsp;".$diskPercent."%</div></div>";
				echo "  <b>Total:</b> ".formatSize($diskTotal)."<b> Used:</b> ".formatSize($diskUsed)."<b> Free:</b> ".formatSize($diskFree)."</td></tr>\n";
			    }
			}
		    }
		    // Binary Information
		    echo "  <tr><td><b>Binary</b></td><td><b>Version</b></td></tr>\n";
		    if (is_executable('/usr/local/bin/MMDVMHost')) {
			$MMDVMHost_Ver = exec('/usr/local/bin/MMDVMHost -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("MMDVMHost"), true); echo "MMDVMHost</td><td align=\"left\">".$MMDVMHost_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DMRGateway')) {
			$DMRGateway_Ver = exec('/usr/local/bin/DMRGateway -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("DMRGateway"), true); echo "DMRGateway</td><td align=\"left\">".$DMRGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DMR2YSF')) {
			$DMR2YSF_Ver = exec('/usr/local/bin/DMR2YSF -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("DMR2YSF"), true); echo "DMR2YSF</td><td align=\"left\">".$DMR2YSF_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DMR2NXDN')) {
			$DMR2NXDN_Ver = exec('/usr/local/bin/DMR2NXDN -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("DMR2NXDN"), true); echo "DMR2NXDN</td><td align=\"left\">".$DMR2NXDN_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSFGateway')) {
			$YSFGateway_Ver = exec('/usr/local/bin/YSFGateway -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("YSFGateway"), true); echo "YSFGateway</td><td align=\"left\">".$YSFGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DGIdGateway')) {
			$DGIdGateway_Ver = exec('/usr/local/bin/DGIdGateway -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("DGIdGateway"), true); echo "DGIdGateway</td><td align=\"left\">".$DGIdGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSF2DMR')) {
			$YSF2DMR_Ver = exec('/usr/local/bin/YSF2DMR -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("YSF2DMR"), true); echo "YSF2DMR</td><td align=\"left\">".$YSF2DMR_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSF2P25')) {
			$YSF2P25_Ver = exec('/usr/local/bin/YSF2P25 -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("YSF2P25"), true); echo "YSF2P25</td><td align=\"left\">".$YSF2P25_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSF2NXDN')) {
			$YSF2NXDN_Ver = exec('/usr/local/bin/YSF2NXDN -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("YSF2NXDN"), true); echo "YSF2NXDN</td><td align=\"left\">".$YSF2NXDN_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/P25Gateway')) {
			$P25Gateway_Ver = exec('/usr/local/bin/P25Gateway -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("P25Gateway"), true); echo "P25Gateway</td><td align=\"left\">".$P25Gateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/NXDNGateway')) {
			$NXDNGateway_Ver = exec('/usr/local/bin/NXDNGateway -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("NXDNGateway"), true); echo "NXDNGateway</td><td align=\"left\">".$NXDNGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DAPNETGateway')) {
			$DAPNETGateway_Ver = exec('/usr/local/bin/DAPNETGateway -v | cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("DAPNETGateway"), true); echo "DAPNETGateway</td><td align=\"left\">".$DAPNETGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/APRSGateway')) {
			$APRSGateway_Ver = exec('/usr/local/bin/APRSGateway -v| cut -d\' \' -f 3-');
			echo "  <tr>";getStatusClass(isProcessRunning("APRSGateway"), true); echo "APRSGateway</td><td align=\"left\">".$APRSGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/sbin/gpsd')) {
			$GPSD_Ver = exec('/usr/sbin/gpsd -V | cut -d\' \' -f 2-');
			echo "  <tr>";getStatusClass(isProcessRunning("gpsd"), true); echo "GPSd</td><td align=\"left\">".$GPSD_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/NextionDriver')) {
			$NEXTIONDRIVER_Ver = exec('/usr/local/bin/NextionDriver -V | head -n 2 | cut -d\' \' -f 3');
			echo "  <tr>";getStatusClass(isProcessRunning("NextionDriver"), true); echo "NextionDriver</td><td align=\"left\">".$NEXTIONDRIVER_Ver."</td></tr>\n";
		    }
		    ?>
		</table>
	    </div>
	    <div class="footer">
		Pi-Star web config, &copy; Andy Taylor (MW0MWZ) 2014-<?php echo date("Y"); ?>.<br />
		&copy; Daniel Caujolle-Bert (F1RMB) 2017-<?php echo date("Y"); ?>.<br />
		Need help? Click <a style="color: #ffffff;" href="https://www.facebook.com/groups/pistarusergroup/" target="_new">here for the Support Group</a><br />
		or Click <a style="color: #ffffff;" href="https://forum.pistar.uk/" target="_new">here to join the Support Forum</a><br />
	    </div>
	</div>
    </body>
</html>
