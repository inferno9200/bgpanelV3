<?php
$page = 'configgroup';
$tab = 5;
$return = 'configgroup.php';


require("../configuration.php");
require("./include.php");

$title = T_('Manage Groups');

$groups = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."group` ORDER BY `groupid`" );


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="container">
				<div style="text-align: center; margin-bottom: 20px;">
					<a href="configgroupadd.php" class="btn btn-primary"><i class="icon-plus icon-white"></i>&nbsp;<?php echo T_('Add New Group'); ?></a>
				</div>
			</div> <!-- End Container -->
			<div class="well">
				<table id="groups" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('ID'); ?></th>
							<th><?php echo T_('Name'); ?></th>
							<th><?php echo T_('Description'); ?></th>
							<th><?php echo T_('Members'); ?></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
<?php

if (mysqli_num_rows($groups) == 0)
{
?>
						<tr>
							<td colspan="6"><div style="text-align: center;"><span class="label label-warning"><?php echo T_('No Groups Found'); ?></span><br /> <?php echo T_('No groups found.'); ?><a href="configgroupadd.php"> <?php echo T_('Click here'); ?></a>&nbsp;<?php echo T_('to add a new group.'); ?></div></td>
						</tr>
<?php
}

while ($rowsGroups = mysqli_fetch_assoc($groups))
{
	if (getGroupClients($rowsGroups['groupid']) == FALSE)
	{
		$counter = 0;
	}
	else
	{
		$counter = count(getGroupClients($rowsGroups['groupid']));
	}
?>
						<tr>
							<td><?php echo $rowsGroups['groupid']; ?></td>
							<td><?php echo htmlspecialchars($rowsGroups['name'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsGroups['description'], ENT_QUOTES); ?></td>
							<td><?php echo $counter; ?></td>
							<td><div style="text-align: center;"><a class="btn btn-small" href="configgroupedit.php?id=<?php echo $rowsGroups['groupid']; ?>"><i class="icon-edit <?php echo formatIcon(); ?>"></i></a></div></td>
							<td><div style="text-align: center;"><a class="btn btn-danger btn-small" href="#" onclick="doDelete('<?php echo $rowsGroups['groupid']; ?>', '<?php echo htmlspecialchars(addslashes($rowsGroups['name']), ENT_QUOTES); ?>')"><i class="icon-remove icon-white"></i></a></div></td>
						</tr>
<?php
	unset($counter);
}

?>					</tbody>
				</table>
<?php

if (mysqli_num_rows($groups) != 0)
{
?>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#groups").tablesorter({
						headers: {
							4: {
								sorter: false
							},
							5: {
								sorter: false
							},
							sortList: [[1,0]]
						}
					});
				});
				<!-- -->
				function doDelete(id, group)
				{
					if (confirm("<?php echo T_('Are you sure you want to delete group:'); ?> "+group+"?"))
					{
						window.location='configgroupprocess.php?task=configgroupdelete&id='+id;
					}
				}
				</script>
<?php
}
unset($groups);

?>
			</div>
<?php


include("./bootstrap/footer.php");
?>