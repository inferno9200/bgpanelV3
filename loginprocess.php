<?php
require("configuration.php");
require("include.php");


if (isset($_POST['task']))
{
	$task = mysql_real_escape_string($_POST['task']);
}
else if (isset($_GET['task']))
{
	$task = mysql_real_escape_string($_GET['task']);
}

if (MAINTENANCE == 1)
{
	exit('<h1><b>503 Service Unavailable</b></h1>'); //If the maintenance mode is ON, we drop the user.
}


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+


switch(@$task)
{
	case 'processlogin':
		$username = mysqli_real_escape_string($_POST['username']);
		$password = mysqli_real_escape_string($_POST['password']);
		$return = $_POST['return'];
		###
		if (!empty($username) && !empty($password))
		{
			###
			//Processing the password
			$salt = hash('sha512', $username); //Salt
			$password = hash('sha512', $salt.$password); //Hashed password with salt
			###
			$numrows = query_numrows( "SELECT `clientid` FROM `".DBPREFIX."client` WHERE `username` = '".$username."' AND `password` = '".$password."' AND `status` = 'Active'" );
			if ($numrows == 1)
			{
				$rows = query_fetch_assoc( "SELECT `clientid`, `username`, `firstname`, `lastname`, `lang` FROM `".DBPREFIX."client` WHERE `username` = '".$username."' AND `password` = '".$password."' AND `status` = 'Active'" ); //Retrieve information from database
				###
				query_basic( "UPDATE `".DBPREFIX."client` SET `lastlogin` = '".date('Y-m-d H:i:s')."', `lastip` = '".$_SERVER['REMOTE_ADDR']."', `lasthost` = '".gethostbyaddr($_SERVER['REMOTE_ADDR'])."' WHERE `clientid` = '".$rows['clientid']."'" ); //Update last connection and so on
				###
				//Creation of the session's information
				$_SESSION['clientid'] = $rows['clientid'];
				$_SESSION['clientusername'] = $rows['username'];
				$_SESSION['clientfirstname'] = $rows['firstname'];
				$_SESSION['clientlastname'] = $rows['lastname'];
				$_SESSION['clientlang'] = $rows['lang'];
				###
				validateClient();
				###
				//Cookie
				if (isset($_POST['rememberMe']))
				{
					setcookie('clientUsername', htmlentities($username, ENT_QUOTES), time() + (86400 * 7 * 2)); // 86400 = 1 day
				}
				else if (isset($_COOKIE['clientUsername']))
				{
					setcookie('clientUsername', htmlentities($username, ENT_QUOTES), time() - 3600); // Remove the cookie
				}
				setcookie('clientLanguage', htmlentities($rows['lang'], ENT_QUOTES), time() + (86400 * 7 * 2)); // 86400 = 1 day
				###
				if (!empty($_SESSION['loginattempt']))
				{
					unset($_SESSION['loginattempt']);
				}
				else if (!empty($_SESSION['lockout']))
				{
					unset($_SESSION['lockout']);
				}
				###
				if (!empty($return))
				{
					header( "Location: ".urldecode($return)); //Redirection to the protected resource
					die();
				}
				else
				{
					header( "Location: index.php" ); //Standard login redirection to index.php
					die();
				}
			}
			else if (query_numrows( "SELECT `clientid` FROM `".DBPREFIX."client` WHERE `username` = '".$username."' AND `password` = '".$password."' AND `status` = 'Suspended'" ) == 1)
			{
				header( "Location: loginsuspended.php" );
				die();
			}
		}
		$_SESSION['loginerror'] = TRUE;
		@$_SESSION['loginattempt']++;
		if (4 < $_SESSION['loginattempt'])
		{
			$_SESSION['lockout'] = time();
			$_SESSION['loginattempt'] = 0; //Reseting attempts as the user will be ban for 5 mins
			$message = T_('5 Incorrect Client Login Attempts').' ('.$username.')';
			query_basic( "INSERT INTO `".DBPREFIX."log` SET `message` = '".$message."', `name` = 'System Message', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		}
		header( "Location: login.php" );
		die();
		break;

	case 'processpassword':
		$username = mysqli_real_escape_string($_POST['username']);
		$email = mysqli_real_escape_string($_POST['email']);
		###
		/**
		 * Securimage - A PHP class for creating captcha images.
		 *
		 * VERSION: 3.0
		 * AUTHOR: Drew Phillips <drew@drew-phillips.com>
		 */
		require("./libs/securimage/securimage.php");
		$securimage = new Securimage();
		###
		if ($securimage->check($_POST['captcha_code']) == TRUE)
		{
			if (!empty($username) && !empty($email))
			{
				$numrows = query_numrows( "SELECT `clientid` FROM `".DBPREFIX."client` WHERE `username` = '".$username."' && `email` = '".$email."'" );
				if ($numrows == 1)
				{
					$rows = query_fetch_assoc( "SELECT `clientid`, `email` FROM `".DBPREFIX."client` WHERE `username` = '".$username."'" );
					###
					//Processing the password
					$password = createRandomPassword(8);
					$password2 = $password; //Temp var for the email
					$salt = hash('sha512', $username); //Salt
					$password = hash('sha512', $salt.$password); //Hashed password with salt
					query_basic( "UPDATE `".DBPREFIX."client` SET `password` = '".$password."' WHERE `clientid` = '".$rows['clientid']."'" );
					###
					$to = htmlentities($rows['email'], ENT_QUOTES);
					$subject = T_('Reset Password');
					$message = T_('Your password has been reset to:');
					$message .= "<br /><br />{$password2}<br /><br />";
					$message .= T_('With IP').': ';
					$message .= $_SERVER['REMOTE_ADDR'];
					###
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: Bright Game Panel System <localhost@'.$_SERVER['SERVER_NAME'].'>' . "\r\n";
					$headers .= 'X-Mailer: PHP/' . phpversion();
					#-----------------+
					$mail = mail($to, $subject, $message, $headers);
					#-----------------+
					if(!$mail)
					{
					   exit("<h1><b>Error: message could not be sent.</b></h1>");
					}
					###
					//Message has been sent
					unset($_SESSION['loginattempt']);
					unset($_SESSION['lockout']);
					$_SESSION['success'] = 'Yes';
					header( "Location: login.php?task=password" );
					die();
				}
			}
		}
		$_SESSION['success'] = 'No';
		$_SESSION['loginattempt']++;
		if (4 < $_SESSION['loginattempt'])
		{
			$_SESSION['lockout'] = time();
			$_SESSION['loginattempt'] = 0; //Reseting attempts as the user will be ban for 5 mins
			$message = T_('5 Incorrect Client Login Attempts').'('.$username.')';
			query_basic( "INSERT INTO `".DBPREFIX."log` SET `message` = '".$message."', `name` = 'System Message', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		}
		header( "Location: login.php?task=password" );
		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>