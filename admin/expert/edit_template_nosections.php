<?php
// Load the language support
require_once('../config/language.php');
//Load the Pi-Star Release file
$pistarReleaseConfig = '/etc/pistar-release';
$configPistarRelease = array();
$configPistarRelease = parse_ini_file($pistarReleaseConfig, true);
//Load the Version Info
require_once('../config/version.php');
?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
  <head>
    <meta name="robots" content="index" />
    <meta name="robots" content="follow" />
    <meta name="language" content="English" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="Author" content="Andrew Taylor (MW0MWZ)" />
    <meta name="Description" content="Pi-Star Editor" />
    <meta name="KeyWords" content="Pi-Star" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <meta http-equiv="Expires" content="0" />
    <title>Pi-Star - Digital Voice Dashboard - Expert Editor</title>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/pistar-css.php" />
  </head>
  <body>
  <div class="container">
  <?php include './header-menu.inc'; ?>
  <div class="contentwide">
      <?php

      // after the form submit
      if(isset($_POST)) {
	  $data = $_POST;

	  // Prepare data and update the configuration file
	  $content = "";
	  
	  foreach($data as $key => $value) {
	      if ($value == '') {
		  $content .= $key."= \n";
	      }
	      else {
		  $content .= $key."=".$value."\n";
	      }
	  }
	  
	  $wCount = FALSE;
	  // write it into file
	  if (($handle = fopen($tempfile, 'w')) != FALSE) {
	      if (($wCount = fwrite($handle, $content)) != FALSE) {
		  // Updates complete - copy the working file back to the proper location
		  exec('sudo mount -o remount,rw /');
		  exec('sudo cp '.$tempfile.' '.$configfile.'');
		  exec('sudo chmod 644 '.$configfile.'');
		  exec('sudo chown root:root '.$configfile.'');
		  exec('sudo mount -o remount,ro /');
		  
		  // Reload the affected daemon
		  // Reload the affected daemon
		  if (isset($servicenames) && (count($servicenames) > 0)) {
		      foreach($servicenames as $servicename) {
			  exec('sudo systemctl restart '.$servicename); // Reload the daemon
		      }
		  }
	      }

	      fclose($handle);
	  }
	  
	  unset($_POST);
      }

      // Prepare the temp file
      exec('sudo cp '.$configfile.' '.$tempfile.'');
      exec('sudo chown www-data:www-data '.$tempfile.'');
      exec('sudo chmod 664 '.$tempfile.'');
      
      $parsedIni = array();
      if (($cfgfile = fopen($tempfile, 'r')) != FALSE) {
	  while ($line = fgets($cfgfile)) {
	      if (strpos($line, '=') !== FALSE) {
		  list($key, $value) = explode('=', $line, 2);
		  $value = trim(str_replace('"', '', $value));
		  
		  $parsedIni[$key] = $value;
	      }
	  }
	  fclose($cfgfile);
      }
      
      echo '<form action="" method="post">'."\n";
      echo "<table>\n";
      echo "<tr><th colspan=\"2\">".$editorname."<th></tr>\n";
      
      // List all key = value 
      foreach($parsedIni as $key => $value)
      {
	  echo "<tr><td align=\"right\" width=\"30%\">$key</td><td align=\"left\"><input type=\"text\" name=\"$key\" value=\"$value\" /></td></tr>\n";
      }
      echo "</table>\n";
      echo '<input type="submit" value="'.$lang['apply'].'" />'."\n";
      echo "<br />\n";
      echo "</form>";
      ?>
  </div>

<div class="footer">
    Pi-Star web config, &copy; Andy Taylor (MW0MWZ) 2014-<?php echo date("Y"); ?>.<br />
    Need help? Click <a style="color: #ffffff;" href="https://www.facebook.com/groups/pistarusergroup/" target="_new">here for the Support Group</a><br />
    or Click <a style="color: #ffffff;" href="https://forum.pistar.uk/" target="_new">here to join the Support Forum</a><br />
</div>

</div>
</body>
</html>
