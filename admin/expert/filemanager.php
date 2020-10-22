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

$fm_config_file = '/etc/tinyfilemanager-config.php';
$fm_auth_file = '/etc/tinyfilemanager-auth.php';

// Create default config file
if (file_exists($fm_config_file) == FALSE) {
    exec('sudo echo "<?php" > /tmp/XEFvyCJ6W2AyFMX5.tmp');
    exec('sudo echo "//Default Configuration" >> /tmp/XEFvyCJ6W2AyFMX5.tmp');
    exec('sudo echo "\$CONFIG = \'{\"lang\":\"en\",\"error_reporting\":false,\"show_hidden\":false,\"hide_Cols\":false,\"calc_folder\":false}\';" >> /tmp/XEFvyCJ6W2AyFMX5.tmp');
    exec('sudo echo "" >> /tmp/XEFvyCJ6W2AyFMX5.tmp');
    exec('sudo echo "?>" >> /tmp/XEFvyCJ6W2AyFMX5.tmp');
    
    exec('sudo mount -o remount,rw /');
    exec('sudo mv /tmp/XEFvyCJ6W2AyFMX5.tmp '.$fm_config_file.'');
    exec('sudo chown www-data:www-data '.$fm_config_file.'');
    exec('sudo chmod 664 '.$fm_config_file.'');
    exec('sudo mount -o remount,ro /');
}

// Create default auth file
if (file_exists($fm_auth_file) == FALSE) {
    exec('sudo echo "<?php" > /tmp/nyzGP6y6xQT5HZmz.tmp');
    exec('sudo echo "\$auth_users = array(" >> /tmp/nyzGP6y6xQT5HZmz.tmp');
    exec('sudo echo "\'root\' => \'\$2y\$10\$uvpqtIbiisbujB.oPbmRouOLgKyVhH2kqaDv7RQdGl66ncHKr0jm6\', //raspberry" >> /tmp/nyzGP6y6xQT5HZmz.tmp');
    exec('sudo echo "\'pi-star\' => \'\$2y\$10\$uvpqtIbiisbujB.oPbmRouOLgKyVhH2kqaDv7RQdGl66ncHKr0jm6\' //raspberry" >> /tmp/nyzGP6y6xQT5HZmz.tmp');
    exec('sudo echo ");" >> /tmp/nyzGP6y6xQT5HZmz.tmp');
    exec('sudo echo "?>" >> /tmp/nyzGP6y6xQT5HZmz.tmp');
    
    exec('sudo mount -o remount,rw /');
    exec('sudo mv /tmp/nyzGP6y6xQT5HZmz.tmp '.$fm_auth_file.'');
    exec('sudo chown www-data:www-data '.$fm_auth_file.'');
    exec('sudo chmod 664 '.$fm_auth_file.'');
    exec('sudo mount -o remount,ro /');
}


require_once('./tinyfilemanager.php');

?>
