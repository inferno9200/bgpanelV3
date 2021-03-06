<?php
$return = TRUE;


require("configuration.php");
require("include.php");


//---------------------------------------------------------+

if (isset($_GET['serverid']) && is_numeric($_GET['serverid']))
{
	if (query_numrows( "SELECT `name` FROM `".DBPREFIX."server` WHERE `serverid` = '".$_GET['serverid']."'" ) == 0)
	{
		exit('Error: Server is invalid.');
	}
	else
	{
		$serverid = $_GET['serverid'];
		$step = 'rcon';
	}
}
else
{
	die();
}

//---------------------------------------------------------+


switch ($step)
{

//------------------------------------------------------------------------------------------------------------+



	case 'rcon':
		require("./includes/func.ssh2.inc.php");
		require_once("./libs/phpseclib/Crypt/AES.php");
		require_once("./libs/phpseclib/ANSI.php");

		$error = '';

		if (empty($serverid))
		{
			$error .= T_('No ServerID specified for server validation !');
		}
		else
		{
			if (!is_numeric($serverid))
			{
				$error .= T_('Invalid ServerID. ');
			}
			else if (query_numrows( "SELECT `name` FROM `".DBPREFIX."server` WHERE `serverid` = '".$serverid."'" ) == 0)
			{
				$error .= T_('Invalid ServerID. ');
			}
		}

		if (!empty($error))
		{
			die();
		}

		$panelstatus = query_fetch_assoc( "SELECT `panelstatus` FROM `".DBPREFIX."server` WHERE `serverid` = '".$serverid."' LIMIT 1" );
		if ($panelstatus['panelstatus'] != 'Started')
		{
			die();
		}

		$status = query_fetch_assoc( "SELECT `status` FROM `".DBPREFIX."server` WHERE `serverid` = '".$serverid."' LIMIT 1" );
		if ($status['status'] != 'Active')
		{
			die();
		}

		$server = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."server` WHERE `serverid` = '".$serverid."' LIMIT 1" );
		$box = query_fetch_assoc( "SELECT `ip`, `login`, `password`, `sshport` FROM `".DBPREFIX."box` WHERE `boxid` = '".$server['boxid']."' LIMIT 1" );

		// Rights
		$checkGroup = checkClientGroup($server['groupid'], $_SESSION['clientid']);
		if ($checkGroup == FALSE)
		{
			die();
		}

		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);

		// Get SSH2 Object OR ERROR String
		$ssh = newNetSSH2($box['ip'], $box['sshport'], $box['login'], $aes->decrypt($box['password']));
		if (!is_object($ssh))
		{
			die();
		}

		$ansi = new File_ANSI();

		// We retrieve screen name ($session)
		$session = $ssh->exec( "screen -ls | awk '{ print $1 }' | grep '^[0-9]*\.".escapeshellcmd($server['screen'])."$'"."\n" );
		$session = trim($session);
		
		if (!$session || $session == '') {
			die();
		}

		// We retrieve screen contents
		$ssh->write("screen -R ".$session."\n");
		$ssh->setTimeout(2);

		@$ansi->appendString($ssh->read());
		$screenContents = htmlspecialchars_decode(strip_tags($ansi->getScreen()));

		$ssh->disconnect();
		unset($session);

		// Each lines are a value of rowsTable
		$rowsTable = explode("\n", $screenContents);

		// Output
		foreach ($rowsTable as $key => $value)
		{
			if (isset($value) && trim($value) != '')
				echo str_replace('\n', '', htmlentities($value, ENT_QUOTES));
		}

?>

<?php
		die();
		break;



//------------------------------------------------------------------------------------------------------------+

}



?>
