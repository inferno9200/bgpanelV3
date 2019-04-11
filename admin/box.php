<?php
$page = 'box';
$tab = 3;
$return = 'box.php';


require("../configuration.php");
require("./include.php");


$title = T_('Boxes');


$cron = query_fetch_assoc( "SELECT `value` FROM `".DBPREFIX."config` WHERE `setting` = 'lastcronrun' LIMIT 1" );
$boxes = mysqli_query($conn, "SELECT `boxid`, `name`, `ip`, `sshport`, `cache` FROM `".DBPREFIX."box` ORDER BY `boxid`" );

include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="container">
				<div style="text-align: center; margin-bottom: 20px;">
					<a href="boxadd.php" class="btn btn-primary"><i class="icon-plus icon-white"></i>&nbsp;<?php echo T_('Add New Box'); ?></a>
				</div>
			</div> <!-- End Container -->
			<div class="well">
				<table id="boxes" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('Name'); ?></th>
							<th><?php echo T_('IP Address'); ?></th>
							<th><?php echo T_('Servers'); ?></th>
							<th><?php echo T_('Network Status'); ?></th>
							<th colspan="2"><?php echo T_('Bandwidth Usage'); ?> (<a href="#" id="bw" rel="tooltip" title="<?php echo T_('Shows Bandwidth Statistics. RX: receive, incoming data. TX: transmitting, outgoing data.'); ?>">?</a>)</th>
							<th><?php echo T_('CPU'); ?> (<a href="#" id="cpu" rel="tooltip" title="<?php echo T_('Shows the percentage of CPU in use by the box (user mode).'); ?>">?</a>)</th>
							<th><?php echo T_('RAM'); ?> (<a href="#" id="ram" rel="tooltip" title="<?php echo T_('Shows the percentage of RAM in use by the box.'); ?>">?</a>)</th>
							<th><?php echo T_('Load Average'); ?> (<a href="#" id="loadavg" rel="tooltip" title="<?php echo T_('Represents the average system load during the last 15 minutes.'); ?>">?</a>) [<a href="http://en.wikipedia.org/wiki/Load_%28computing%29" target="_blank">Wiki</a>]</th>
							<th><?php echo T_('HDD'); ?> (<a href="#" id="hdd" rel="tooltip" title="<?php echo T_('Shows the percentage of HDD usage.'); ?>">?</a>)</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
<?php

if (mysqli_num_rows($boxes) == 0)
{
?>
						<tr>
							<td colspan="11"><div style="text-align: center;"><span class="label label-warning"><?php echo T_('No Boxes Found'); ?></span><br /><?php echo T_('No boxes found.'); ?> <a href="boxadd.php"><?php echo T_('Click here'); ?></a> <?php echo T_('to add a new box.'); ?></div></td>
						</tr>
<?php
}

while ($rowsBoxes = mysqli_fetch_assoc($boxes))
{
	$cache = unserialize(gzuncompress($rowsBoxes['cache']));
?>
						<tr>
							<td><?php echo htmlspecialchars($rowsBoxes['name'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsBoxes['ip'], ENT_QUOTES); ?></td>
							<td><?php echo query_numrows( "SELECT `serverid` FROM `".DBPREFIX."server` WHERE `boxid` = '".$rowsBoxes['boxid']."'" ); ?></td>
							<td><?php echo formatStatus(getStatus($rowsBoxes['ip'], $rowsBoxes['sshport'])); ?></td>
							<td> RX:&nbsp;<?php echo bytesToSize($cache["{$rowsBoxes['boxid']}"]['bandwidth']['rx_usage']); ?>/s </td>
							<td> TX:&nbsp;<?php echo bytesToSize($cache["{$rowsBoxes['boxid']}"]['bandwidth']['tx_usage']); ?>/s </td>
							<td><span class="badge badge-<?php

							if ($cache["{$rowsBoxes['boxid']}"]['cpu']['usage'] < 65) {
								echo 'info';
							} else if ($cache["{$rowsBoxes['boxid']}"]['cpu']['usage'] < 85) {
								echo 'warning';
							} else { echo 'important'; }

							?>"><?php echo $cache["{$rowsBoxes['boxid']}"]['cpu']['usage']; ?>&nbsp;%</span></td>
							<td><span class="badge badge-<?php

							if ($cache["{$rowsBoxes['boxid']}"]['ram']['usage'] < 65) {
								echo 'info';
							} else if ($cache["{$rowsBoxes['boxid']}"]['ram']['usage'] < 85) {
								echo 'warning';
							} else { echo 'important'; }

							?>"><?php echo $cache["{$rowsBoxes['boxid']}"]['ram']['usage']; ?>&nbsp;%</span></td>
							<td><span class="badge badge-<?php

							if (substr($cache["{$rowsBoxes['boxid']}"]['loadavg']['loadavg'], 0, -3) < $cache["{$rowsBoxes['boxid']}"]['cpu']['cores']) {
								echo 'info';
							} else if (substr($cache["{$rowsBoxes['boxid']}"]['loadavg']['loadavg'], 0, -3) == $cache["{$rowsBoxes['boxid']}"]['cpu']['cores']) {
								echo 'warning';
							} else { echo 'important'; }

							?>"><?php echo $cache["{$rowsBoxes['boxid']}"]['loadavg']['loadavg']; ?></span></td>
							<td><span class="badge badge-<?php

							if ($cache["{$rowsBoxes['boxid']}"]['hdd']['usage'] < 65) {
								echo 'info';
							} else if ($cache["{$rowsBoxes['boxid']}"]['hdd']['usage'] < 85) {
								echo 'warning';
							} else { echo 'important'; }

							?>"><?php echo $cache["{$rowsBoxes['boxid']}"]['hdd']['usage']; ?>&nbsp;%</span></td>
							<td><div style="text-align: center;"><a class="btn btn-small" href="boxprofile.php?id=<?php echo $rowsBoxes['boxid']; ?>"><i class="icon-edit <?php echo formatIcon(); ?>"></i></a></div></td>
							<td><div style="text-align: center;"><a class="btn btn-info btn-small" href="boxsummary.php?id=<?php echo $rowsBoxes['boxid']; ?>"><i class="icon-search icon-white"></i></a></div></td>
						</tr>
<?php
	unset($cache);
}

?>					</tbody>
				</table>
<?php

if (mysqli_num_rows($boxes) != 0)
{
?>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#boxes").tablesorter({
						headers: {
							4: {
								sorter: false
							},
							5: {
								sorter: false
							},
							6: {
								sorter: false
							},
							7: {
								sorter: false
							},
							8: {
								sorter: false
							},
							9: {
								sorter: false
							},
							10: {
								sorter: false
							}
						},
						sortList: [[0,0]]
					});
					$('#bw').tooltip();
					$('#cpu').tooltip();
					$('#ram').tooltip();
					$('#loadavg').tooltip();
					$('#hdd').tooltip();
				});
				</script>
<?php
}
unset($boxes);

?>
			</div>

			<div class="well"><?php echo T_('Last Update'); ?> : <span class="label"><?php echo formatDate($cron['value']); ?></span><?php
if ($cron['value'] == 'Never')
{
	echo "\t\t\t<br />".T_('Setup the cron job to enable box monitoring!');
}
?></div>

<?php


include("./bootstrap/footer.php");
?>
