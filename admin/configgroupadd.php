<?php
$page = 'configgroupadd';
$tab = 5;
$return = 'configgroupadd.php';


require("../configuration.php");
require("./include.php");

$title = T_('Add New Group');

include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="well">
				<form method="post" action="configgroupprocess.php">
					<input type="hidden" name="task" value="configgroupadd" />
					<label><?php echo T_('Group Name'); ?></label>
						<input type="text" name="name" class="span4" value="<?php
if (isset($_SESSION['name']))
{
	echo htmlspecialchars($_SESSION['name'], ENT_QUOTES);
	unset($_SESSION['name']);
}
?>">
					<label><?php echo T_('Group Description'); ?></label>
						<textarea name="notes" class="textarea span10"><?php
if (isset($_SESSION['notes']))
{
	echo htmlspecialchars($_SESSION['notes'], ENT_QUOTES);
	unset($_SESSION['notes']);
}
?></textarea>
					<div style="text-align: center; margin-top: 19px;">
						<button type="submit" class="btn btn-primary"><?php echo T_('Add New Group'); ?></button>
					</div>
					<div style="text-align: center; margin-top: 19px;">
						<ul class="pager">
							<li>
								<a href="configgroup.php"><?php echo T_('Back to Groups'); ?></a>
							</li>
						</ul>
					</div>
				</form>
			</div>
<?php


include("./bootstrap/footer.php");
?>
