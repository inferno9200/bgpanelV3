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
	case 'configgroupadd':
		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$description = mysqli_real_escape_string($conn, $_POST['notes']);
		###
		//Used to fill in the blanks of the form
		$_SESSION['name'] = $name;
		$_SESSION['notes'] = $description;
		###
		//Check the inputs. Output an error if the validation failed
		$nameLength = strlen($name);
		###
		$error = '';
		###
		if ($nameLength < 2)
		{
			$error .= T_('Group Name is too short (2 Chars min.). ');
		}
		if (query_numrows( "SELECT `groupid` FROM `".DBPREFIX."group` WHERE `name` = '".$name."'" ) != 0)
		{
			$error .= T_('This name is already in use !');
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: configgroupadd.php" );
			die();
		}
		###
		//As the form has been validated, vars are useless
		unset($_SESSION['name']);
		unset($_SESSION['notes']);
		###
		//Adding group to the database
		$sql = ( "INSERT INTO `".DBPREFIX."group` SET `name` = '".$name."', `description` = '".$description."'" );
		###
		mysqli_query($conn, $sql); // it has to be executed here otherwise $mysqli_insert_id isn't working! #fix_later
		$groupid = mysqli_insert_id($conn);
		###
		$_SESSION['msg1'] = T_('Group Added Successfully!');
		$_SESSION['msg2'] = T_('The new group has been added but you have to edit it to add members.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: configgroupedit.php?id=".urlencode($groupid) );
		die();
		break;

	case 'configgroupedit':
		$groupid = mysqli_real_escape_string($conn, $_POST['groupid']);
		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$description = mysqli_real_escape_string($conn, $_POST['notes']);
		if (is_numeric($groupid))
		{
			if (getGroupClients($groupid) != FALSE)
			{
				$clients = getGroupClients($groupid);
				foreach($clients as $key => $value)
				{
					if (isset($_POST['removeid'.$key]))
					{
						$removeids[] = $value;
					}
				}
				unset($clients);
			}
		}
		$newClient = mysqli_real_escape_string($conn, $_POST['newClient']);
		###
		//Check the inputs. Output an error if the validation failed
		$nameLength = strlen($name);
		###
		$error = '';
		###
		if (!is_numeric($groupid))
		{
			$error .= T_('Invalid GroupID. ');
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."group` WHERE `groupid` = '".$groupid."'" ) == 0)
		{
			$error .= T_('Invalid GroupID. ');
		}
		###
		if ($nameLength < 2)
		{
			$error .= T_('Group Name is too short (2 Chars min.). ');
		}
		if ($newClient != '-Select-')
		{
			if (query_numrows( "SELECT `clientid` FROM `".DBPREFIX."client` WHERE `username` = '".$newClient."'" ) == 0)
			{
				$error .= T_('Invalid Client Username '.$newClient.'. ');
			}
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error! Form has been reset!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: configgroupedit.php?id=".urlencode($groupid) );
			die();
		}
		###
		if ($newClient == '-Select-')
		{
			// Update group
			query_basic( "UPDATE `".DBPREFIX."group` SET `name` = '".$name."', `description` = '".$description."' WHERE `groupid` = '".$groupid."'" );
			###
			if (isset($removeids))
			{
				// Remove clients
				foreach($removeids as $key => $value)
				{
					$groupids = query_fetch_assoc( "SELECT `groupids` FROM `".DBPREFIX."groupMember` WHERE `clientid` = '".$value."'" );
					###
					$groupids['groupids'] = str_replace( $groupid.';', '', $groupids['groupids'] );
					###
					if (empty($groupids['groupids']))
					{
						query_basic( "DELETE FROM `".DBPREFIX."groupMember` WHERE `clientid` = '".$value."' LIMIT 1" );
					}
					else
					{
						query_basic( "UPDATE `".DBPREFIX."groupMember` SET `groupids` = '".$groupids['groupids']."' WHERE `clientid` = '".$value."'" );
					}
					unset($groupids);
				}
			}
			###
			$_SESSION['msg1'] = T_('Group Updated Successfully!');
			$_SESSION['msg2'] = T_('Your changes to the group have been saved.');
			$_SESSION['msg-type'] = 'success';
		}
		else
		{
			// Adding a new client
			$clientid = query_fetch_assoc( "SELECT `clientid` FROM `".DBPREFIX."client` WHERE `username` = '".$newClient."'" );
			###
			if (!checkClientGroup($groupid, $clientid['clientid']))
			{
				if (query_numrows( "SELECT `id` FROM `".DBPREFIX."groupMember` WHERE `clientid` = '".$clientid['clientid']."'" ) == 0)
				{
					query_basic( "INSERT INTO `".DBPREFIX."groupMember` SET `clientid` = '".$clientid['clientid']."', `groupids` = '".$groupid.";'" );
				}
				else
				{
					$groupids = query_fetch_assoc( "SELECT `groupids` FROM `".DBPREFIX."groupMember` WHERE `clientid` = '".$clientid['clientid']."'" );
					###
					query_basic( "UPDATE `".DBPREFIX."groupMember` SET `groupids` = '".$groupids['groupids'].$groupid.";' WHERE `clientid` = '".$clientid['clientid']."'" );
					###
					unset($groupids);
				}
			}
			unset($clientid);
			###
			$_SESSION['msg1'] = T_('New Client Successfully Added!');
			$_SESSION['msg2'] = $newClient.T_(' has been added to the group.');
			$_SESSION['msg-type'] = 'success';
		}
		header( "Location: configgroupedit.php?id=".urlencode($groupid) );
		die();
		break;

	case 'configgroupdelete':
		$groupid = $_GET['id'];
		###
		$error = '';
		###
		if (!is_numeric($groupid))
		{
			$error .= T_('Invalid GroupID. ');
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."group` WHERE `groupid` = '".$groupid."'" ) == 0)
		{
			$error .= T_('Invalid GroupID. ');
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
		if (query_numrows( "SELECT `serverid` FROM `".DBPREFIX."server` WHERE `groupid` = '".$groupid."'" ) != 0)
		{
			$_SESSION['msg1'] = T_('Error!');
			$_SESSION['msg2'] = T_('The selected group cannot be deleted as it is currently linked with a game server. The server must be deleted first.');
			$_SESSION['msg-type'] = 'error';
			header( "Location: configgroup.php" );
			die();
		}
		###
		if (getGroupClients($groupid) != FALSE)
		{
			$clients = getGroupClients($groupid);
			foreach($clients as $key => $value)
			{
				$removeids[] = $value;
			}
			unset($clients);
		}
		###
		if (isset($removeids))
		{
			// Remove groupID from groupMember table
			foreach($removeids as $key => $value)
			{
				$groupids = query_fetch_assoc( "SELECT `groupids` FROM `".DBPREFIX."groupMember` WHERE `clientid` = '".$value."'" );
				###
				$groupids['groupids'] = str_replace( $groupid.';', '', $groupids['groupids'] );
				###
				if (empty($groupids['groupids']))
				{
					query_basic( "DELETE FROM `".DBPREFIX."groupMember` WHERE `clientid` = '".$value."' LIMIT 1" );
				}
				else
				{
					query_basic( "UPDATE `".DBPREFIX."groupMember` SET `groupids` = '".$groupids['groupids']."' WHERE `clientid` = '".$value."'" );
				}
				unset($groupids);
			}
		}
		###
		query_basic( "DELETE FROM `".DBPREFIX."group` WHERE `groupid` = '".$groupid."' LIMIT 1" );
		###
		$_SESSION['msg1'] = T_('Group Deleted Successfully!');
		$_SESSION['msg2'] = T_('The selected group has been removed.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: configgroup.php" );
		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>
