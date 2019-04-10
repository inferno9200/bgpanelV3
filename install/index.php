<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
if (!is_file('../configuration.php'))
{
	exit('<html><body><h1>Configuration file not found !</h1></body></html>');
}
else
{
	require('../configuration.php');
}

define('WIZARDVERSION', 'v1.7.0');
require('./inc/versions.php');
require('./inc/mysql.php');

//---------------------------------------------------------+

if (isset($_POST['task']))
{
	$task = $_POST['task'];
}

switch (@$task)
{
	case 'license':
		if ( isset($_POST['license']) )
		{
			if ($_POST['license'] == 'on')
			{
				header( "Location: index.php?step=one" );
				die();
			}
		}
		exit( "Make sure you've check the notes!" );
		break;

	default:
		break;
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Installation Script</title>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<script src="./bootstrap/js/jquery.js"></script>
			<script src="./bootstrap/js/bootstrap.js"></script>
			<link href="./bootstrap/css/bootstrap.css" rel="stylesheet">
			<link href="./bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
			<link rel="shortcut icon" href="./bootstrap/img/favicon.ico">
	</head>
	<body>
			<div class="container">
				<div class="page-header">
					<h1><center>Installation Script BGP V3</center></h1>
				</div>
				<ul class="breadcrumb">
<?php

//---------------------------------------------------------+

if (!isset($_GET['step'])) // Step == 'zero'
{
?>
					<li class="active">Notes & Readme</li>
<?php
}
else if ($_GET['step'] == 'one')
{
?>
					<li>
						<a href="index.php">Notes & Readme</a> <span class="divider">/</span>
					</li>
					<li class="active">Check Requirements</li>
<?php
}
else if ($_GET['step'] == 'two')
{
?>
					<li>
						<a href="index.php">Notes & Readme</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="index.php?step=one">Check Requirements</a> <span class="divider">/</span>
					</li>
					<li class="active">Select Database Update</li>
<?php
}
else if ($_GET['step'] == 'three')
{
?>
					<li>
						<a href="index.php">Notes & Readme</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="index.php?step=one">Check Requirements</a> <span class="divider">/</span>
					</li>
					<li>
						<a href="index.php?step=two">Select Database Update</a> <span class="divider">/</span>
					</li>
					<li class="active">Install Database</li>
<?php
}

//---------------------------------------------------------+

?>
				</ul>
<?php

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

if (!isset($_GET['step']))
{
?>
				<div class="well">
					<div style="width:auto;height:480px;overflow:scroll;overflow-y:scroll;overflow-x:hidden;">
<?php
	$license = fopen('../README.md', 'r');

	while ($rows = fgets($license))
	{
		echo $rows.'<br />';
	}

	fclose($license);
?>
					</div>
				</div>
				<form method="post" action="index.php">
					<input type="hidden" name="task" value="license" />
					<label class="checkbox">
						<input type="checkbox" name="license">&nbsp;I have read all notes.
					</label>
					<div style="text-align: center; margin-top: 19px;">
						<button type="submit" class="btn">Submit</button>
					</div>
				</form>
<?php
}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



else if ($_GET['step'] == 'one')
{
?>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Action</th>
							<th>Status</th>
							<th>Note</th>
						</tr>
					</thead>
					<tbody>
						<tr class="success">
							<td>Checking for CONFIGURATION file</td>
							<td><span class="label label-success">FOUND</span></td>
							<td></td>
						</tr>
<?php

	$versioncompare = version_compare(PHP_VERSION, '5.6');
	if ($versioncompare == -1)
	{
?>
						<tr class="error">
							<td>Checking your version of PHP</td>
							<td><span class="label label-important">FAILED (<?php echo PHP_VERSION; ?>)</span></td>
							<td>Upgrade to PHP 7.0</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking your version of PHP</td>
							<td><span class="label label-success"><?php echo PHP_VERSION; ?></span></td>
							<td></td>
						</tr>
<?php
	}
	unset($versioncompare);

?>
<?php

	if (ini_get('safe_mode'))
	{
?>
						<tr class="error">
							<td>Checking for PHP safe mode</td>
							<td><span class="label label-important">ON</span></td>
							<td>Please, disable safe mode !!!</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for PHP safe mode</td>
							<td><span class="label label-success">OFF</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('mysqli'))
	{
?>
						<tr class="error">
							<td>Checking for MySQL extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>MySQL extension could not be found or is not installed. Please recompile your Apache with the MySQL extension included.</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for MySQL extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php

		$mysql_link = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
		if ($mysql_link == FALSE)
		{
?>
						<tr class="error">
							<td>Checking for MySQL server connection</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>Could not connect to MySQL: "<?php echo mysqli_error($conn); ?>"</td>
						</tr>
<?php
			$error = TRUE;
		}
		else
		{
?>
						<tr class="success">
							<td>Checking for MySQL server connection</td>
							<td><span class="label label-success">SUCCESSFUL</span></td>
							<td></td>
						</tr>

<?php
			mysqli_close($mysql_link);
		}
	}

?>
<?php

	if (!function_exists('fsockopen'))
	{
?>
						<tr class="error">
							<td>Checking for FSOCKOPEN function</td>
							<td><span class="label label-important">FAILED</span></td>
							<td></td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for FSOCKOPEN function</td>
							<td><span class="label label-success">SUCCESSFUL</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!function_exists('mail'))
	{
?>
						<tr class="error">
							<td>Checking for MAIL function</td>
							<td><span class="label label-important">FAILED</span></td>
							<td></td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for MAIL function</td>
							<td><span class="label label-success">SUCCESSFUL</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('curl'))
	{
?>
						<tr class="error">
							<td>Checking for Curl extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>Curl extension is not installed. (<a href="http://php.net/curl">Curl</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for Curl extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('mbstring'))
	{
?>
						<tr class="error">
							<td>Checking for MBSTRING extension (LGSL - Used to show UTF-8 server and player names correctly)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>mbstring extension is not installed. (<a href="http://php.net/mbstring">mbstring</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for MBSTRING extension (LGSL - Used to show UTF-8 server and player names correctly)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('bz2'))
	{
?>
						<tr class="error">
							<td>Checking for BZIP2 extension (LGSL - Used to show Source server settings over a certain size)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>BZIP2 extension is not installed. (<a href="http://php.net/bzip2">BZIP2</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for BZIP2 extension (LGSL - Used to show Source server settings over a certain size)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('zlib'))
	{
?>
						<tr class="error">
							<td>Checking for ZLIB extension (LGSL - Required for America's Army 3)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>ZLIB extension is not installed. (<a href="http://php.net/zlib">ZLIB</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for ZLIB extension (LGSL - Required for America's Army 3)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('gd') && !extension_loaded('gd2'))
	{
?>
						<tr class="error">
							<td>Checking for GD extension (pChart Requirement)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>GD / GD2 extensions are not installed. (<a href="http://php.net/book.image.php">GD</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for GD extension (pChart Requirement)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!function_exists('imagettftext'))
	{
?>
						<tr class="error">
							<td>Checking for FreeType extension (securimage Requirement)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>FreeType extension is not installed. (<a href="http://php.net/manual/en/image.installation.php">FreeType</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for FreeType extension (securimage Requirement)</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	if (!extension_loaded('simplexml'))
	{
?>
						<tr class="error">
							<td>Checking for SimpleXML extension</td>
							<td><span class="label label-important">FAILED</span></td>
							<td>SimpleXML extension is not installed. (<a href="http://php.net/simplexml">SimpleXML</a>).</td>
						</tr>
<?php
		$error = TRUE;
	}
	else
	{
?>
						<tr class="success">
							<td>Checking for SimpleXML extension</td>
							<td><span class="label label-success">INSTALLED</span></td>
							<td></td>
						</tr>
<?php
	}

?>
<?php

	$passphrase = file_get_contents("../.ssh/passphrase");
	if (preg_match('#isEmpty = TRUE;#', $passphrase))
	{
		if (is_writable("../.ssh/passphrase"))
		{
?>
						<tr class="success">
							<td>Checking for PASSPHRASE file is_writable (.ssh/passphrase)</td>
							<td><span class="label label-success">OK</span></td>
							<td></td>
						</tr>
<?php
		}
		else
		{
?>
						<tr class="error">
							<td>Checking for PASSPHRASE file is_writable (.ssh/passphrase)</td>
							<td><span class="label label-important">FAILED</span></td>
							<td></td>
						</tr>
<?php
			$error = TRUE;
		}
	}
	unset($passphrase);

?>
					</tbody>
				</table>
<?php

	if (isset($error))
	{
?>
				<div style="text-align: center;">
					<h3><b>Fatal Error(s) Found.</b></h3><br />
					<button class="btn" onclick="window.location.reload();">Check Again</button>
				</div>
<?php
	}
	else
	{
?>
				<div style="text-align: center;">
					<ul class="pager">
						<li>
							<a href="index.php?step=two">Next Step &rarr;</a>
						</li>
					</ul>
				</div>
<?php
	}

}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



else if ($_GET['step'] == 'two')
{
?>
				<div class="well">
				<h2>Checking for existing databases . . . . .</h2>
<?php

	$mysql_link = @mysqli_connect(DBHOST,DBUSER,DBPASSWORD);
	if (!$mysql_link)
	{
		exit('Could not connect to MySQL: '.mysqli_error($conn));
	}
	else
	{
		$mysql_database_link = mysqli_select_db($nodb, DBNAME);
		if ($mysql_database_link == FALSE)
		{
			exit('Could not connect to MySQL database: '.mysqli_error($conn));
		}
		else
		{
			$tables = mysqli_query($conn, 'SHOW TABLES');
			$rowsTables = mysqli_fetch_array($tables);

			if (!empty($rowsTables))
			{
				while ($rowsTables = mysqli_fetch_array($tables))
				{
					if ($rowsTables[0] == DBPREFIX.'config')
					{
						$currentVersion = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'panelversion' LIMIT 1" ));
					}
				}
			}

			mysqli_close($mysql_link);
			mysqli_close($conn);
		}
	}

	if (isset($currentVersion))
	{
?>
				<div class="alert alert-block">
					<strong>FOUND !</strong> Tables exist in the database.<br />
					You can update your previous version of BrightGamePanel or perform a clean install <u>which will overwrite all data (BGP tables with the same prefix) in the database.</u><br />
					It is recommend you back up your database first.<br />
				</div>
				<h4>Current Version:</h4>&nbsp;<span class="label label-info"><?php echo $currentVersion['value']; ?></span><br /><br />
				<h4>Select Action :</h4><br />
				<form action="index.php" method="get">
					<input type="hidden" name="step" value="three" />
					<input name="version" type="radio" value="update" checked="checked" /><b>&nbsp;Update to the Last Version (<?php echo LASTBGPVERSION; ?>)</b><br /><br /><br />
					<input name="version" type="radio" value="full" /><b>&nbsp;<span class="label label-warning">Perform Clean Install</span>&nbsp;- Version <?php echo LASTBGPVERSION; ?></b><br /><br />
					<button type="submit" class="btn btn-primary">Install MySQL Database</button>
				</form>
				</div>
<?php
	}
	else
	{
?>
				<span class="label label-success">No tables found in the database</span><br /><br />
				<form action="index.php" method="get">
					<input type="hidden" name="step" value="three" />
					<input name="version" type="radio" value="full" checked="checked" /><b> BGP V3</b><br /><br />
					<button type="submit" class="btn btn-primary"> INSTALL </button>
				</form>
				</div>
<?php
	}

?>
				<div style="text-align: center;">
					<ul class="pager">
						<li>
							<a href="index.php?step=one">&larr; Previous Step</a>
						</li>
					</ul>
				</div>
<?php
}



//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+



else if ($_GET['step'] == 'three')
{

	switch (@$_GET['version'])
	{
		case 'full':

			//---------------------------------------------------------+

			$crypt_key = hash('sha512', md5(str_shuffle(time())));

			if (is_writable("../.ssh/passphrase"))
			{
				$handle = fopen('../.ssh/passphrase', 'w');
				fwrite($handle, $crypt_key);
				fclose($handle);
			}

			//---------------------------------------------------------+

			require("./sql/full.php");

			break;

		case 'update':

			$mysql_link = mysqli_connect(DBHOST,DBUSER,DBPASSWORD);
			if (!$mysql_link)
			{
				exit(mysqli_error($conn));
			}
			else
			{
				$mysql_database_link = mysqli_select_db($nodb, DBNAME);
				if ($mysql_database_link == FALSE)
				{
					echo "Could not connect to MySQL database";
				}
				else
				{
					$currentVersion = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'panelversion' LIMIT 1" ));
					mysqli_close($mysql_link);
				}
			}

			//---------------------------------------------------------+

			foreach($bgpVersions as $key => $value)
			{
				if ($value == $currentVersion['value']) // Version reference for the update
				{
					if ($key == end($bgpVersions))
					{
						break; // Already up-to-date
					}
					else
					{
						$i = $key; // Starting point for the update

						for ($i; $i < key($bgpVersions); $i++) // Loop in order to reach the last version
						{
							// Apply the update
							$sqlFile = './sql/';
							$sqlFile .= 'update_'.str_replace('.', '', $bgpVersions[$i]).'_to_'.str_replace('.', '', $bgpVersions[$i + 1]).'.php';

							require($sqlFile);
						}

						break; // Update finished
					}
				}
			}

			//---------------------------------------------------------+

			$mysql_link = mysqli_connect(DBHOST,DBUSER,DBPASSWORD);
			if (!$mysql_link)
			{
				exit(mysqli_error($conn));
			}
			else
			{
				$mysql_database_link = mysqli_select_db($nodb, DBNAME);
				if ($mysql_database_link == FALSE)
				{
					echo "Could not connect to MySQL database";
				}
				else
				{
					$currentVersion = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'panelversion' LIMIT 1" ));
					mysqli_close($mysql_link);
				}
			}

			if ($currentVersion['value'] != LASTBGPVERSION)
			{
				exit( "Update Error." );
			}

			//---------------------------------------------------------+

			break;

		default:
			exit('<h1><b>Error</b></h1>');
	}

	//---------------------------------------------------------+

?>
				<div class="well">
				<div class="alert alert-block">
					<strong>DELETE THE INSTALL FOLDER</strong><br />
					<?php echo getcwd(); ?>

				</div>
<?php
	if (@$_GET['version'] == 'full') // Full install case
	{
?>
				<h2>Install Complete!</h2>
				<legend>Login Information :</legend>
				Admin Username: <b>admin</b><br />
				Admin Password: <b>password</b><br />
				<hr>
				<i class="icon-share-alt"></i>&nbsp;<a href="../admin">@Admin Login</a>
				<hr>
				<div class="alert alert-error">
					<strong>Wait!</strong>
					Remember to change the admin username and password.
				</div>
<?php
	}
	else // Update Case
	{
?>
				<h2>Your system is now up-to-date.</h2>
				<legend>Changelog:</legend>
				<div style="width:auto;height:480px;overflow:scroll;overflow-y:scroll;overflow-x:hidden;">
<?php
?>
				</div>
				<hr>
				<i class="icon-share-alt"></i>&nbsp;<a href="../admin">@Admin Login</a>
<?php
	}
?>
				<hr>
				<h1>Thanks for using BGP V3 :-)</h1>
				</div>
<?php
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
?>
				<hr>
	</body>
</html>
