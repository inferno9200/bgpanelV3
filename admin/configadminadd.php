<?php
$page = 'configadminadd';
$tab = 5;
$return = 'configadminadd.php';


require("../configuration.php");
require("./include.php");

$title = T_('Add New Administrator');

include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="well">
				<form method="post" action="configadminprocess.php">
					<input type="hidden" name="task" value="configadminadd" />
					<label><?php echo T_('Username'); ?></label>
						<input type="text" name="username" class="span4" value="<?php
if (isset($_SESSION['username']))
{
	echo htmlspecialchars($_SESSION['username'], ENT_QUOTES);
	unset($_SESSION['username']);
}
?>">
					<label><?php echo T_('Password'); ?></label>
						<input type="password" name="password" class="span3" placeholder="">
					<label><?php echo T_('Confirm Password'); ?></label>
						<input type="password" name="password2" class="span3" placeholder="">
					<label><?php echo T_('First Name'); ?></label>
						<input type="text" name="firstname" class="span4" value="<?php
if (isset($_SESSION['firstname']))
{
	echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES);
	unset($_SESSION['firstname']);
}
?>">
					<label><?php echo T_('Last Name'); ?></label>
						<input type="text" name="lastname" class="span4" value="<?php
if (isset($_SESSION['lastname']))
{
	echo htmlspecialchars($_SESSION['lastname'], ENT_QUOTES);
	unset($_SESSION['lastname']);
}
?>">
						<span class="help-inline"><?php echo T_('Optional'); ?></span>
					<label><?php echo T_('Email'); ?></label>
						<input type="text" name="email" class="span3" value="<?php
if (isset($_SESSION['email']))
{
	echo htmlspecialchars($_SESSION['email'], ENT_QUOTES);
	unset($_SESSION['email']);
}
?>">
					<label><?php echo T_('Access Level'); ?></label>
						<select name="access">
							<option value="Super" <?php
if (!empty($_SESSION['access']) && $_SESSION['access'] == 'Super')
{
	echo " selected=\"selected\"";
	unset($_SESSION['access']);
}
?>><?php echo T_('Super Administrator'); ?></option>
							<option value="Full" <?php
if (!empty($_SESSION['access']) && $_SESSION['access'] == 'Full')
{
	echo " selected=\"selected\"";
	unset($_SESSION['access']);
}
?>><?php echo T_('Full Administrator'); ?></option>
							<option value="Limited" <?php
if (!empty($_SESSION['access']) && $_SESSION['access'] == 'Limited')
{
	echo " selected=\"selected\"";
	unset($_SESSION['access']);
}
?>><?php echo T_('Limited Administrator'); ?></option>
						</select>
					<div style="text-align: center; margin-top: 19px;">
						<button type="submit" class="btn btn-primary"><?php echo T_('Add New Administrator'); ?></button>
					</div>
					<div style="text-align: center; margin-top: 19px;">
						<ul class="pager">
							<li>
								<a href="configadmin.php"><?php echo T_('Back to Administrators'); ?></a>
							</li>
						</ul>
					</div>
				</form>
			</div>
<?php


include("./bootstrap/footer.php");
?>