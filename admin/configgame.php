<?php
$page = 'configgame';
$tab = 5;
$return = 'configgame.php';


require("../configuration.php");
require("./include.php");

$title = T_('Manage Games');

$games = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."game` ORDER BY `game`" );


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="container">
				<div style="text-align: center; margin-bottom: 20px;">
					<a href="configgameadd.php" class="btn btn-primary"><i class="icon-plus icon-white"></i>&nbsp;<?php echo T_('Add New Game'); ?></a>
				</div>
			</div> <!-- End Container -->
			<div class="well">
				<table id="games" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('Game'); ?></th>
							<th><?php echo T_('Query Type'); ?></th>
							<th><?php echo T_('Cache Directory'); ?></th>
							<th><?php echo T_('Status'); ?></th>
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
							<td><?php echo htmlspecialchars($rowsGames['querytype'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsGames['cachedir'], ENT_QUOTES); ?></td>
							<td><?php echo formatStatus($rowsGames['status']); ?></td>
							<td><div style="text-align: center;"><a class="btn btn-small" href="configgameedit.php?id=<?php echo $rowsGames['gameid']; ?>"><i class="icon-edit <?php echo formatIcon(); ?>"></i></a></div></td>
							<td><div style="text-align: center;"><a class="btn btn-danger btn-small" href="#" onclick="doDelete('<?php echo $rowsGames['gameid']; ?>', '<?php echo htmlspecialchars(addslashes($rowsGames['game']), ENT_QUOTES); ?>')"><i class="icon-remove icon-white"></i></a></div></td>
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
					$("#games").tablesorter({
						headers: {
							4: {
								sorter: false
							},
							5: {
								sorter: false
							}
						},
						sortList: [[0,0]]
					});
				});
				<!-- -->
				function doDelete(id, game)
				{
					if (confirm("<?php echo T_('Are you sure you want to delete game:'); ?> "+game+"?"))
					{
						window.location='configgameprocess.php?task=configgamedelete&id='+id;
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
