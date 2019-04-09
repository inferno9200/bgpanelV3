<?php
//Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}
$connection = mysql_pconnect(DBHOST, DBUSER, DBPASSWORD);	// Connection to database
if (!$connection)	// Return error if connection is broken
{
	exit("<html><head></head><body><h1>Database maintenance</h1><p>Please check back later</p></body></html>");
}
$db_connection = mysql_select_db(DBNAME);	// Select our database
if (!$db_connection)	// Return error	if error happened with database
{
	exit("<html><head></head><body><h1>Database maintenance</h1><p>Please check back later</p></body></html>");
}
/**
 * query_basic -- mysql_query ALIAS
 *
 * Used for INSERT INTO - UPDATE - DELETE requests.
 *
 * Return true on success
 */
function query_basic($query) {
	$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
	$result = mysqli_query($conn, $query);
	if ($result == FALSE) {
		$msg = 'Invalid query : '.mysqli_error($conn)."\n";
		echo $msg;
		return FALSE;
	}
	else
		return TRUE;
}
/**
 * query_numrows -- mysql_query + mysql_num_rows
 *
 * Retrieves the number of rows from a result set and return it.
 */
function query_numrows($query) {
	$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
	$result = mysqli_query($conn, $query);
	if ($result == FALSE)
	{
		$msg = 'Invalid query : '.mysqli_error($conn)."\n";
		echo $msg;
	}
	return (mysqli_num_rows($result));
}
/**
 * query_fetch_assoc -- mysql_query + mysql_fetch_assoc
 *
 * Returns an associative array that corresponds to the fetched row.
 */
function query_fetch_assoc($query) {
	$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
	$result = mysqli_query($conn, $query);
	if ($result == FALSE)
	{
		$msg = 'Invalid query : '.mysqli_error($conn)."\n";
		echo $msg;
	}
	return mysqli_fetch_assoc($result);
}
?>
