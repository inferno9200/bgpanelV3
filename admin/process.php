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
	case 'logout':
		if (isAdminLoggedIn() == TRUE)
		{
			logout();
			header( "Location: login.php" );
			die();
		}
		else
		{
			exit('Not logged in');
		}
		break;

	case 'myaccount':
		$adminid = mysqli_real_escape_string($conn, $_POST['adminid']);
		$firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
		$firstname = ucwords($firstname); //Format the first name as a proper noun
		$lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
		$lastname = ucwords($lastname); //Format the last name as a proper noun
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$email = strtolower($email); //Format the email to lower case
		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$password2 = mysqli_real_escape_string($conn, $_POST['password2']);
		$language = mysqli_real_escape_string($conn, $_POST['language']);
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
			if ($password != $password2)
			{
				$error .= T_("Passwords don't match. ");
			}
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error! Form has been reset!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: myaccount.php" );
			die();
		}
		###
		//Processing password
		if (empty($password))
		{
			query_basic( "UPDATE `".DBPREFIX."admin` SET `username` = '".$username."', `firstname` = '".$firstname."', `lastname` = '".$lastname."', `email` = '".$email."', `lang` = '".$language."' WHERE `adminid` = '".$adminid."'" );
		}
		else
		{
			$salt = hash('sha512', $username); //Salt
			$password = hash('sha512', $salt.$password); //Hashed password with salt
			query_basic( "UPDATE `".DBPREFIX."admin` SET `username` = '".$username."', `firstname` = '".$firstname."', `lastname` = '".$lastname."', `email` = '".$email."', `password` = '".$password."', `lang` = '".$language."' WHERE `adminid` = '".$adminid."'" );
		}
		###
		//Refresh session's information if the connected user has edited his profile
		$_SESSION['adminusername'] = $username;
		$_SESSION['adminfirstname'] = $firstname;
		$_SESSION['adminlastname'] = $lastname;
		$_SESSION['adminlang'] = $language;
		###
		$_SESSION['msg1'] = T_('Account Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to your account have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: index.php" );
		die();
		break;

	case 'personalnotes':
		$adminid = mysqli_real_escape_string($conn, $_POST['adminid']);
		$notes = mysqli_real_escape_string($conn, $_POST['notes']);
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
		query_basic( "UPDATE `".DBPREFIX."admin` SET `notes` = '".$notes."' WHERE `adminid` = '".$adminid."'" );
		###
		$_SESSION['msg1'] = T_('Personal Notes Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to your personal notes have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: index.php" );
		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>