<div class="header">
    <div style="font-size: 8px; text-align: left; padding-left: 8px; float: left;">Hostname: <?php echo exec('cat /etc/hostname'); ?></div><div style="font-size: 8px; text-align: right; padding-right: 8px;">Pi-Star:<?php echo $_SESSION['PiStarRelease']['Pi-Star']['Version']?> / <?php echo $lang['dashboard'].": ".$version; ?></div>
    <h1>Pi-Star Digital Voice - Expert</h1>
    <p>
	<div class="navbar">
	    <a class="menuconfig" href="/admin/configure.php"><?php echo $lang['configuration'];?></a>
	    <a class="menubackup" href="/admin/config_backup.php"><?php echo $lang['backup_restore'];?></a>
	    <a class="menuupgrade" href="/admin/expert/upgrade.php"><?php echo $lang['upgrade'];?></a>
	    <a class="menuupdate" href="/admin/update.php"><?php echo $lang['update'];?></a>
	    <a class="menuadmin" href="/admin/"><?php echo $lang['admin'];?></a>
	    <a class="menudashboard" href="/"><?php echo $lang['dashboard'];?></a>
	</div>

	<div class="lnavbar">
            <ul class="mainnav">
		<li class="has-subs"><a href="#">Quick Editors</a>
		    <ul class="dropdown">
		    	<li class="subs"><a href="edit_mmdvmhost.php">MMDVMHost</a></li>
			<!-- DStar Sub -->
			<li class="subs has-subs"><a href="#">DStar</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_dstarrepeater.php">DStarRepeater</a></li>
				<li class="subs"><a href="edit_ircddbgateway.php">ircDDB Gateway</a></li>
				<li class="subs"><a href="edit_timeserver.php">TimeServer</a></li>
			    </ul>
			</li>
			<!-- DMR Sub -->
			<li class="subs has-subs"><a href="#">DMR</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_dmrgateway.php">DMR Gateway</a></li>
				<li class="subs"><a href="edit_dmr2ysf.php">DMR to YSF</a></li>
				<li class="subs"><a href="edit_dmr2nxdn.php">DMR to NXDN</a></li>
			    </ul>
			</li>
			<!-- SystemFusion Sub -->
			<li class="subs has-subs"><a href="#">SystemFusion</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_ysfgateway.php">YSF Gateway</a></li>
				<li class="subs"><a href="edit_dgidgateway.php">DGId Gateway</a></li>
				<li class="subs"><a href="edit_ysf2dmr.php">YSF to DMR</a></li>
				<li class="subs"><a href="edit_ysf2p25.php">YSF to P25</a></li>
				<li class="subs"><a href="edit_ysf2nxdn.php">YSF to NXDN</a></li>
			    </ul>
			</li>
			<li class="subs"><a href="edit_p25gateway.php">P25 Gateway</a></li>
			<li class="subs"><a href="edit_nxdngateway.php">NXDN Gateway</a></li>
			<li class="subs"><a href="edit_m17gateway.php">M17 Gateway</a></li>
			<li class="subs"><a href="edit_aprsgateway.php">APRS Gateway</a></li>
			<!-- DAPNet Sub -->
			<li class="subs has-subs"><a href="#">DAPNet</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_dapnetgateway.php">DAPNet Gateway</a></li>
				<li class="subs"><a href="edit_dapnetapi.php">DAPNet API</a></li>
			    </ul>
			</li>
		    </ul>
		</li>

		<li class="has-subs"><a href="#">Full Editors</a>
		    <ul class="dropdown">
			<li class="subs"><a href="fulledit_mmdvmhost.php">MMDVMHost</a></li>
			<li class="subs"><a href="fulledit_pistar-remote.php">PiStar-Remote</a></li>
			<!-- DStar Sub -->
			<li class="subs has-subs"><a href="#">DStar</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_dstarrepeater.php">DStarRepeater</a></li>
				<li class="subs"><a href="fulledit_ircddbgateway.php">ircDDB Gateway</a></li>
				<li class="subs"><a href="fulledit_timeserver.php">TimeServer</a></li>
			    </ul>
			</li>
			<!-- DMR Sub -->
			<li class="subs has-subs"><a href="#">DMR</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_dmrgateway.php">DMR Gateway</a></li>
				<li class="subs"><a href="fulledit_dmr2ysf.php">DMR to YSF</a></li>
				<li class="subs"><a href="fulledit_dmr2nxdn.php">DMR to NXDN</a></li>
				<li class="subs"><a href="fulledit_bmapikey.php">Brandmeister API</a></li>
			    </ul>
			</li>
			<!-- YSF Sub -->
			<li class="subs has-subs"><a href="#">SystemFusion</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_ysfgateway.php">YSF Gateway</a></li>
				<li class="subs"><a href="fulledit_dgidgateway.php">DGId Gateway</a></li>
				<li class="subs"><a href="fulledit_ysf2dmr.php">YSF to DMR</a></li>
				<li class="subs"><a href="fulledit_ysf2p25.php">YSF to P25</a></li>
				<li class="subs"><a href="fulledit_ysf2nxdn.php">YSF to NXDN</a></li>
			    </ul>
			</li>
			<li class="subs"><a href="fulledit_p25gateway.php">P25 Gateway</a></li>
			<li class="subs"><a href="fulledit_nxdngateway.php">NXDN Gateway</a></li>
			<li class="subs"><a href="fulledit_m17gateway.php">M17 Gateway</a></li>
			<li class="subs"><a href="fulledit_aprsgateway.php">APRS Gateway</a></li>
			<!-- DAPNet Sub -->
			<li class="subs has-subs"><a href="#">DAPNet</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_dapnetgateway.php">DAPNet Gateway</a></li>
				<li class="subs"><a href="fulledit_dapnetapi.php">DAPNet API</a></li>
			    </ul>
			</li>
			<li class="subs"><a href="fulledit_gpsd.php">GPSd</a></li>
			<li class="subs"><a href="fulledit_wpaconfig.php">WiFi</a></li>
			<li class="subs"><a href="fulledit_cron.php">System Cron</a></li>
			<li class="subs"><a href="fulledit_rssidat.php">RSSI Data</a></li>
		    </ul>
		</li>
		
		<li class="has-subs"><a href="#">Hosts Files</a>
		    <ul class="dropdown">
			<li class="subs"><a href="fulledit_hosts_DCS_Hosts.php">DCS Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_DExtra_Hosts.php">DExtra Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_DPlus_Hosts.php">DPlus Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_DMR_Hosts.php">DMR Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_P25_Hosts.php">P25 Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_NXDN_Hosts.php">NXDN Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_XLX_Hosts.php">XLX Hosts</a></li>
		    </ul>
		</li>
			
		<li class="has-subs"><a href="#">Tools</a>
		    <ul class="dropdown">
			<!-- Services Sub -->
			<li class="subs has-subs"><a href="#">Pi-Star Services</a>
			    <ul class="dropdown">
				<li class="subs"><a href="services.php?action=stop">Stop Services</a></li>
				<li class="subs"><a href="services.php?action=fullstop">Full Stop Services</a></li>
				<li class="subs"><a href="services.php?action=restart">Restart Services</a></li>
				<li class="subs"><a href="services.php?action=status">Services Status</a></li>
				<li class="subs"><a href="services.php?action=killmmdvmhost">Kill MMDVMHost</a></li>
			    </ul>
			</li>
			<li class="subs has-subs"><a href="#">Jitter Tests</a>
			    <ul class="dropdown">
				<li class="subs"><a href="jitter_test.php?group=brandmeister">Brandmeister</a></li>
				<li class="subs"><a href="jitter_test.php?group=dmrplus">DMR&#43;</a></li>
				<li class="subs"><a href="jitter_test.php?group=hblink">HBLink</a></li>
			    </ul>
			</li>
			<li class="subs has-subs"><a href="#">Extras</a>
			    <ul class="dropdown">
				<?php if (file_exists("/etc/theshield.enabled")) { ?>
				    <li class="subs"><a href="services.php?action=disabletheshield">Disable &lsquo;The Shield&rsquo; IDs</a></li>
				<?php } else { ?>
				    <li class="subs"><a href="services.php?action=enabletheshield">Enable &lsquo;The Shield&rsquo; IDs</a></li>
				<?php } ?>
				<li class="subs"><a href="services.php?action=updatehostsfiles">Update Hosts Files</a></li>
			    </ul>
			</li>
			<li class="subs"><a href="security_upgrades.php">Security Upgrades</a></li>
			<li class="subs"><a href="filemanager.php">File Manager</a></li>
			<li class="subs"><a href="edit_dashboard.php">CSS Tool</a></li>
			<li class="subs"><a href="ssh_access.php">SSH Access</a></li>
		    </ul>
		</li>
	    </ul>
	</div>
    </p>
</div>
