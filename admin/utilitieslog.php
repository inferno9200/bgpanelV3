<?php
$page = 'utilitieslog';
$tab = 4;
$return = 'utilitieslog.php';


require("../configuration.php");
require("./include.php");


$title = T_('Activity Logs');


//---------------------------------------------------------+
// Num Pages Process:

$numLogs = query_numrows( "SELECT * FROM `".DBPREFIX."log` ORDER BY `logid` LIMIT 750" );

$numPages = ceil($numLogs / 50);

//---------------------------------------------------------+
// Pages Process:

if (isset($_GET['page']))
{
	$page = mysqli_real_escape_string($conn, $_GET['page']);
}
else
{
	$page = 1;
}

// Security
if ($page > 15 || !is_numeric($page))
{
	exit('Page error!');
}

//---------------------------------------------------------+
// Logs:

$logs = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."log` ORDER BY `logid` DESC LIMIT ".(($page - 1) * 50).", 50" );

//---------------------------------------------------------+


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="container">
				<div style="text-align: center;">
					<a href="#" class="btn btn-danger" onclick="deleteLogs();return false;"><i class="icon-warning-sign icon-white"></i>&nbsp;<?php echo T_('Purge'); ?></a>
					<a href="#" class="btn btn-primary" onclick="dlTxtLogs();return false;"><i class="icon-download-alt icon-white"></i>&nbsp;<?php echo T_('TXT'); ?></a>
					<a href="#" class="btn btn-primary" onclick="dlCsvLogs();return false;"><i class="icon-download-alt icon-white"></i>&nbsp;<?php echo T_('CSV'); ?></a>
				</div>
			</div> <!-- End Container -->
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
		echo "utilitieslog.php?page=".$i;
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
				<!-- -->
				function deleteLogs()
				{
					if (confirm("<?php echo T_('WARNING : All logs will be deleted!'); ?>"))
					{
						window.location.href='utilitieslogprocess.php?task=deletelog';
					}
				}
				<!-- -->
				function dlTxtLogs()
				{
					if (confirm("<?php echo T_('Download all logs (TXT) ?'); ?>"))
					{
						window.location.href='utilitieslogprocess.php?task=dumplogtxt';
					}
				}
				<!-- -->
				function dlCsvLogs()
				{
					if (confirm("<?php echo T_("Download all logs (CSV: Comma-Separated Values(;) ) ?"); ?>"))
					{
						window.location.href='utilitieslogprocess.php?task=dumplogcsv';
					}
				}
				</script>
<?php
}
unset($logs, $numLogs, $numPages, $page);

?>
			</div>
<?php


include("./bootstrap/footer.php");
?>