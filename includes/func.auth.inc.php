<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}

function validateAdmin()
{
	session_regenerate_id();
	$token = session_id();
	###
	$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
	mysqli_query($conn, "UPDATE `".DBPREFIX."admin` SET `token` = '".$token."' WHERE `adminid` = '".$_SESSION['adminid']."'" );
}

function validateClient()
{
	session_regenerate_id();
	$token = session_id();
	###
	$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
	mysqli_query($conn, "UPDATE `".DBPREFIX."client` SET `token` = '".$token."' WHERE `clientid` = '".$_SESSION['clientid']."'" );
}

function isAdminLoggedIn()
{
	if (!empty($_SESSION['adminid']) && is_numeric($_SESSION['adminid']))
	{
		$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
		$adminverify = mysqli_query($conn, "SELECT `username` FROM `".DBPREFIX."admin` WHERE `adminid` = '".$_SESSION['adminid']."' && `status` = 'Active'" );
		if (mysqli_num_rows($adminverify) == '1')
		{
			return TRUE;
		}
		unset($adminverify);
	}
	return FALSE;
}

function isClientLoggedIn()
{
	if (!empty($_SESSION['clientid']) && is_numeric($_SESSION['clientid']))
	{
		$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
		$clientverify = mysqli_query($conn, "SELECT `username` FROM `".DBPREFIX."client` WHERE `clientid` = '".$_SESSION['clientid']."' && `status` = 'Active'" );
		if (mysqli_num_rows($clientverify) == '1')
		{
			return TRUE;
		}
		unset($clientverify);
	}
	return FALSE;
}

function logout()
{
	$_SESSION = array();
	session_destroy();
}

?>