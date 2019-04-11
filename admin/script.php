<?php
$page = 'script';
$tab = 5;
$return = 'script.php';


require("../configuration.php");
require("./include.php");


$title = T_('Manage Scripts');


$scripts = mysqli_query($conn, "SELECT `scriptid`, `groupid`, `boxid`, `catid`, `name`, `status`, `panelstatus`, `type` FROM `".DBPREFIX."script` ORDER BY `scriptid`" );


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="container">
				<div style="text-align: center; margin-bottom: 20px;">
					<a href="scriptadd.php" class="btn btn-primary"><i class="icon-plus icon-white"></i>&nbsp;<?php echo T_('Add New Script'); ?></a>
				</div>
			</div> <!-- End Container -->
			<div class="well">
				<table id="scripts" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('Name'); ?></th>
							<th><?php echo T_('Category'); ?></th>
							<th><?php echo T_('Owner Group'); ?></th>
							<th><?php echo T_('Exec Mode'); ?></th>
							<th><?php echo T_('Panel Status'); ?></th>
							<th><?php echo T_('Box Name'); ?></th>
							<th><?php echo T_('Status'); ?></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
<?php

if (mysqli_num_rows($scripts) == 0)
{
?>
						<tr>
							<td colspan="10"><div style="text-align: center;"><span class="label label-warning"><?php echo T_('No Scripts Found'); ?></span><br /><?php echo T_('No scripts found.'); ?> <a href="scriptadd.php"><?php echo T_('Click here'); ?></a> <?php echo T_('to add a new script.'); ?></div></td>
						</tr>
<?php
}

while ($rowsScripts = mysqli_fetch_assoc($scripts))
{
	$cat = query_fetch_assoc( "SELECT `name` FROM `".DBPREFIX."scriptCat` WHERE `id` = '".$rowsScripts['catid']."' LIMIT 1" );
	$group = query_fetch_assoc( "SELECT `name` FROM `".DBPREFIX."group` WHERE `groupid` = '".$rowsScripts['groupid']."' LIMIT 1" );
	$box = query_fetch_assoc( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$rowsScripts['boxid']."' LIMIT 1" );
	###
	if (!empty($rowsScripts['panelstatus']))
	{
		$pstatus = formatStatus($rowsScripts['panelstatus']);
	}
	else
	{
		$pstatus = "<span class=\"label\"><em>".T_('None')."</em></span>";
	}

?>
						<tr>
							<td><?php echo htmlspecialchars($rowsScripts['name'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?></td>
							<td><?php if (!empty($group['name'])) { echo htmlspecialchars($group['name'], ENT_QUOTES); } else { echo "<span class=\"label\"><em>".T_('None')."</em></span>"; } ?></td>
							<td><?php if ($rowsScripts['type'] == '0') { echo T_('Non-Interactive'); } else { echo T_('Interactive'); }; ?></td>
							<td><?php echo $pstatus; ?></td>
							<td><?php echo htmlspecialchars($box['name'], ENT_QUOTES); ?></td>
							<td><?php echo formatStatus($rowsScripts['status']); ?></td>
							<td><div style="text-align: center;"><a class="btn btn-small" href="scriptprofile.php?id=<?php echo $rowsScripts['scriptid']; ?>"><i class="icon-edit <?php echo formatIcon(); ?>"></i></a></div></td>
							<td><div style="text-align: center;"><a class="btn btn-info btn-small" href="scriptsummary.php?id=<?php echo $rowsScripts['scriptid']; ?>"><i class="icon-search icon-white"></i></a></div></td>
						</tr>
<?php

	unset($cat, $group, $box, $pstatus);
}

?>					</tbody>
				</table>
<?php

if (mysqli_num_rows($scripts) != 0)
{
?>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#scripts").tablesorter({
						headers: {
							7: {
								sorter: false
							},
							8: {
								sorter: false
							}
						},
						sortList: [[1,0]]
					});
				});
				</script>
<?php
}
unset($scripts);

?>
				<div style="text-align: center; margin-top: 19px;">
					<ul class="pager">
						<li>
							<a href="scriptcatmanage.php"><?php echo T_('Go to Categories'); ?></a>
						</li>
					</ul>
				</div>
			</div>
<?php


include("./bootstrap/footer.php");
?>