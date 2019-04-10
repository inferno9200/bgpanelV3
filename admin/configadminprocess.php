<?php
$return = TRUE;


require("../configuration.php");
require("./include.php");


if (isset($_POST['task']))
{
	$task = mysqli_real_escape_string($conn, $_POST['task']);
}
else if (isset($_GET['task']))
{
	$task = mysqli_real_escape_string($conn, $_GET['task']);
}


switch (@$task)
{
	case 'configadminadd':
		$access = mysqli_real_escape_string($conn, $_POST['access']);
		$firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
		$firstname = ucwords($firstname); //Format the first name as a proper noun
		$lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
		$lastname = ucwords($lastname); //Format the last name as a proper noun
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$email = strtolower($email); //Format the email to lower case
		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$password2 = mysqli_real_escape_string($conn, $_POST['password2']);
		###
		//Used to fill in the blanks of the form
		$_SESSION['access'] = $access;
		$_SESSION['firstname'] = $firstname;
		$_SESSION['lastname'] = $lastname;
		$_SESSION['email'] = $email;
		$_SESSION['username'] = $username;
		###
		//Check the inputs. Output an error if the validation failed
		$firstnameLength = strlen($firstname);
		$usernameLength = strlen($username);
		$passwordLength = strlen($password);
		###
		$error = '';
		###
		if ($firstnameLength < 2)
		{
			$error .= T_('Firstname is too short (2 Chars min.). ');
		}
		if (checkEmail($email) == FALSE)
		{
			$error .= T_('Invalid Email. ');
		}
		if ($usernameLength < 4)
		{
			$error .= T_('Username is too short (4 Chars min.). ');
		}
		else if (query_numrows( "SELECT `adminid` FROM `".DBPREFIX."admin` WHERE `username` = '".$username."'" ) != 0)
		{
			$error .= T_('Username is already in use. ');
		}
		if ($passwordLength <= 3)
		{
			$error .= T_('Password is unsecure or not set. ');
		}
		else if ($password != $password2)
		{
			$error .= T_("Passwords don't match.");
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: configadminadd.php" );
			die();
		}
		###
		//As the form has been validated, vars are useless
		unset($_SESSION['access']);
		unset($_SESSION['firstname']);
		unset($_SESSION['lastname']);
		unset($_SESSION['email']);
		unset($_SESSION['username']);
		###
		//Adding administrator to the database
		$salt = hash('sha512', $username); //Salt
		$password = hash('sha512', $salt.$password); //Hashed password with salt
		query_basic( "INSERT INTO `".DBPREFIX."admin` SET
			`username` = '".$username."',
			`firstname` = '".$firstname."',
			`lastname` = '".$lastname."',
			`email` = '".$email."',
			`password` = '".$password."',
			`access` = '".$access."',
			`notes` = '',
			`status` = 'Active',
			`lang` = '".DEFAULT_LOCALE."',
			`lastlogin` = '0000-00-00 00:00:00',
			`lastactivity` = '0',
			`lastip` = '~',
			`lasthost` = '~',
			`token` = ''" );
		###
		$_SESSION['msg1'] = T_('Admin Added Successfully!');
		$_SESSION['msg2'] = T_('The new admin account has been added and is ready for use.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: configadmin.php" );
		die();
		break;

	case 'configadminedit':
		$adminid = mysqli_real_escape_string($conn, $_POST['adminid']);
		$access = mysqli_real_escape_string($conn, $_POST['access']);
		$firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
		$firstname = ucwords($firstname); //Format the first name as a proper noun
		$lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
		$lastname = ucwords($lastname); //Format the last name as a proper noun
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$email = strtolower($email); //Format the email to lower case
		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$password2 = mysqli_real_escape_string($conn, $_POST['password2']);
		$status = mysqli_real_escape_string($conn, $_POST['status']);
		###
		//Check the inputs. Output an error if the validation failed
		$firstnameLength = strlen($firstname);
		$usernameLength = strlen($username);
		$passwordLength = strlen($password);
		###
		$error = '';
		###
		if (!is_numeric($adminid))
		{
			$error .= T_('Invalid AdminID. ');
		}
		else if (query_numrows( "SELECT `username` FROM `".DBPREFIX."admin` WHERE `adminid` = '".$adminid."'" ) == 0)
		{
			$error .= T_('Invalid AdminID. ');
		}
		###
		if ($firstnameLength < 2)
		{
			$error .= T_('Firstname is too short (2 Chars min.). ');
		}
		if (checkEmail($email) == FALSE)
		{
			$error .= T_('Invalid Email. ');
		}
		if ($usernameLength < 4)
		{
			$error .= T_('Username is too short (4 Chars min.). ');
		}
		else if (query_numrows( "SELECT `status` FROM `".DBPREFIX."admin` WHERE `username` = '".$username."' && `adminid` != '".$adminid."'" ) != 0)
		{
			$error .= T_('Username is already in use by another administrator. ');
		}
		if (!empty($password))
		{
			if ($passwordLength <= 3)
			{
				$error .= T_('Password is unsecure. ');
			}
				else if ($password != $password2)
			{
				$error .= T_("Passwords don't match. ");
			}
		}
		if ($adminid == $_SESSION['adminid'])
		{
			$error .= T_("You cannot change your information yourself. You should use")." <a href=\"myaccount.php\">".T_('My Account')."</a> ".T_("instead.");
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error! Form has been reset!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: configadminedit.php?id=".urlencode($adminid));
			die();
		}
		###
		//Processing password
		if (empty($password))
		{
			query_basic( "UPDATE `".DBPREFIX."admin` SET
				`username` = '".$username."',
				`firstname` = '".$firstname."',
				`lastname` = '".$lastname."',
				`email` = '".$email."',
				`access` = '".$access."',
				`status` = '".$status."' WHERE `adminid` = '".$adminid."'" );
		}
		else
		{
			$salt = hash('sha512', $username); //Salt
			$password = hash('sha512', $salt.$password); //Hashed password with salt
			query_basic( "UPDATE `".DBPREFIX."admin` SET
				`username` = '".$username."',
				`firstname` = '".$firstname."',
				`lastname` = '".$lastname."',
				`email` = '".$email."',
				`password` = '".$password."',
				`access` = '".$access."',
				`status` = '".$status."' WHERE `adminid` = '".$adminid."'" );
		}
		###
		$_SESSION['msg1'] = T_('Admin Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to the admin have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: configadmin.php" );
		die();
		break;

	case 'configadmindelete':
		$adminid = $_GET['id'];
		###
		$error = '';
		###
		if (!is_numeric($adminid))
		{
			$error .= T_('Invalid AdminID. ');
		}
		else if (query_numrows( "SELECT `adminid` FROM `".DBPREFIX."admin` WHERE `adminid` = '".$adminid."'" ) == 0)
		{
			$error .= T_('Invalid AdminID. ');
		}
		if ($adminid == $_SESSION['adminid'])
		{
			$error .= T_('You cannot delete yourself!');
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: index.php" );
			die();
		}
		###
		query_basic( "DELETE FROM `".DBPREFIX."admin` WHERE `adminid` = '".$adminid."' LIMIT 1" );
		###
		$_SESSION['msg1'] = T_('Admin Deleted Successfully!');
		$_SESSION['msg2'] = T_('The selected admin has been removed.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: configadmin.php" );
		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>
