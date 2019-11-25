<?php
//*************************************************************************************************
        if (is_dir("install"))
        {
	  header( "Location: /install" );
	  die();
        }
	define('LICENSE', 'GNU GENERAL PUBLIC LICENSE - Version 3, 29 June 2007');
	define('DBHOST', 'localhost');
	define('DBNAME', 'bgpv3');
	define('DBUSER', 'root');
	define('DBPASSWORD', 'admin');
	define('DBPREFIX', 'bgp_');
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
