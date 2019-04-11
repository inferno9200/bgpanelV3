<?php
$page = 'clientadd';
$tab = 1;
$return = 'clientadd.php';

require("../configuration.php");
require("./include.php");


$title = T_('Add New Client');

include("./bootstrap/header.php");
include("./bootstrap/notifications.php");


?>
			<div class="well">
				<form method="post" action="clientprocess.php">
					<input type="hidden" name="task" value="clientadd" />
					<label><?php echo T_('Username'); ?></label>
						<input type="text" name="username" class="span4" value="<?php
if (isset($_SESSION['username']))
{
	echo htmlspecialchars($_SESSION['username'], ENT_QUOTES);
	unset($_SESSION['username']);
}
?>">
					<label><?php echo T_('Password'); ?></label>
						<input type="text" name="password" class="span3" placeholder="">
						<span class="help-inline"><?php echo T_('Leave blank for random password'); ?></span>
					<label><?php echo T_('First Name'); ?></label>
						<input type="text" name="firstname" class="span4" value="<?php
if (isset($_SESSION['firstname']))
{
	echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES);
	unset($_SESSION['firstname']);
}
?>">
						<span class="help-inline"><?php echo T_('Optional'); ?></span>
					<label><?php echo T_('Last Name'); ?></label>
						<input type="text" name="lastname" class="span4" value="<?php
if (isset($_SESSION['lastname']))
{
	echo htmlspecialchars($_SESSION['lastname'], ENT_QUOTES);
	unset($_SESSION['lastname']);
}
?>">
						<span class="help-inline"><?php echo T_('Optional'); ?></span>
					<label>Email</label>
						<input type="text" name="email" class="span3" value="<?php
if (isset($_SESSION['email']))
{
	echo htmlspecialchars($_SESSION['email'], ENT_QUOTES);
	unset($_SESSION['email']);
}
?>">
					<label><?php echo T_("Client's Notes"); ?></label>
						<textarea name="notes" class="textarea span10"><?php
if (isset($_SESSION['notes']))
{
	echo htmlspecialchars($_SESSION['notes'], ENT_QUOTES);
	unset($_SESSION['notes']);
}
?></textarea>
					<label class="checkbox">
						<input type="checkbox" name="sendemail" checked="checked">&nbsp;<?php echo T_('Send New Client Account Email'); ?>
					</label>
					<div style="text-align: center; margin-top: 19px;">
						<button type="submit" class="btn btn-primary"><?php echo T_('Add New Client'); ?></button>
					</div>
					<div style="text-align: center; margin-top: 19px;">
						<ul class="pager">
							<li>
								<a href="client.php"><?php echo T_('Back to Clients'); ?></a>
							</li>
						</ul>
					</div>
				</form>
			</div>
<?php


include("./bootstrap/footer.php");
?>