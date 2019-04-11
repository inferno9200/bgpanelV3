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
	case 'optimize':
		$result = mysqli_query($conn, 'SHOW TABLES');
		while($table = mysqli_fetch_row($result))
		{
			if (preg_match("#^".DBPREFIX."#", $table[0]))
			{
				mysqli_query($conn, 'OPTIMIZE TABLE '.$table[0]);
			}
		}
		unset($result);
		###
		$_SESSION['msg1'] = T_('Optimizing tables... Done!');
		$_SESSION['msg2'] = T_('Tables are up to date.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: utilitiesoptimize.php" );
		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>