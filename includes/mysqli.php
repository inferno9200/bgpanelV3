<?php
//Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}
$connection = mysql_pconnect(DBHOST, DBUSER, DBPASSWORD);	// Connection to database
if (!$connection)	// Return error if connection is broken
{
	exit("<html><head></head><body><b>Critical Error!!!</b><br />MySQL Error!</body></html>");
}
$db_connection = mysql_select_db(DBNAME);	// Select our database
if (!$db_connection)	// Return error	if error happened with database
{
	exit("<html><head></head><body><b>Critical Error!!!</b><br />MySQL Error!</body></html>");
}
/**
 * query_basic -- mysql_query ALIAS
 *
 * Used for INSERT INTO - UPDATE - DELETE requests.
 *
 * No return.
 */
function query_basic($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$msg = 'Invalid query : '.mysql_error()."\n";
		echo $msg;
	}
}
/**
 * query_numrows -- mysql_query + mysql_num_rows
 *
 * Retrieves the number of rows from a result set and return it.
 */
function query_numrows($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$msg = 'Invalid query : '.mysql_error()."\n";
		echo $msg;
	}
	return (mysql_num_rows($result));
}
/**
 * query_fetch_assoc -- mysql_query + mysql_fetch_assoc
 *
 * Returns an associative array that corresponds to the fetched row.
 */
function query_fetch_assoc($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$msg = 'Invalid query : '.mysql_error()."\n";
		echo $msg;
	}
	return (mysql_fetch_assoc($result));
}
?>
