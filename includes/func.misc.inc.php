<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}



/**
 * Little function that will generate a random password
 *
 * Some letters and digits have been removed, as they can be mistaken
 */
function createRandomPassword($length)
{
	$chars = "abcdefghijkmnpqrstuvwxyz23456789-#@*!_?ABCDEFGHJKLMNPQRSTUVWXYZ"; //Available characters for the password
	$string = str_shuffle($chars);
	$pass = substr($string, 0, $length); //Truncate the password to the specified length
	return $pass;
}



/**
 * Validate Email Addresses
 *
 * Return TRUE if the email is okay, FALSE if not.
 */
function checkEmail($email)
{
	if (strlen($email) == 0)
	{
		return FALSE;
	}
	if (preg_match('|^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$|i', $email))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}



/**
 * Validate Ip Addresses, by iceomnia
 *
 * Return TRUE if the IP is okay, FALSE if not.
 */
function validateIP($ip)
{
	$regex = "#[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#";
	$validate = preg_match($regex, $ip);
	if ($validate == 1)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}



/**
* Validate path, by warhawk3407
*
* Return TRUE if the specified path is okay, FALSE if not.
*/
function validatePath($path)
{
	$path = str_replace("\\", '', $path); // Strip '\'

	$regex = "#^/?(/?[ a-zA-Z0-9_.-])+/?$#";

	$validate = preg_match($regex, $path);

	if ($validate == 1) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}



/**
 * Format the mysql timestamp.
 */
function formatDate($timestamp)
{
	if ($timestamp == '0000-00-00 00:00:00' || $timestamp == 'Never')
	{
		return 'Never';
	}
	else
	{
		$dateTable = date_parse_from_format('Y-m-d H:i:s', $timestamp);
		return date('l | F j, Y | H:i', mktime($dateTable['hour'], $dateTable['minute'], $dateTable['second'], $dateTable['month'], $dateTable['day'], $dateTable['year']));
	}
}



/**
 * getStatus
 *
 * Test if the specified [ip-port] is Online or Offline.
 *
 * Return string 'Online' or 'Offline'
 */
function getStatus($ip, $port)
{
	if($socket = @fsockopen($ip, $port, $errno, $errstr, 2))
	{
		fclose($socket);
		return 'Online';
	}
	else
	{
		###
		//Uncomment the line above for debugging
		//echo "$errstr ($errno)<br />\n";
		###
		return 'Offline';
	}
}

/**
 * Convert bytes to human readable format
 *
 * http://codeaid.net/php/convert-size-in-bytes-to-a-human-readable-format-%28php%29
 *
 * @param integer bytes Size in bytes to convert
 * @return string
 */
function bytesToSize($bytes, $precision = 2)
{
	$kilobyte = 1024;
	$megabyte = $kilobyte * 1024;
	$gigabyte = $megabyte * 1024;
	$terabyte = $gigabyte * 1024;

	if (($bytes >= 0) && ($bytes < $kilobyte)) {
		return $bytes . ' B';

	} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
		return round($bytes / $kilobyte, $precision) . ' KB';

	} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
		return round($bytes / $megabyte, $precision) . ' MB';

	} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
		return round($bytes / $gigabyte, $precision) . ' GB';

	} elseif ($bytes >= $terabyte) {
		return round($bytes / $terabyte, $precision) . ' TB';

	} else {
		return $bytes . ' B';
	}
}

?>