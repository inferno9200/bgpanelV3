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
	case 'deletelog':
		query_basic( "TRUNCATE `".DBPREFIX."log`" );
		$_SESSION['msg1'] = T_('Activity Logs Deleted Successfully!');
		$_SESSION['msg2'] = T_('All activity logs have been removed.');
		$_SESSION['msg-type'] = 'success';
		header( "Location: utilitieslog.php" );
		die();
		break;

	case 'dumplogtxt':
		$output = '';
		$date = formatDate(date('Y-m-d H:i:s'));
		$numLogs = query_numrows( "SELECT * FROM `".DBPREFIX."log` ORDER BY `logid`" );

//---------------------------------------------------------+
$output .= "
//==================================================================================
//
//	BRIGHT GAME PANEL ACTIVITY LOGS DUMP
//
//==================================================================================
//
//	FILENAME: bgp-activity-logs-".date('Y-m-d')."
//	DATE: {$date}
//	ADMIN USERNAME: {$_SESSION['adminusername']}
//	ADMIN FIRSTNAME: {$_SESSION['adminfirstname']}
//	ADMIN LASTNAME: {$_SESSION['adminlastname']}
//
//	NUMBER OF LOGS: {$numLogs}
//	ORDERED BY: LOGID
//
//==================================================================================
//
//	NOTES:
//
//		Timestamp Format: date(Y-m-d H:i:s)
//
//==================================================================================
";
//---------------------------------------------------------+
$output .= "\n".
	str_pad("LOGID:", 8).
	str_pad("Message:", 100).
	str_pad("Name:", 24).
	str_pad("IP:", 20).
	str_pad("Timestamp:", 19)."\n";
//---------------------------------------------------------+

		$logs = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."log` ORDER BY `logid` DESC" );

		while ($rowsLogs = mysqli_fetch_assoc($logs))
		{
//---------------------------------------------------------+
$output .=
	str_pad($rowsLogs['logid'], 8).
	str_pad($rowsLogs['message'], 100).
	str_pad($rowsLogs['name'], 24).
	str_pad($rowsLogs['ip'], 20).
	str_pad($rowsLogs['timestamp'], 19)."\n";
//---------------------------------------------------------+
		}

//---------------------------------------------------------+
$output .= "
//==================================================================================
//	END
//==================================================================================
";
//---------------------------------------------------------+

		header('Content-type: text/plain');
		header('Content-Disposition: attachment; filename="bgp-activity-logs-'.date('Y-m-d').'.txt"');

		echo $output;

		die();
		break;

	case 'dumplogcsv':

		/**
		 * CSV Export
		 * @link: http://www.comscripts.com/sources/php.export-csv.102.html
		 */

		$resQuery = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."log` ORDER BY `logid` DESC" );

		header("Content-Type: application/csv-tab-delimited-table");
		header('Content-Disposition: attachment; filename="bgp-activity-logs-'.date('Y-m-d').'.csv"');

		if (mysqli_num_rows($resQuery) != 0)
		{
			// Columns
			$fields = mysqli_num_fields($resQuery);
			$i = 0;
			while ($i < $fields) {
				echo mysqli_field_name($resQuery, $i).";";
				$i++;
			}
			echo "\n";

			// Table data
			while ($arrSelect = mysqli_fetch_array($resQuery, MYSQLI_ASSOC)) {
				foreach($arrSelect as $elem) {
					echo "$elem;";
				}
				echo "\n";
			}
		}

		die();
		break;

	default:
		exit('<h1><b>Error</b></h1>');
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>
