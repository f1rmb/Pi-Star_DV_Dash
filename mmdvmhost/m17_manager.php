<?php
if ($_SERVER["PHP_SELF"] == "/admin/index.php") { // Stop this working outside of the admin page
    
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

    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

    // Check if M17 is Enabled
    $testMMDVModeM17 = getConfigItem("M17 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
    if ( $testMMDVModeM17 == 1 ) {
	// Check that the remote is enabled
	if (isset($_SESSION['M17GatewayConfigs']['Remote Commands']['Enable']) && (isset($_SESSION['M17GatewayConfigs']['Remote Commands']['Port'])) && ($_SESSION['M17GatewayConfigs']['Remote Commands']['Enable'] == 1)) {
	    if (!empty($_POST) && isset($_POST["m17MgrSubmit"])) {
		$remoteCommand = "";
		$remotePort = $_SESSION['M17GatewayConfigs']['Remote Commands']['Port'];
		
		// Handle Posted Data
		if ($_POST["Link"] == "LINK") {
		    $m17LinkHost = $_POST['m17LinkHost'];
		    $m17LinkToHost = "";
		    if ($m17LinkHost != "none") { // Unlinking
			$m17LinkToHost = "".$m17LinkHost."_".$_POST['m17LinkModule']."";
		    }
		    $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." Reflector ".$m17LinkToHost."";
		}
		else if ($_POST["Link"] == "UNLINK") {
		    $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." Reflector";
		}
		else {
		    echo "<b>M17 Link Manager</b>\n";
		    echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td>";
		    echo "Something wrong with your input, (Neither Link nor Unlink Sent) - please try again";
		    echo "</td></tr>\n</table>\n<br />\n";
		    unset($_POST);
		    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
		}
		if (empty($_POST['m17LinkHost'])) {
		    echo "<b>M17 Link Manager</b>\n";
		    echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td>";
		    echo "Something wrong with your input, (No target specified) -  please try again";
		    echo "</td></tr>\n</table>\n<br />\n";
		    unset($_POST);
		    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
		}
		
		if (isset($remoteCommand)) {
		    echo "<b>M17 Link Manager</b>\n";
		    echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td>";
		    echo exec($remoteCommand);
		    echo "</td></tr>\n</table>\n<br />\n";
		    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
		}
	    }
	    else {
		// Output HTML
		?>
    		<b>M17 Link Manager</b>
		<form action="//<?php echo htmlentities($_SERVER['HTTP_HOST']).htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
		    <table>
			<tr>
			    <th width="150"><a class="tooltip" href="#">Reflector<span><b>Reflector</b></span></a></th>
			    <th width="150"><a class="tooltip" href="#">Module<span><b>Module</b></span></a></th>
			    <th width="150"><a class="tooltip" href="#">Link / Un-Link<span><b>Link / Un-Link</b></span></a></th>
			    <th width="150"><a class="tooltip" href="#">Action<span><b>Action</b></span></a></th>
			</tr>
			<tr>
			    <?php
			    $m17CurrentHost = "";
			    $m17CurrentModule = "A";
			    $m17Linked = getActualLink($reverseLogLinesM17Gateway, "M17");
			    if (strpos($m17Linked, " Not ") === false) {
				$m17CurrentHost = substr($m17Linked, 0, -2);
				$m17CurrentModule = substr($m17Linked, -1);
			    }
			    ?>
			    <td>
				<select name="m17LinkHost">
				    <?php
				    if ($m17CurrentHost == "") {
					echo "      <option value=\"none\" selected=\"selected\">None</option>\n";
				    }
				    else {
					echo "      <option value=\"none\">None</option>\n";
				    }
				    if ($m17Hosts = fopen("/usr/local/etc/M17Hosts.txt", "r")) {
					while ($m17HostsLine = fgets($m17Hosts)) {
					    $m17Host = preg_split('/\s+/', $m17HostsLine);
					    if ((strpos($m17Host[0], '#') === FALSE ) && ($m17Host[0] != '')) {
						if ($m17CurrentHost == $m17Host[0]) {
						    echo "      <option value=\"$m17Host[0]\" selected=\"selected\">$m17Host[0]</option>\n";
						}
						else {
						    echo "      <option value=\"$m17Host[0]\">$m17Host[0]</option>\n";
						}
					    }
					}
					fclose($m17Hosts);
				    }
				    ?>
				</select>
			    </td>
			    <td>
				<select name="m17LinkModule">
				    <?php
				    $m17ModuleList = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
				    foreach ($m17ModuleList as $module) {
					if ($m17CurrentModule == $module) {
					    echo "  <option value=\"".$module."\" selected=\"selected\">".$module."</option>\n";
					}
					else {
					    echo "  <option value=\"".$module."\">".$module."</option>\n";
					}
				    }
				    ?>
				</select>
			    </td>
			    <td>
				<input type="radio" name="Link" value="LINK" checked="checked" />Link
				<input type="radio" name="Link" value="UNLINK" />UnLink
			    </td>
			    <td>
				<input type="submit" name="m17MgrSubmit" value="Request Change" />
			    </td>
			</tr>
		    </table>
		</form>
		<br />
	    <?php
            }
        }
    }
}
?>
