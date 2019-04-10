<?php
$page = 'scriptcatmanage';
$tab = 5;
$return = 'scriptcatmanage.php';


require("../configuration.php");
require("./include.php");


$title = T_('Manage Script Categories');


$categories = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."scriptCat` ORDER BY `id`" );


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="container">
				<div style="text-align: center; margin-bottom: 20px;">
					<a href="scriptcatadd.php" class="btn btn-primary"><i class="icon-plus icon-white"></i>&nbsp;<?php echo T_('Add New Category'); ?></a>
				</div>
			</div> <!-- End Container -->
			<div class="well">
				<table id="categories" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('Name'); ?></th>
							<th><?php echo T_('Description'); ?></th>
							<th><?php echo T_('Scripts'); ?></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
<?php

if (mysqli_num_rows($categories) == 0)
{
?>
						<tr>
							<td colspan="5"><div style="text-align: center;"><span class="label label-warning"><?php echo T_('No Categories Found'); ?></span><br /><?php echo T_('No categories found.'); ?> <a href="scriptcatadd.php"><?php echo T_('Click here'); ?></a> <?php echo T_('to add a new category.'); ?></div></td>
						</tr>
<?php
}

while ($rowsCategories = mysqli_fetch_assoc($categories))
{
?>
						<tr>
							<td><?php echo htmlspecialchars($rowsCategories['name'], ENT_QUOTES); ?></td>
							<td><?php echo htmlspecialchars($rowsCategories['description'], ENT_QUOTES); ?></td>
							<td><?php echo query_numrows( "SELECT `scriptid` FROM `".DBPREFIX."script` WHERE `catid` = '".$rowsCategories['id']."'" ); ?></td>
							<td><div style="text-align: center;"><a class="btn btn-small" href="scriptcatedit.php?id=<?php echo $rowsCategories['id']; ?>"><i class="icon-edit <?php echo formatIcon(); ?>"></i></a></div></td>
							<td><div style="text-align: center;"><a class="btn btn-danger btn-small" href="#" onclick="doDelete('<?php echo $rowsCategories['id']; ?>', '<?php echo htmlspecialchars(addslashes($rowsCategories['name']), ENT_QUOTES); ?>')"><i class="icon-remove icon-white"></i></a></div></td>
						</tr>
<?php
}

?>					</tbody>
				</table>
<?php

if (mysqli_num_rows($categories) != 0)
{
?>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#categories").tablesorter({
						headers: {
							3: {
								sorter: false
							},
							4: {
								sorter: false
							},
							sortList: [[0,0]]
						}
					});
				});
				<!-- -->
				function doDelete(id, cat)
				{
					if (confirm("<?php echo T_('Are you sure you want to delete category:'); ?> "+cat+"?"))
					{
						window.location='scriptprocess.php?task=scriptcatdelete&id='+id;
					}
				}
				</script>
<?php
}
unset($categories);

?>
				<div style="text-align: center; margin-top: 19px;">
					<ul class="pager">
						<li>
							<a href="script.php"><?php echo T_('Go to Scripts'); ?></a>
						</li>
					</ul>
				</div>
			</div>
<?php


include("./bootstrap/footer.php");
?>
