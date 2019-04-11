<?php
$return = TRUE;


require("../configuration.php");
require("./include.php");
require("../includes/templates.php");


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
	case 'generaledit':
		$panelName = mysqli_real_escape_string($conn, $_POST['panelName']);
		$systemUrl = mysqli_real_escape_string($conn, $_POST['systemUrl']);
		$adminTemplate = mysqli_real_escape_string($conn, $_POST['adminTemplate']);
		$clientTemplate = mysqli_real_escape_string($conn, $_POST['clientTemplate']);
		$maintenance = mysqli_real_escape_string($conn, $_POST['status']);
		###
		//Check the inputs. Output an error if the validation failed
		$panelNameLength = strlen($panelName);
		$systemUrlLength = strlen($systemUrl);
		###
		$error = '';
		###
		if ($panelNameLength == 0)
		{
			$error .= T_('Panel Name is too short ! ');
		}
		if ($systemUrlLength <= 7)
		{
			$error .= T_('System Url is too short ! ');
		}
		if ($maintenance != '0' && $maintenance != '1')
		{
			$error .= T_('Invalid maintenance mode. ');
		}
		//---------------------------------------------------------+
		$err = 0;

		foreach ($templates as $key => $value)
		{
			if ($adminTemplate == $value)
			{
				if (is_file('../bootstrap/css/'.$value))
				{
					unset($err);
					break;
				}
			}
			$err++;
		}

		if (isset($err))
		{
			$error .= T_('Invalid Admin template !');
		}
		//---------------------------------------------------------+
		$err = 0;

		foreach ($templates as $key => $value)
		{
			if ($clientTemplate == $value)
			{
				if (is_file('../bootstrap/css/'.$value))
				{
					unset($err);
					break;
				}
			}
			$err++;
		}

		if (isset($err))
		{
			$error .= T_('Invalid Client template !');
		}
		//---------------------------------------------------------+
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error! Form has been reset!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: configgeneral.php" );
			die();
		}
		###
		//Update
		query_basic( "UPDATE `".DBPREFIX."config` SET `value` = '".$panelName."' WHERE `setting` = 'panelname'" );
		query_basic( "UPDATE `".DBPREFIX."config` SET `value` = '".$systemUrl."' WHERE `setting` = 'systemurl'" );
		query_basic( "UPDATE `".DBPREFIX."config` SET `value` = '".$adminTemplate."' WHERE `setting` = 'admintemplate'" );
		query_basic( "UPDATE `".DBPREFIX."config` SET `value` = '".$clientTemplate."' WHERE `setting` = 'clienttemplate'" );
		query_basic( "UPDATE `".DBPREFIX."config` SET `value` = '".$maintenance."' WHERE `setting` = 'maintenance'" );
		###
		$_SESSION['msg1'] = T_('Settings Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to the settings have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: configgeneral.php" );
		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>