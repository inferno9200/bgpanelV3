<?php
$page = 'client';
$tab = 1;
$return = 'client.php';
require("../configuration.php");
require("./include.php");
$title = T_('Clients');


$clients = mysqli_query($conn, "SELECT `clientid`, `firstname`, `lastname`, `email`, `lastlogin`, `status` FROM `".DBPREFIX."client` ORDER BY `clientid`" );

include("./bootstrap/header.php");
include("./bootstrap/notifications.php");


?>
			<div class="container">
				<div style="text-align: center; margin-bottom: 20px;">
					<a href="clientadd.php" class="btn btn-primary"><i class="icon-plus icon-white"></i>&nbsp;<?php echo T_('Add New Client'); ?></a>
				</div>
			</div>
			<div class="well">
				<table id="clients" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('ID'); ?></th>
							<th><?php echo T_('First Name'); ?></th>
							<th><?php echo T_('Last Name'); ?></th>
							<th><?php echo T_('Email'); ?></th>
							<th><?php echo T_('Last Login'); ?></th>
							<th><?php echo T_('Status'); ?></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
<?php

if (mysqli_num_rows($clients) == 0)
{
?>
						<tr>
							<td colspan="8"><div style="text-align: center;"><span class="label label-warning"><?php echo T_('No Clients Found'); ?></span><br /><?php echo T_('No clients found.'); ?> <a href="clientadd.php"><?php echo T_('Click here'); ?></a>&nbsp;<?php echo T_('to add a new client.'); ?></div></td>
						</tr>
<?php
}

while ($rowsClients = mysqli_fetch_assoc($clients))
{
?>
						<tr>
							<td><?php echo $rowsClients['clientid']; ?></td>
							<td><?php echo htmlspecialchars($rowsClients['firstname'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsClients['lastname'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsClients['email'], ENT_QUOTES); ?></td>
							<td><?php echo formatDate($rowsClients['lastlogin']); ?></td>
							<td><?php echo formatStatus($rowsClients['status']); ?></td>
							<td><div style="text-align: center;"><a class="btn btn-small" href="clientprofile.php?id=<?php echo $rowsClients['clientid']; ?>"><i class="icon-edit <?php echo formatIcon(); ?>"></i></a></div></td>
							<td><div style="text-align: center;"><a class="btn btn-info btn-small" href="clientsummary.php?id=<?php echo $rowsClients['clientid']; ?>"><i class="icon-search icon-white"></i></a></div></td>
						</tr>
<?php
}

?>					</tbody>
				</table>
<?php

if (mysqli_num_rows($clients) != 0)
{
?>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#clients").tablesorter({
						headers: {
							6: {
								sorter: false
							},
							7: {
								sorter: false
							}
						},
						sortList: [[3,0]]
					});
				});
				</script>
<?php
}
unset($clients);

?>
			</div>
<?php


include("./bootstrap/footer.php");
?>