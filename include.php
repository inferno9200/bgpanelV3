<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}


if (is_dir("install"))
{
	header( "Location: install" );
	die();
}


require("./includes/functions.php");
require("./includes/mysql.php");
require("./libs/php-gettext/gettext.inc.php");

/**
 * Authentication
 */
session_start();

if (isClientLoggedIn() == FALSE) //Check if the user have wanted to access to a protected resource without being logged in
{
	if (!empty($return))  //Retrieve the last page where the user wanted to go
	{
		if ($return === TRUE) //Process protection
		{
			header( "Location: login.php" );
			die();
		}
		else
		{
			header( "Location: login.php?return=".urldecode($return) );
			die();
		}
	}
}


/**
 * SESSION check up (Test if the information stored in the globals $_SESSION are valid)
 */
if (isClientLoggedIn() == TRUE)
{
	$clientverify = mysqli_query( "SELECT `username`, `firstname`, `lastname`, `token`, `lastip` FROM `".DBPREFIX."client` WHERE `clientid` = '".$_SESSION['clientid']."' && `status` = 'Active'" );
	###
	$clientverify = mysql_fetch_assoc($clientverify);
	if (
			($clientverify['username'] != $_SESSION['clientusername']) ||
			($clientverify['firstname'] != $_SESSION['clientfirstname']) ||
			($clientverify['lastname'] != $_SESSION['clientlastname']) ||
			($clientverify['token'] != session_id()) ||
			($clientverify['lastip'] != $_SERVER['REMOTE_ADDR'])
		)
	{
		session_destroy();
		header( "Location: login.php" );
		die();
	}

	/**
	 * Define Language Using 'php gettext'
	 */
	defineLanguage($_SESSION['clientlang']);

	query_basic( "UPDATE `".DBPREFIX."client` SET `lastactivity` = '".$_SERVER['REQUEST_TIME']."' WHERE `clientid` = '".$_SESSION['clientid']."'" );

}


/**
 * GET BrightGamePanel Database INFORMATION
 * Load 'values' from `config` Table
 */
$panelName = query_fetch_assoc( "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'panelname' LIMIT 1" );
$panelVersion = query_fetch_assoc( "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'panelversion' LIMIT 1" );
$template = query_fetch_assoc( "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'clienttemplate' LIMIT 1" );
$maintenance = query_fetch_assoc( "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'maintenance' LIMIT 1" );


/**
 * GET BGP CORE FILES INFORMATION
 * Load version.xml (ROOT/.version/version.xml)
 */
$bgpCoreInfo = simplexml_load_file('./.version/version.xml');


/**
 * VERSION CONTROL
 * Check that core files are compatible with the current BrightGamePanel Database
 */
if ($panelVersion['value'] != $bgpCoreInfo->{'version'})
{
	die();
}


/**
 * CONSTANTS
 */
define( 'SITENAME', $panelName['value'] );
define( 'DBVERSION', $panelVersion['value'] );
define( 'TEMPLATE', $template['value'] );
define( 'MAINTENANCE', $maintenance['value'] );
unset($panelName, $panelVersion, $template, $maintenance);

define( 'PROJECT', $bgpCoreInfo->{'project'} );
define( 'PACKAGE', $bgpCoreInfo->{'package'} );
define( 'BRANCH', $bgpCoreInfo->{'branch'} );
define( 'COREVERSION', $bgpCoreInfo->{'version'} );
define( 'RELEASEDATE', $bgpCoreInfo->{'date'} );
unset($bgpCoreInfo);


/**
 * CRYPT_KEY is the Passphrase Used to Cipher/Decipher SSH Passwords
 * The key is stored into the file: ".ssh/passphrase"
 */
define('CRYPT_KEY', file_get_contents("./.ssh/passphrase"));


/**
 * MAINTENANCE CHECKER
 * Logout client.
 */
if (MAINTENANCE == 1)
{
	if (isClientLoggedIn() == TRUE)
	{
		logout();
		exit('<h1><b>503 Service Unavailable</b></h1>'); //If the maintenance mode is ON, we drop the user.
	}
}


?>
