<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
} 

//if (!class_exists('Net_SSH2')) {
//	if (file_exists('../libs/vendor/autoload.php')) {
 //       use phpseclib\Crypt\AES;
 //       use phpseclib\Crypt\Random;
 //       use phpseclib\Net\SSH2;
//		require_once("../libs/vendor/autoload.php");
//	}
//	else {
//		// Client Side
//		require_once("../libs/vendor/autoload.php");
 //       use phpseclib\Crypt\AES;
 //       use phpseclib\Crypt\Random;
 //       use phpseclib\Net\SSH2;
//	}
//}



/**
 * Establish a SSH2 connection using PHPSECLIB
 *
 * @return object (ssh obj) OR string (err)
 */
//function newNetSSH2($ip, $sshport = 22, $login, $password)
//{

//	$ssh = new Net_SSH2($ip, $sshport);

//	if (!$ssh->login($login, $password))
//	{
//		$socket = @fsockopen($ip, $sshport, $errno, $errstr, 5);

//		if ($socket == FALSE) {
//			$debug = "Unable to connect to $ip on port $sshport : $errstr ( Errno: $errno )";
//			return $debug;
//		}

//		return 'Unable to connect to box with SSH';
//	}

//	return $ssh;
//}

?>