<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}
	function query_basic($query)
	{
		$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
		$result = mysqli_query($conn, $query);
		if ($result == FALSE)
		{
			$msg = 'Invalid query : '.mysqli_error($conn)."\n";
			echo $msg;
			
		}
	}
?>
