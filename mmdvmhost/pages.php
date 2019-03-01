<?php
// Most of the work here contributed by geeks4hire (Ben Horan)

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
?>

<?php

// Get origin of the page loading
$origin = (isset($_GET['origin']) ? $_GET['origin'] : (isset($myOrigin) ? $myOrigin : "unknown"));

if (strcmp($origin, "admin") == 0) {
    $myRIC = getConfigItem("DAPNETAPI", "MY_RIC", getDAPNETAPIConfig());

    // Display personnal messages only if RIC has been defined, and some personnal messages are available
    if ($myRIC && (array_search('<MY_RIC>', $logLinesDAPNETGateway) != FALSE)) {
?>


<!-- Personnal messages-->

<div>

  <b><?php echo $lang['pocsag_persolist'];?></b>

  <div>
    <table>
      <thread>
        <tr>
          <th style="width: 140px;" ><a class="tooltip" href="#"><?php echo $lang['time'];?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
          <th style="width: max-content;" ><a class="tooltip" href="#"><?php echo $lang['pocsag_msg'];?><span><b>Message contents</b></span></a></th>
        </tr>
      </thread>
    </table>
  </div>

  <div style="max-height:190px; overflow-y:auto;" >
    <table>
      <thread>
        <tr>
          <th></th>
          <th></th>
        </tr>
      </thread>
      
      <tbody>

<?php

        $found = false;
  
        foreach ($logLinesDAPNETGateway as $dapnetMessageLine) {

            // After this, only messages for my RIC are stored
            if (!$found && strcmp($dapnetMessageLine, '<MY_RIC>') == 0) {
                $found = true;
                continue;
            }

            if ($found) {
                $dapnetMessageArr = explode(" ", $dapnetMessageLine);
                $utc_time = $dapnetMessageArr["0"]." ".substr($dapnetMessageArr["1"],0,-4);
                $utc_tz =  new DateTimeZone('UTC');
                $local_tz = new DateTimeZone(date_default_timezone_get ());
                $dt = new DateTime($utc_time, $utc_tz);
                $dt->setTimeZone($local_tz);
                $local_time = $dt->format('H:i:s M jS');

                $pos = strpos($dapnetMessageLine, '"');
                $len = strlen($dapnetMessageLine);
                $pocsag_msg = substr($dapnetMessageLine, ($pos - $len) + 1, ($len - $pos) - 2);
                
                // Formatting long messages without spaces
                if (strpos($pocsag_msg, ' ') == 0 && strlen($pocsag_msg) >= 70) {
                    $pocsag_msg = wordwrap($pocsag_msg, 70, ' ', true);
                }

?>

        <tr>
          <td style="width: 140px; vertical-align: top; text-align: left;"><?php echo $local_time; ?></td>
          <td style="width: max-content; vertical-align: top; text-align: left; word-wrap: break-word; white-space: normal !important;"><?php echo $pocsag_msg; ?></td>
        </tr>

<?php
            } // $found
        } // foreach
?>

      </tbody>
    </table>

  </div>

  <br />
    
<?php
    } // $myRIC
?>
    
  <div>

<!-- Activity -->
  <b><?php echo $lang['pocsag_list'];?></b>

  <div>
    <table>
      <thread>
        <tr>
          <th style="width: 140px;" ><a class="tooltip" href="#"><?php echo $lang['time'];?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
          <th style="width: 70px;" ><a class="tooltip" href="#"><?php echo $lang['pocsag_timeslot'];?><span><b>Message Mode</b></span></a></th>
          <th style="width: 90px;" ><a class="tooltip" href="#"><?php echo $lang['target'];?><span><b>RIC / CapCode of the receiving Pager</b></span></a></th>
          <th style="width: max-content;" ><a class="tooltip" href="#"><?php echo $lang['pocsag_msg'];?><span><b>Message contents</b></span></a></th>
        </tr>
      </thread>
    </table>
  </div>

  <div style="max-height:190px; overflow-y:auto;" >
    <table>
      <thread>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thread>
      <tbody>

<?php

    foreach($logLinesDAPNETGateway as $dapnetMessageLine) {

        // After this, only messages for my RIC are stored
        if (strcmp($dapnetMessageLine, '<MY_RIC>') == 0)
            break;
      
        $dapnetMessageArr = explode(" ", $dapnetMessageLine);
        $utc_time = $dapnetMessageArr["0"]." ".substr($dapnetMessageArr["1"],0,-4);
        $utc_tz =  new DateTimeZone('UTC');
        $local_tz = new DateTimeZone(date_default_timezone_get ());
        $dt = new DateTime($utc_time, $utc_tz);
        $dt->setTimeZone($local_tz);
        $local_time = $dt->format('H:i:s M jS');
        $pocsag_timeslot = $dapnetMessageArr["6"];
        $pocsag_ric = str_replace(',', '', $dapnetMessageArr["8"]);

        $pos = strpos($dapnetMessageLine, '"');
        $len = strlen($dapnetMessageLine);
        $pocsag_msg = substr($dapnetMessageLine, ($pos - $len) + 1, ($len - $pos) - 2);
        
        // Formatting long messages without spaces
        if (strpos($pocsag_msg, ' ') == 0 && strlen($pocsag_msg) >= 45) {
            $pocsag_msg = wordwrap($pocsag_msg, 45, ' ', true);
        }

?>

        <tr>
          <td style="width: 140px; vertical-align: top; text-align: left;"><?php echo $local_time; ?></td>
          <td style="width: 70px; vertical-align: top; text-align: center;"><?php echo "Slot ".$pocsag_timeslot; ?></td>
          <td style="width: 90px; vertical-align: top; text-align: center;"><?php echo $pocsag_ric; ?></td>
          <td style="width: max-content; vertical-align: top; text-align: left; word-wrap: break-word; white-space: normal !important;"><?php echo $pocsag_msg; ?></td>
        </tr>

<?php
    } // foreach
?>

      </tbody>
    </table>

  </div>

</div>

<?php
}
else { // origin == "admin"
?>

<b><?php echo $lang['pocsag_list'];?></b>

<table>
    <tr>
      <th><a class="tooltip" href="#"><?php echo $lang['time'];?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['pocsag_timeslot'];?><span><b>Message Mode</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['target'];?><span><b>RIC / CapCode of the receiving Pager</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['pocsag_msg'];?><span><b>Message contents</b></span></a></th>
    </tr>

<?php
    
    foreach($logLinesDAPNETGateway as $dapnetMessageLine) {
        $dapnetMessageArr = explode(' ', $dapnetMessageLine);
        $utc_time = $dapnetMessageArr["0"]." ".substr($dapnetMessageArr["1"],0,-4);
        $utc_tz =  new DateTimeZone('UTC');
        $local_tz = new DateTimeZone(date_default_timezone_get ());
        $dt = new DateTime($utc_time, $utc_tz);
        $dt->setTimeZone($local_tz);
        $local_time = $dt->format('H:i:s M jS');
        $pocsag_timeslot = $dapnetMessageArr["6"];
        $pocsag_ric = str_replace(',', '', $dapnetMessageArr["8"]);

        $pos = strpos($dapnetMessageLine, '"');
        $len = strlen($dapnetMessageLine);
        $pocsag_msg = substr($dapnetMessageLine, ($pos - $len) + 1, ($len - $pos) - 2);
        
        // Formatting long messages without spaces
        if (strpos($pocsag_msg, ' ') == 0 && strlen($pocsag_msg) >= 45) {
            $pocsag_msg = wordwrap($pocsag_msg, 45, ' ', true);
        }
        
?>

  <tr>
    <td style="width: 140px; vertical-align: top; text-align: left;"><?php echo $local_time; ?></td>
    <td style="width: 70px; vertical-align: top; text-align: center;"><?php echo "Slot ".$pocsag_timeslot; ?></td>
    <td style="width: 90px; vertical-align: top; text-align: center;"><?php echo $pocsag_ric; ?></td>
    <td style="width: max-content; vertical-align: top; text-align: left; word-wrap: break-word; white-space: normal !important;"><?php echo $pocsag_msg; ?></td>
  </tr>
        
<?php
    } // foreach
?>

</table>

<?php
} // origin == "admin"
?>
