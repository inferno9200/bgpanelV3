<?php
//*************************************************************************************************
        define('LICENSE', 'GNU GENERAL PUBLIC LICENSE - Version 3, 29 June 2007');
        define('DBHOST', 'sql139.main-hosting.eu');
        define('DBNAME', 'u489082846_bgp');
        define('DBUSER', 'u489082846_bgp');
        define('DBPASSWORD', 'bgp123');
        define('DBPREFIX', 'bgpv3_');
        define('CRONDELAY', 600);
        date_default_timezone_set('Europe/London');
        define('PROJECT_DIR', realpath(dirname(__FILE__)));
        define('LOCALE_DIR', PROJECT_DIR . '/locale');
        define('DEFAULT_LOCALE', 'en_EN');
        error_reporting(E_ALL);
        $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
        $nodb = mysqli_connect(DBHOST, DBUSER, DBPASSWORD);
//*************************************************************************************************
?>
