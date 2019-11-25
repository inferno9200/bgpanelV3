<?php
$page = 'boxgamefile';
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
$return = 'boxgamefile.php?id='.urlencode($boxid);

require("../configuration.php");
require("./include.php");

$title = T_('Box Game File Repositories');

if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
{
	exit('Error: BoxID is invalid.');
}

$rows = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );
$games = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."game` ORDER BY `game`" );

include("./bootstrap/header.php");
include("./bootstrap/notifications.php");


?>
			<ul class="nav nav-tabs">
				<li><a href="boxsummary.php?id=<?php echo $boxid; ?>">Summary</a></li>
				<li><a href="boxprofile.php?id=<?php echo $boxid; ?>">Profile</a></li>
				<li><a href="boxip.php?id=<?php echo $boxid; ?>">IP Addresses</a></li>
				<li><a href="boxserver.php?id=<?php echo $boxid; ?>">Servers</a></li>
				<li><a href="boxchart.php?id=<?php echo $boxid; ?>">Charts</a></li>
				<li class="active"><a href="boxgamefile.php?id=<?php echo $boxid; ?>">Game File Repositories</a></li>
				<li><a href="boxlog.php?id=<?php echo $boxid; ?>">Activity Logs</a></li>
			</ul>
			<div class="well">
				<table id="gamefiles" class="zebra-striped">
					<thead>
						<tr>
							<th>Game</th>
							<th>Directory</th>
							<th>Disk Usage</th>
							<th>Last Modification</th>
							<th>Status</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
<?php

while ($rowsGames = mysqli_fetch_assoc($games))
{
?>
						<tr>
							<td><?php echo htmlspecialchars($rowsGames['game'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsGames['cachedir'], ENT_QUOTES); ?></td>
							<td>None</td>
							<td>Never</td>
							<td><span class="label label-success">Ready</span></td>
							<td>
								<!-- Actions -->
								<div style="text-align: center;">
									<a class="btn btn-small" href="#" onclick="">
										<i class="icon-repeat <?php echo formatIcon(); ?>"></i>
									</a>
								</div>
							</td>
							<td>
								<!-- Drop Action -->
								<div style="text-align: center;">
									<a class="btn btn-small" href="#" onclick="">
										<i class="icon-trash <?php echo formatIcon(); ?>"></i>
									</a>
								</div>
							</td>
						</tr>
<?php
}
?>					</tbody>
				</table>
<?php

if (mysqli_num_rows($games) != 0)
{
?>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#gamefiles").tablesorter({
						headers: {
							5: {
								sorter: false
							},
							6: {
								sorter: false
							}
						},
						sortList: [[0,0]]
					});
				});
				function doRepoAction(boxid, gameid, task, action, game)
				{
					if (confirm('<?php echo T_('Are you sure you want to'); ?> '+action+' '+game+' ?'))
					{
						window.location='boxprocess.php?boxid='+boxid+'&gameid='+gameid+'&task='+task;
					}
				}
				</script>
<?php
}
unset($games);
?>
			</div>
<?php


include("./bootstrap/footer.php");
?>