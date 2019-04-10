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
	case 'clientadd':
		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
		$firstname = ucwords($firstname); //Format the first name as a proper noun
		$lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
		$lastname = ucwords($lastname); //Format the last name as a proper noun
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$email = strtolower($email); //Format the email to lower case
		$notes = mysqli_real_escape_string($conn, $_POST['notes']);
		if (isset($_POST['sendemail'])) {
			$sendemail = 'on';
		} else {
			$sendemail = '';
		}
		###
		//Used to fill in the blanks of the form
		$_SESSION['username'] = $username;
		$_SESSION['firstname'] = $firstname;
		$_SESSION['lastname'] = $lastname;
		$_SESSION['email'] = $email;
		$_SESSION['notes'] = $notes;
		###
		//Check the inputs. Output an error if the validation failed
		$usernameLength = strlen($username);
		$passwordLength = strlen($password);
		###
		$error = '';
		###
		if ($usernameLength < 4)
		{
			$error .= T_('Username is too short (4 Chars min.). ');
		}
		else if (query_numrows( "SELECT `clientid` FROM `".DBPREFIX."client` WHERE `username` = '".$username."'" ) != 0)
		{
			$error .= T_('Username is already in use. ');
		}
		if ((!empty($password)) && ($passwordLength <= 3))
		{
			$error .= T_('Password is unsecure. ');
		}
		if (checkEmail($email) == FALSE)
		{
			$error .= T_('Invalid Email. ');
		}
		if (empty($password) && empty($sendemail))
		{
			$error .= T_('You must send an email for a random password.');
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: clientadd.php" );
			die();
		}
		###
		//As the form has been validated, vars are useless
		unset($_SESSION['username']);
		unset($_SESSION['firstname']);
		unset($_SESSION['lastname']);
		unset($_SESSION['email']);
		unset($_SESSION['notes']);
		###
		//Adding client to the database
		if (empty($password))
		{
			$password = createRandomPassword(8);
		}
		$password2 = $password; //Temp var for the email
		$salt = hash('sha512', $username); //Salt
		$password = hash('sha512', $salt.$password); //Hashed password with salt
		$sql = ( "INSERT INTO `".DBPREFIX."client` SET
			`username` = '".$username."',
			`password` = '".$password."',
			`firstname` = '".$firstname."',
			`lastname` = '".$lastname."',
			`email` = '".$email."',
			`notes` = '".$notes."',
			`status` = 'Active',
			`lang` = 'en_EN',
			`lastlogin` = '0000-00-00 00:00:00',
			`lastactivity` = '0',
			`lastip` = '~',
			`lasthost` = '~',
			`created` = '".date('Y-m-d')."',
			`token`= ''" );
		###
		//Adding event to the database
		mysqli_query($conn, $sql); // it has to be executed here otherwise $mysqli_insert_id isn't working! #fix_later
		$clientid = mysqli_insert_id($conn);
		$message = "New Client Added: ".$username;
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `clientid` = '".$clientid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		if ($sendemail == 'on')
		{
			$systemurl = query_fetch_assoc( "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'systemurl' LIMIT 1" );
			###
			$to = $email;
			$subject = T_('Game Panel Account Information');
			$message = T_("Dear")." {$firstname} {$lastname},<br /><br /><u>".T_('Here is your account login details:')."</u><br />".T_('Username:')." {$username}<br />".T_('Email Address:')." {$email}<br />".T_('Password:')." {$password2}<br />".T_('Game Panel Link:')." ".$systemurl['value'];
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
		}
		###
		$_SESSION['msg1'] = T_('Client Added Successfully!');
		$_SESSION['msg2'] = T_('The new client account has been added and is ready for use.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: clientsummary.php?id=".urlencode($clientid));
		die();
		break;

	case 'clientprofile':
		$clientid = mysqli_real_escape_string($conn, $_POST['clientid']);
		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$status = mysqli_real_escape_string($conn, $_POST['status']);
		$firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
		$firstname = ucwords($firstname); //Format the first name as a proper noun
		$lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
		$lastname = ucwords($lastname); //Format the last name as a proper noun
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$email = strtolower($email); //Format the email to lower case
		if (isset($_POST['sendemail']))	{
			$sendemail = 'on';
		} else {
			$sendemail = '';
		}
		###
		//Check the inputs. Output an error if the validation failed
		$usernameLength = strlen($username);
		$passwordLength = strlen($password);
		###
		$error = '';
		###
		if (!is_numeric($clientid))
		{
			$error .= T_('Invalid ClientID. ');
		}
		else if (query_numrows( "SELECT `username` FROM `".DBPREFIX."client` WHERE `clientid` = '".$clientid."'" ) == 0)
		{
			$error .= T_('Invalid ClientID. ');
		}
		###
		if ($usernameLength < 4)
		{
			$error .= T_('Username is too short (4 Chars min.). ');
		}
		else if (query_numrows( "SELECT `status` FROM `".DBPREFIX."client` WHERE `username` = '".$username."' && `clientid` != '".$clientid."'" ) != 0)
		{
			$error .= T_('Username is already in use. ');
		}
		if ((!empty($password)) && ($passwordLength <= 3))
		{
			$error .= T_('Password is unsecure. ');
		}
		if (checkEmail($email) == FALSE)
		{
			$error .= T_('Invalid Email. ');
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error! Form has been reset!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: clientprofile.php?id=".urlencode($clientid));
			die();
		}
		###
		if (empty($password))
		{
			query_basic( "UPDATE `".DBPREFIX."client` SET
				`username` = '".$username."',
				`firstname` = '".$firstname."',
				`lastname` = '".$lastname."',
				`email` = '".$email."',
				`status` = '".$status."' WHERE `clientid` = '".$clientid."'" );
		}
		else
		{
			$password2 = $password; //Temp var for the email
			$salt = hash('sha512', $username); //Salt
			$password = hash('sha512', $salt.$password); //Hashed password with salt
			query_basic( "UPDATE `".DBPREFIX."client` SET
				`username` = '".$username."',
				`password` = '".$password."',
				`firstname` = '".$firstname."',
				`lastname` = '".$lastname."',
				`email` = '".$email."',
				`status` = '".$status."' WHERE `clientid` = '".$clientid."'" );
		}
		###
		//Adding event to the database
		$message = "Client Edited: ".$username." (by Admin)";
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `clientid` = '".$clientid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		if ( ($sendemail == 'on') && (!empty($password) ) )
		{
			$systemurl = query_fetch_assoc( "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'systemurl' LIMIT 1" );
			###
			$to = $email;
			$subject = 'Game Panel Account Information';
			$message = T_("Dear")." {$firstname} {$lastname},<br /><br /><u>".T_('Here is your new account login details:')."</u><br />".T_('Username:')." {$username}<br />".T_('Email Address:')." {$email}<br />".T_('Password:')." {$password2}<br />".T_('Game Panel Link:')." ".$systemurl['value'];
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
		}
		###
		$_SESSION['msg1'] = T_('Client Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to the client have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: clientsummary.php?id=".urlencode($clientid) );
		die();
		break;

	case 'clientdelete':
		$clientid = $_GET['id'];
		###
		$error = '';
		###
		if (!is_numeric($clientid))
		{
			$error .= T_('Invalid ClientID. ');
		}
		else if (query_numrows( "SELECT `username` FROM `".DBPREFIX."client` WHERE `clientid` = '".$clientid."'" ) == 0)
		{
			$error .= T_('Invalid ClientID. ');
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
		$username = query_fetch_assoc( "SELECT `username` FROM `".DBPREFIX."client` WHERE `clientid` = '".$clientid."' LIMIT 1" );
		###
		query_basic( "DELETE FROM `".DBPREFIX."client` WHERE `clientid` = '".$clientid."' LIMIT 1" );
		###
		//We have to remove the client from any associated group
		query_basic( "DELETE FROM `".DBPREFIX."groupMember` WHERE `clientid` = '".$clientid."' LIMIT 1" );
		###
		//Adding event to the database
		$message = 'Client Deleted: '.mysqli_real_escape_string($conn, $username['username']);
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `clientid` = '".$clientid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		$_SESSION['msg1'] = T_('Client Deleted Successfully!');
		$_SESSION['msg2'] = T_('The selected client has been removed.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: client.php" );
		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>
