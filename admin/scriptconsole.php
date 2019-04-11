<?php
$page = 'scriptconsole';
$tab = 5;
$isSummary = TRUE;
###
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
	$scriptid = $_GET['id'];
}
else
{
	exit('Error:ScriptID error.');
}
###
$return = 'scriptconsole.php?id='.urlencode($scriptid);


require("../configuration.php");
require("./include.php");
require("../includes/func.ssh2.inc.php");
require_once("../libs/phpseclib/Crypt/AES.php");
require_once("../libs/phpseclib/ANSI.php");


$title = T_('Script Console');


if (query_numrows( "SELECT `name` FROM `".DBPREFIX."script` WHERE `scriptid` = '".$scriptid."'" ) == 0)
{
	exit('Error: ScriptID is invalid.');
}

$rows = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."script` WHERE `scriptid` = '".$scriptid."' LIMIT 1" );


if ($rows['status'] != 'Active')
{
	exit('Validation Error! The script is disabled!');
}
else
{
	$box = query_fetch_assoc( "SELECT `ip`, `login`, `password`, `sshport` FROM `".DBPREFIX."box` WHERE `boxid` = '".$rows['boxid']."' LIMIT 1" );
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
		header( 'Location: index.php' );
		die();
	}

	$ansi = new File_ANSI();

	$screen = $rows['screen'];
	if (empty($screen)) {
		$screen = preg_replace('#[^a-zA-Z0-9]#', "_", $rows['name']);
	}

	// We retrieve screen name ($session)
	$session = $ssh->exec( "screen -ls | awk '{ print $1 }' | grep '^[0-9]*\.".$screen."$'"."\n" );
	$session = trim($session);

	if ($rows['type'] == '1')
	{
		if (!empty($_GET['cmd']) && !empty($session))
		{
			$cmdRcon = $_GET['cmd'];

			// We prepare and we send the command into the screen
			$cmd = "screen -S ".$session." -p 0 -X stuff \"".$cmdRcon."\"`echo -ne '\015'`";
			$ssh->exec($cmd."\n");
			unset($cmd);

			// Adding event to the database
			$message = 'Script command ('.mysqli_real_escape_string($conn, $cmdRcon).') sent to : '.mysqli_real_escape_string($conn, $rows['name']);
			query_basic( "INSERT INTO `".DBPREFIX."log` SET `scriptid` = '".$scriptid."', `message` = '".$message."', `name` = '".mysqli_real_escape_string($conn, $_SESSION['adminfirstname'])." ".mysqli_real_escape_string($conn, $_SESSION['adminlastname'])."', `ip` = '".$_SERVER['REMOTE_ADDR']."'" );
			unset($cmdRcon);

			header( 'Location: scriptconsole.php?id='.urlencode($scriptid) );
			die();
		}
	}

	// We retrieve screen contents
	if (!empty($session)) {
		$ssh->write("screen -R ".$session."\n");
		$ssh->setTimeout(1);

		@$ansi->appendString($ssh->read());
		$screenContents = htmlspecialchars_decode(strip_tags($ansi->getScreen()));
	}
	else {
		$screenContents = "The Script is not running...\n";
	}

	$ssh->disconnect();
}


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<ul class="nav nav-tabs">
				<li><a href="scriptsummary.php?id=<?php echo $scriptid; ?>">Summary</a></li>
				<li><a href="scriptprofile.php?id=<?php echo $scriptid; ?>">Profile</a></li>
				<li class="active"><a href="scriptconsole.php?id=<?php echo $scriptid; ?>">Console</a></li>
			</ul>
			<script type="text/javascript">
			$(document).ready(function() {
				prettyPrint();
			});
			</script>
			<div class="page-header">
				<h1><small><?php echo htmlspecialchars($rows['name'], ENT_QUOTES); ?></small></h1>
			</div>
<pre class="prettyprint">
<?php

// Each lines are a value of rowsTable
$rowsTable = explode("\n", $screenContents);

// Output
foreach ($rowsTable as $key => $value)
{
	echo htmlentities($value, ENT_QUOTES);
}

?>

</pre>
				<div style="text-align: center;">
<?php

if ($rows['type'] == '1' && !empty($session))
{
?>
					<form class="form-inline" method="get" action="scriptconsole.php">
						<input type="hidden" name="id" value="<?php echo $rows['scriptid']; ?>" />
						<div class="input-prepend input-append">
							<span class="add-on"><?php echo T_('Command'); ?>:</span>
							<input type="text" name="cmd" class="input-xlarge" placeholder="<?php echo T_('Your Command'); ?>">
							<button type="submit" class="btn">
								<?php echo T_('Send'); ?>
							</button>
							<button class="btn" onclick="window.location.reload();">
								<?php echo T_('Refresh'); ?>
							</button>
						</div>
					</form>
<?php
}
else
{
?>
					<button class="btn" onclick="window.location.reload();">
						<?php echo T_('Refresh'); ?>
					</button>
<?php
}

?>
				</div>
				<hr/>
				<div style="text-align: center; margin-top: 19px;">
					<ul class="pager">
						<li>
							<a href="script.php"><?php echo T_('Back to Scripts'); ?></a>
						</li>
					</ul>
				</div>
<?php


include("./bootstrap/footer.php");
?>