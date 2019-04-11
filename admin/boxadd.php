<?php
$page = 'boxadd';
$tab = 3;
$return = 'boxadd.php';


require("../configuration.php");
require("./include.php");


$title = T_('Add New Box');


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="well">
				<form method="post" action="boxprocess.php">
					<input type="hidden" name="task" value="boxadd" />
					<label><?php echo T_('Server Name'); ?></label>
						<input type="text" name="name" class="span4" value="<?php
if (isset($_SESSION['name']))
{
	echo htmlspecialchars($_SESSION['name'], ENT_QUOTES);
	unset($_SESSION['name']);
}
?>">
					<label><?php echo T_('IP Address'); ?></label>
						<input type="text" name="ip" class="span3" value="<?php
if (isset($_SESSION['ip']))
{
	echo htmlspecialchars($_SESSION['ip'], ENT_QUOTES);
	unset($_SESSION['ip']);
}
?>">
					<label><?php echo T_('SSH Login'); ?></label>
						<input type="text" name="login" class="span3" value="<?php
if (isset($_SESSION['login']))
{
	echo htmlspecialchars($_SESSION['login'], ENT_QUOTES);
	unset($_SESSION['login']);
}
?>">
					<label><?php echo T_('SSH Password'); ?></label>
						<input type="password" name="password" class="span3">
					<label><?php echo T_('Confirm Password'); ?></label>
						<input type="password" name="password2" class="span3">
					<label><?php echo T_('SSH Port'); ?></label>
						<input type="text" name="sshport" class="span1" placeholder="22" value="<?php
if (isset($_SESSION['sshport']))
{
	echo htmlspecialchars($_SESSION['sshport'], ENT_QUOTES);
	unset($_SESSION['sshport']);
}
?>">
					<label><?php echo T_('OS Type'); ?></label>
						<input type="text" class="input-xlarge disabled" disabled="" placeholder="Linux">
					<label><?php echo T_('Admin Notes'); ?></label>
						<textarea name="notes" class="textarea span10"><?php
if (isset($_SESSION['notes']))
{
	echo htmlspecialchars($_SESSION['notes'], ENT_QUOTES);
	unset($_SESSION['notes']);
}
?></textarea>
					<label class="checkbox">
						<input type="checkbox" name="verify" checked="checked">&nbsp;<?php echo T_('Verify Login &amp; Password'); ?>
					</label>
					<div style="text-align: center; margin-top: 19px;">
						<button type="submit" class="btn btn-primary"><?php echo T_('Add New Box'); ?></button>
					</div>
					<div style="text-align: center; margin-top: 19px;">
						<ul class="pager">
							<li>
								<a href="box.php"><?php echo T_('Back to Boxes'); ?></a>
							</li>
						</ul>
					</div>
				</form>
			</div>
<?php


include("./bootstrap/footer.php");
?>