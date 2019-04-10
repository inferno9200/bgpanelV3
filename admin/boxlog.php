<?php
$page = 'boxlog';
$tab = 3;
$isSummary = TRUE;
###
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
	$boxid = $_GET['id'];
}
else
{
	exit('Error: BoxID error.');
}
###
$return = 'boxlog.php?id='.urlencode($boxid);


require("../configuration.php");
require("./include.php");


$title = T_('Box Activity Logs');


if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
{
	exit('Error: BoxID is invalid.');
}


$rows = query_fetch_assoc( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );

//---------------------------------------------------------+
//Num Pages Process:

$numLogs = query_numrows( "SELECT * FROM `".DBPREFIX."log` WHERE `boxid` = '".$boxid."' ORDER BY `logid` LIMIT 750" );

$numPages = ceil($numLogs / 50);

//---------------------------------------------------------+
//Pages Process:

if (isset($_GET['page']))
{
	$page = mysqli_real_escape_string($conn, $_GET['page']);
}
else
{
	$page = 1;
}

//Security
if ($page > 15 || !is_numeric($page))
{
	exit('Page error!');
}

//---------------------------------------------------------+
//Logs:

$logs = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."log` WHERE `boxid` = '".$boxid."' ORDER BY `logid` DESC LIMIT ".(($page - 1) * 50).", 50" );

//---------------------------------------------------------+


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<ul class="nav nav-tabs">
				<li><a href="boxsummary.php?id=<?php echo $boxid; ?>"><?php echo T_('Summary'); ?></a></li>
				<li><a href="boxprofile.php?id=<?php echo $boxid; ?>"><?php echo T_('Profile'); ?></a></li>
				<li><a href="boxip.php?id=<?php echo $boxid; ?>"><?php echo T_('IP Addresses'); ?></a></li>
				<li><a href="boxserver.php?id=<?php echo $boxid; ?>"><?php echo T_('Servers'); ?></a></li>
				<li><a href="boxchart.php?id=<?php echo $boxid; ?>"><?php echo T_('Charts'); ?></a></li>
				<li><a href="boxgamefile.php?id=<?php echo $boxid; ?>"><?php echo T_('Game File Repositories'); ?></a></li>
				<li class="active"><a href="boxlog.php?id=<?php echo $boxid; ?>"><?php echo T_('Activity Logs'); ?></a></li>
			</ul>
			<div class="pagination" style="text-align: center;">
				<ul>
<?php

for ($i = 1; $i < $numPages + 1; $i++)
{
?>
					<li <?php
	if ($i == $page) {
		echo "class=\"active\"";
	} ?>>
						<a href="<?php
	if ($i == $page) {
		echo "#";
	} else {
		echo "boxlog.php?id={$boxid}&page=".$i;
	}?>"><?php echo $i; ?></a>
					</li>
<?php
}

?>
				</ul>
			</div>
			<div class="well">
				<div style="text-align: center; margin-bottom: 5px;">
					<span class="label label-info"><?php echo T_('Activity Logs'); ?></span>
				</div>
				<table id="logs" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('ID'); ?></th>
							<th><?php echo T_('Message'); ?></th>
							<th><?php echo T_('Name'); ?></th>
							<th><?php echo T_('IP'); ?></th>
							<th><?php echo T_('Timestamp'); ?></th>
						</tr>
					</thead>
					<tbody>
<?php

if (mysqli_num_rows($logs) == 0)
{
?>
						<tr>
							<td colspan="5"><div style="text-align: center;"><span class="label label-warning"><?php echo T_('No Logs Found'); ?></span></div></td>
						</tr>
<?php
}

$n = 0;
while ($rowsLogs = mysqli_fetch_assoc($logs))
{
?>
						<tr>
							<td><?php echo $rowsLogs['logid']; ?></td>
							<td><?php echo htmlspecialchars($rowsLogs['message'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsLogs['name'], ENT_QUOTES); ?></td>
							<td><?php echo $rowsLogs['ip']; ?></td>
							<td><?php echo formatDate($rowsLogs['timestamp']); ?></td>
						</tr>
<?php
	$n++;
}
unset($n);

?>
					</tbody>
				</table>
<?php

if (mysqli_num_rows($logs) != 0)
{
?>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#logs").tablesorter({
						sortList: [[0,1]]
					});
				});
				</script>
<?php
}
unset($logs, $numLogs, $numPages, $page);

?>
			</div>
<?php


include("./bootstrap/footer.php");
?>
