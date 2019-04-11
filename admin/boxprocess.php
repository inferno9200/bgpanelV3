<?php
$return = TRUE;
require("../configuration.php");
require("./include.php");
require '../libs/vendor/autoload.php';
use phpseclib\Crypt\AES;
use phpseclib\Crypt\Random;
use phpseclib\Net\SSH2;

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
	case 'boxadd':
		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$ip = mysqli_real_escape_string($conn, $_POST['ip']);
		$login = mysqli_real_escape_string($conn, $_POST['login']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$password2 = mysqli_real_escape_string($conn, $_POST['password2']);
		$sshport = mysqli_real_escape_string($conn, $_POST['sshport']);
		$notes = mysqli_real_escape_string($conn, $_POST['notes']);
		if (isset($_POST['verify'])) {
			$verify = 'on';
		} else {
			$verify = '';
		}
		###
		//Used to fill in the blanks of the form
		$_SESSION['name'] = $name;
		$_SESSION['ip'] = $ip;
		$_SESSION['login'] = $login;
		$_SESSION['sshport'] = $sshport;
		$_SESSION['notes'] = $notes;
		###
		//Check the inputs. Output an error if the validation failed
		$nameLength = strlen($name);
		###
		$error = '';
		###
		if ($nameLength < 2)
		{
			$error .= T_('Box Name is too short (2 Chars min.). ');
		}
		if (!validateIP($ip))
		{
			$error .= T_('Invalid IP. ');
		}
		else if (query_numrows( "SELECT `boxid` FROM `".DBPREFIX."box` WHERE `ip` = '".$ip."' && `login` = '".$login."'" ) != 0)
		{
			$error .= T_('This IP is already in use with the same login ! ');
		}
		if (empty($login))
		{
			$error .= T_('No SSH login ! ');
		}
		if (empty($password))
		{
			$error .= T_('SSH Password is not set. ');
		}
		else if ($password != $password2)
		{
			$error .= T_("SSH Passwords don't match. ");
		}
		if (empty($sshport))
		{
			$sshport = 22;
		}
		else if (!is_numeric($sshport))
		{
			$error .= T_('SSH Port is not a numeric value ! ');
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: boxadd.php" );
			die();
		}
		###
		//Check SSH2 connection if specified
		if ($verify == 'on')
		{
			// Get SSH2 Object OR ERROR String
			$ssh = new SSH2($ip, $sshport);
			
			if (!$ssh->login($login, $password)) {
				$_SESSION['msg1'] = T_('Connection Error!');
				$_SESSION['msg2'] = T_('Please check your ip/password or username and ssh port!');
				$_SESSION['msg-type'] = 'error';
				header( "Location: boxadd.php" );
				die();
			}
			$ssh->disconnect();
		}
		###
		//As the form has been validated, vars are useless
		unset($_SESSION['name']);
		unset($_SESSION['ip']);
		unset($_SESSION['login']);
		unset($_SESSION['sshport']);
		unset($_SESSION['notes']);
		###
		//Security
		$sshport = abs($sshport);
		###
		//Adding the box to the database
		$passphrased = file_get_contents('../.ssh/passphrase');
		$aes = new AES(AES::MODE_ECB);
		$aes->setKeyLength(256);
		$aes->setKey($passphrased);
		$sql = ( "INSERT INTO `".DBPREFIX."box` SET
			`name` = '".$name."',
			`ip` = '".$ip."',
			`login` = '".$login."',
			`password` = '".mysqli_real_escape_string($conn, $aes->encrypt($password))."',
			`sshport` = '".$sshport."',
			`notes` = '".$notes."'" );
		###
		mysqli_query($conn, $sql); // it has to be executed here otherwise $mysqli_insert_id isn't working! #fix_later
		$boxid = mysqli_insert_id($conn);
		###
		// Check if the password has been correctly stored
		$boxPasswd = query_fetch_assoc( "SELECT `password` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
		if ( $aes->decrypt($boxPasswd['password']) != $password ) {
			query_basic( "DELETE FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );

			$_SESSION['msg1'] = T_('Malformed Box Password!');
			$_SESSION['msg2'] = T_('The password stored in your MySQL Database for this box is corrupted. Cancelling...');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxadd.php" );
			die();
		}
		###
		//Addin box ip
		query_basic( "INSERT INTO `".DBPREFIX."boxIp` SET
			`boxid` = '".$boxid."',
			`ip` = '".$ip."'" );
		//Adding cache
		$boxCache =	array(
			$boxid => array(
				'players'	=> array('players' => 0),

				'bandwidth'	=> array('rx_usage' => 0,
									 'tx_usage' => 0,
									 'rx_total' => 0,
									 'tx_total' => 0),

				'cpu'		=> array('proc' => '',
									 'cores' => 0,
									 'usage' => 0),

				'ram'		=> array('total' => 0,
									 'used' => 0,
									 'free' => 0,
									 'usage' => 0),

				'loadavg'	=> array('loadavg' => '0.00'),
				'hostname'	=> array('hostname' => ''),
				'os'		=> array('os' => ''),
				'date'		=> array('date' => ''),
				'kernel'	=> array('kernel' => ''),
				'arch'		=> array('arch' => ''),
				'uptime'	=> array('uptime' => ''),

				'swap'		=> array('total' => 0,
									 'used' => 0,
									 'free' => 0,
									 'usage' => 0),

				'hdd'		=> array('total' => 0,
									 'used' => 0,
									 'free' => 0,
									 'usage' => 0)
			)
		);
		query_basic( "UPDATE `".DBPREFIX."box` SET `cache` = '".mysqli_real_escape_string($conn, gzcompress(serialize($boxCache), 2))."' WHERE `boxid` = '".$boxid."'" );
		###
		//Adding event to the database
		$message = "Box Added: ".$name;
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `boxid` = '".$boxid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		$_SESSION['msg1'] = T_('Box Added Successfully!');
		$_SESSION['msg2'] = T_('The box has been added and is ready for use.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: boxsummary.php?id=".urlencode($boxid) );
		die();
		break;

	case 'boxprofile':
		$boxid = mysqli_real_escape_string($conn, $_POST['boxid']);
		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$ip = mysqli_real_escape_string($conn, $_POST['ip']);
		$login = mysqli_real_escape_string($conn, $_POST['login']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);
		$sshport = mysqli_real_escape_string($conn, $_POST['sshport']);
		$notes = mysqli_real_escape_string($conn, $_POST['notes']);
		if (isset($_POST['verify'])) {
			$verify = 'on';
		} else {
			$verify = '';
		}
		###
		//Check the inputs. Output an error if the validation failed
		$nameLength = strlen($name);
		###
		$error = '';
		###
		if (!is_numeric($boxid))
		{
			$error .= T_('Invalid BoxID. ');
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
		{
			$error .= T_('Invalid BoxID. ');
		}
		###
		if ($nameLength < 2)
		{
			$error .= T_('Box Name is too short (2 Chars min.). ');
		}
		if (!validateIP($ip))
		{
			$error .= T_('Invalid IP. ');
		}
        else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `ip` = '".$ip."' && `login` = '".$login."' && `boxid` != '".$boxid."'" ) != 0)
        {
			$error .= T_('This IP is already in use with the same login ! ');
		}
		if (empty($login))
		{
			$error .= T_('No SSH login ! ');
		}
		if (empty($sshport))
		{
			$sshport = 22;
		}
		else if (!is_numeric($sshport))
		{
			$error .= T_('SSH Port is not a numeric value ! ');
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error! Form has been reset!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: boxprofile.php?id=".urlencode($boxid) );
			die();
		}
		###
		//Security
		$sshport = abs($sshport);
		###
		//Check SSH2 connection if specified
		if ($verify == 'on')
		{
			if (empty($password))
			{
				// Get SSH Password
				$passwd = query_fetch_assoc( "SELECT `password` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
				$aes = new Crypt_AES();
				$aes->setKeyLength(256);
				$aes->setKey(CRYPT_KEY);
				$password = $aes->decrypt($passwd['password']);
				unset($passwd);
			}
			// Get SSH2 Object OR ERROR String
			$ssh = newNetSSH2($ip, $sshport, $login, $password);
			if (!is_object($ssh))
			{
				$_SESSION['msg1'] = T_('Connection Error!');
				$_SESSION['msg2'] = $ssh;
				$_SESSION['msg-type'] = 'error';
				header( "Location: boxprofile.php?id=".urlencode($boxid) );
				die();
			}
			$ssh->disconnect();
		}

		//Processing password
		if (empty($password)) //No password provided, we keep the encrypted one that is stored into database
		{
			$passwd = query_fetch_assoc( "SELECT `password` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
			$password = $passwd['password'];
			unset($passwd);
		}
		else
		{
			$aes = new Crypt_AES();
			$aes->setKeyLength(256);
			$aes->setKey(CRYPT_KEY);
			$password = $aes->encrypt($password);
		}

		// Backup old password
		$oldAuth = query_fetch_assoc( "SELECT `login`, `password` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );

		// Backup old ip
		$oldIp = query_fetch_assoc( "SELECT `ip` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );

		// Updating
		query_basic( "UPDATE `".DBPREFIX."box` SET
		  `name` = '".$name."',
		  `ip` = '".$ip."',
		  `login` = '".$login."',
		  `password` = '".mysqli_real_escape_string($conn, $password)."',
		  `sshport` = '".$sshport."',
		  `notes` = '".$notes."' WHERE `boxid` = '".$boxid."'" );

		// Check if the password has been correctly stored
		$boxPasswd = query_fetch_assoc( "SELECT `password` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
		if ( $aes->decrypt($boxPasswd['password']) != $aes->decrypt($password) ) {
			query_basic( "UPDATE `".DBPREFIX."box` SET `login` = '".mysqli_real_escape_string($conn, $oldAuth['login'])."', `password` = '".mysqli_real_escape_string($conn, $oldAuth['password'])."' WHERE `boxid` = '".$boxid."'" );

			$_SESSION['msg1'] = T_('Malformed Box Password!');
			$_SESSION['msg2'] = T_('The password stored in your MySQL Database for this box is corrupted. Old password kept...');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxprofile.php?id=".urlencode($boxid) );
			die();
		}

		query_basic( "UPDATE `".DBPREFIX."boxIp` SET `ip` = '".$ip."' WHERE `boxid` = '".$boxid."' && `ip` = '".$oldIp['ip']."'" );

		//Adding event to the database
		$message = "Box Edited: ".$name;
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `boxid` = '".$boxid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		$_SESSION['msg1'] = T_('Box Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to the box have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: boxsummary.php?id=".urlencode($boxid) );
		die();
		break;

	case 'boxnotes':
		$boxid = mysqli_real_escape_string($conn, $_POST['boxid']);
		$notes = mysqli_real_escape_string($conn, $_POST['notes']);
		###
		$error = '';
		###
		if (!is_numeric($boxid))
		{
			$error .= T_('Invalid BoxID. ');
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
		{
			$error .= T_('Invalid BoxID. ');
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
		query_basic( "UPDATE `".DBPREFIX."box` SET `notes` = '".$notes."' WHERE `boxid` = '".$boxid."'" );
		###
		$_SESSION['msg1'] = T_('Admin Notes Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to the admin notes have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: boxsummary.php?id=".urlencode($boxid) );
		die();
		break;

	case 'boxdelete':
		$boxid = $_GET['id'];
		###
		$error = '';
		###
		if (!is_numeric($boxid))
		{
			$error .= T_('Invalid BoxID. ');
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
		{
			$error .= T_('Invalid BoxID. ');
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
		if (query_numrows( "SELECT `serverid` FROM `".DBPREFIX."server` WHERE `boxid` = '".$boxid."'" ) != 0)
		{
			$_SESSION['msg1'] = T_('Error!');
			$_SESSION['msg2'] = T_('Assigned servers must be deleted first.');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxsummary.php?id=".urlencode($boxid) );
			die();
		}
		$rows = query_fetch_assoc( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
		###
		query_basic( "DELETE FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
		query_basic( "DELETE FROM `".DBPREFIX."boxIp` WHERE `boxid` = '".$boxid."'" );
		###
		//Adding event to the database
		$message = 'Box Deleted: '.mysqli_real_escape_string($conn, $rows['name']);
		###
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `boxid` = '".$boxid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		$_SESSION['msg1'] = T_('Box Deleted Successfully!');
		$_SESSION['msg2'] = T_('The selected box has been removed.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: box.php" );
		die();
		break;

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

	case 'boxipedit':
		$boxid = mysqli_real_escape_string($conn, $_POST['boxid']);
		$newip = mysqli_real_escape_string($conn, $_POST['newip']);
		###
		// New IP Verify
		if (isset($_POST['verify'])) {
			$verify = 'on';
		} else {
			$verify = '';
		}
		###
		// Get IPs for Removal
		$ips = mysqli_query($conn, "SELECT `ipid` FROM `".DBPREFIX."boxIp` WHERE `boxid` = '".$boxid."' ORDER BY `ipid`" );
		while ($rowsIps = mysqli_fetch_assoc($ips)) {
			$removeValue = 'removeid' . $rowsIps['ipid'];
			if ( isset($_POST[$removeValue]) && $_POST[$removeValue] == 'on' ) {
				$removeids[] = $rowsIps['ipid'];
			}
		}
		unset($ips);
		###
		$error = '';
		###
		if (!is_numeric($boxid))
		{
			$error .= T_('Invalid BoxID. ');
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
		{
			$error .= T_('Invalid BoxID. ');
		}
		###
		if (!empty($newip))
		{
			// Add IP
			if (!validateIP($newip))
			{
				$error .= T_('Invalid IP. ');
			}
			else if (query_numrows( "SELECT `ipid` FROM `".DBPREFIX."boxIp` WHERE `ip` = '".$newip."' && `boxid` = '".$boxid."'" ) != 0)
			{
				$error .= T_('This IP is already in use ! ');
			}
		}
		else
		{
			// Remove IPs
			if (isset($removeids)) {
				foreach ($removeids as $key => $value)
				{
					$ip = query_fetch_assoc( "SELECT `ip` FROM `".DBPREFIX."boxIp` WHERE `ipid` = '".$value."' && `boxid` = '".$boxid."' LIMIT 1" );
					if (
						(query_numrows( "SELECT `boxid` FROM `".DBPREFIX."box` WHERE `ip` = '".$ip['ip']."' && `boxid` = '".$boxid."'" ) != 0) ||
						(query_numrows( "SELECT `serverid` FROM `".DBPREFIX."server` WHERE `ipid` = '".$value."' && `boxid` = '".$boxid."'" ) != 0)
						) {
						// Passive security
						unset($removeids[$key]);
					}
				}
			}
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = T_('Validation Error! Form has been reset!');
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: boxip.php?id=".urlencode($boxid) );
			die();
		}
		###
		if (!empty($newip))
		{
			//Check SSH2 connection if specified
			list($sshport, $login, $password) = mysqli_fetch_array(mysqli_query($conn, "SELECT `sshport`, `login`, `password` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" ));
			$aes = new Crypt_AES();
			$aes->setKeyLength(256);
			$aes->setKey(CRYPT_KEY);
			$password = $aes->decrypt($password);
			if ($verify == 'on')
			{
				// Get SSH2 Object OR ERROR String
				$ssh = newNetSSH2($newip, $sshport, $login, $password);
				if (!is_object($ssh))
				{
					$_SESSION['msg1'] = T_('Connection Error!');
					$_SESSION['msg2'] = $ssh;
					$_SESSION['msg-type'] = 'error';
					header( "Location: boxip.php?id=".urlencode($boxid) );
					die();
				}
				$ssh->disconnect();
			}
			// Add IP
			query_basic( "INSERT INTO `".DBPREFIX."boxIp` SET `boxid` = '".$boxid."', `ip` = '".$newip."'" );

		}
		else
		{
			// Remove IPs
			if (isset($removeids)) {
				foreach ($removeids as $value) {
					query_basic( "DELETE FROM `".DBPREFIX."boxIp` WHERE `ipid` = '".$value."' LIMIT 1" );
				}
			}
		}
		###
		$_SESSION['msg1'] = T_('Box Updated Successfully!');
		$_SESSION['msg2'] = T_('Your changes to the box have been saved.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: boxip.php?id=".urlencode($boxid) );
		die();
		break;


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

	case 'makeRepo':
		require_once("../libs/gameinstaller/gameinstaller.php");
		###
		$boxid = mysqli_real_escape_string($conn, $_GET['boxid']);
		$gameid = mysqli_real_escape_string($conn, $_GET['gameid']);
		###
		if (!is_numeric($boxid))
		{
			exit( T_('Invalid BoxID. ') );
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
		{
			exit( T_('Invalid BoxID. ') );
		}
		if (!is_numeric($gameid))
		{
			exit( T_('Invalid GameID. ') );
		}
		else if (query_numrows( "SELECT `game` FROM `".DBPREFIX."game` WHERE `gameid` = '".$gameid."'" ) == 0)
		{
			exit( T_('Invalid GameID. ') );
		}
		###
		$box = query_fetch_assoc( "SELECT `ip`, `login`, `password`, `sshport`, `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
		$game = query_fetch_assoc( "SELECT `game`, `cachedir` FROM `".DBPREFIX."game` WHERE `gameid` = '".$gameid."' LIMIT 1" );
		###
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);
		###
		// Get SSH2 Object OR ERROR String
		$ssh = newNetSSH2($box['ip'], $box['sshport'], $box['login'], $aes->decrypt($box['password']));
		if (!is_object($ssh))
		{
			$_SESSION['msg1'] = T_('Connection Error!');
			$_SESSION['msg2'] = $ssh;
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		###
		$gameInstaller = new GameInstaller( $ssh );
		###
		$setGame = $gameInstaller->setGame( $game['game'] );
		if ($setGame == FALSE) {
			$_SESSION['msg1'] = T_('Game Installer Error!');
			$_SESSION['msg2'] = T_('Game Not Supported');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		$setRepoPath = $gameInstaller->setRepoPath( $game['cachedir'], TRUE );
		if ($setRepoPath == FALSE) {
			$_SESSION['msg1'] = T_('Unable To Make Game Cache Repository!');
			$_SESSION['msg2'] = T_('Unable To Set Repository Directory');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		$opStatus = $gameInstaller->checkOperation( 'makeRepo' );
		if ($opStatus == TRUE) {
			$_SESSION['msg1'] = T_('Unable To Make Game Cache Repository!');
			$_SESSION['msg2'] = T_('Operation In Progress For This Repository Or Repository Locked For Server Side Operation!');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		$makeRepo = $gameInstaller->makeRepo( );
		if ($makeRepo == FALSE) {
			$_SESSION['msg1'] = T_('Unable To Make Game Cache Repository!');
			$_SESSION['msg2'] = T_('Internal Error');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		###
		//Adding event to the database
		$message = "Repository Created for ".mysqli_real_escape_string($conn, $game['game'] )." on ".mysqli_real_escape_string($conn, $box['name'] );
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `boxid` = '".$boxid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		$_SESSION['msg1'] = T_('Making Game Cache Repository!');
		$_SESSION['msg2'] = T_('Your game cache repository is currently being created. Please wait...');
		$_SESSION['msg-type'] = 'success';
		header( "Location: boxgamefile.php?id=".urlencode($boxid) );
		die();
		break;

	case 'abortOperation':
		require_once("../libs/gameinstaller/gameinstaller.php");
		###
		$boxid = mysqli_real_escape_string($conn, $_GET['boxid']);
		$gameid = mysqli_real_escape_string($conn, $_GET['gameid']);
		###
		if (!is_numeric($boxid))
		{
			exit( T_('Invalid BoxID. ') );
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
		{
			exit( T_('Invalid BoxID. ') );
		}
		if (!is_numeric($gameid))
		{
			exit( T_('Invalid GameID. ') );
		}
		else if (query_numrows( "SELECT `game` FROM `".DBPREFIX."game` WHERE `gameid` = '".$gameid."'" ) == 0)
		{
			exit( T_('Invalid GameID. ') );
		}
		###
		$box = query_fetch_assoc( "SELECT `ip`, `login`, `password`, `sshport`, `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
		$game = query_fetch_assoc( "SELECT `game`, `cachedir` FROM `".DBPREFIX."game` WHERE `gameid` = '".$gameid."' LIMIT 1" );
		###
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);
		###
		// Get SSH2 Object OR ERROR String
		$ssh = newNetSSH2($box['ip'], $box['sshport'], $box['login'], $aes->decrypt($box['password']));
		if (!is_object($ssh))
		{
			$_SESSION['msg1'] = T_('Connection Error!');
			$_SESSION['msg2'] = $ssh;
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		###
		$gameInstaller = new GameInstaller( $ssh );
		###
		$gameInstaller->setRepoPath( $game['cachedir'] );
		$gameInstaller->abortOperation( 'makeRepo' );
		###
		//Adding event to the database
		$message = "Operation Aborted for ".mysqli_real_escape_string($conn, $game['game'] )." on ".mysqli_real_escape_string($conn, $box['name'] );
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `boxid` = '".$boxid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		$_SESSION['msg1'] = T_('Warning: Operation Aborted!');
		$_SESSION['msg2'] = '';
		$_SESSION['msg-type'] = 'warning';
		header( "Location: boxgamefile.php?id=".urlencode($boxid) );
		die();
		break;

	case 'deleteRepo':
		require_once("../libs/gameinstaller/gameinstaller.php");
		###
		$boxid = mysqli_real_escape_string($conn, $_GET['boxid']);
		$gameid = mysqli_real_escape_string($conn, $_GET['gameid']);
		###
		if (!is_numeric($boxid))
		{
			exit( T_('Invalid BoxID. ') );
		}
		else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
		{
			exit( T_('Invalid BoxID. ') );
		}
		if (!is_numeric($gameid))
		{
			exit( T_('Invalid GameID. ') );
		}
		else if (query_numrows( "SELECT `game` FROM `".DBPREFIX."game` WHERE `gameid` = '".$gameid."'" ) == 0)
		{
			exit( T_('Invalid GameID. ') );
		}
		###
		$box = query_fetch_assoc( "SELECT `ip`, `login`, `password`, `sshport`, `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
		$game = query_fetch_assoc( "SELECT `game`, `cachedir` FROM `".DBPREFIX."game` WHERE `gameid` = '".$gameid."' LIMIT 1" );
		###
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);
		###
		// Get SSH2 Object OR ERROR String
		$ssh = newNetSSH2($box['ip'], $box['sshport'], $box['login'], $aes->decrypt($box['password']));
		if (!is_object($ssh))
		{
			$_SESSION['msg1'] = T_('Connection Error!');
			$_SESSION['msg2'] = $ssh;
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		###
		$gameInstaller = new GameInstaller( $ssh );
		###
		$gameInstaller->setRepoPath( $game['cachedir'] );
		###
		$opStatus = $gameInstaller->checkOperation( 'makeRepo' );
		if ($opStatus == TRUE) {
			$_SESSION['msg1'] = T_('Unable To Delete Game Cache Repository!');
			$_SESSION['msg2'] = T_('Operation In Progress For This Repository Or Repository Locked For Server Side Operation!');
			$_SESSION['msg-type'] = 'error';
			header( "Location: boxgamefile.php?id=".urlencode($boxid) );
			die();
		}
		###
		$gameInstaller->deleteRepo( );
		###
		//Adding event to the database
		$message = "Repository Deleted for ".mysqli_real_escape_string($conn, $game['game'] )." on ".mysqli_real_escape_string($conn, $box['name'] );
		query_basic( "INSERT INTO `".DBPREFIX."log` SET `boxid` = '".$boxid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
		###
		$_SESSION['msg1'] = T_('Warning: Repository Deleted!');
		$_SESSION['msg2'] = T_('Repository files are under deletion.');
		$_SESSION['msg-type'] = 'warning';
		header( "Location: boxgamefile.php?id=".urlencode($boxid) );
		die();
		break;

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>