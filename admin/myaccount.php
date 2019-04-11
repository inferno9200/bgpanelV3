<?php
$page = 'myaccount';
$tab = 9;
$isSummary = TRUE;
$return = 'myaccount.php';


require("../configuration.php");
require("./include.php");


$title = T_('My Account');


$rows = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."admin` WHERE `adminid` = '".$_SESSION['adminid']."' LIMIT 1" );


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#"><?php echo T_('Profile'); ?></a></li>
			</ul>
			<div class="well">
				<form method="post" action="process.php">
					<input type="hidden" name="task" value="myaccount" />
					<input type="hidden" name="adminid" value="<?php echo $_SESSION['adminid']; ?>" />
					<label><?php echo T_('First Name'); ?></label>
						<input type="text" name="firstname" class="span4" value="<?php echo htmlspecialchars($rows['firstname'], ENT_QUOTES); ?>">
					<label><?php echo T_('Last Name'); ?></label>
						<input type="text" name="lastname" class="span4" value="<?php echo htmlspecialchars($rows['lastname'], ENT_QUOTES); ?>">
					<label><?php echo T_('Email'); ?></label>
						<input type="text" name="email" class="span3" value="<?php echo htmlspecialchars($rows['email'], ENT_QUOTES); ?>">
					<label><?php echo T_('Username'); ?></label>
						<input type="text" name="username" class="span4" value="<?php echo htmlspecialchars($rows['username'], ENT_QUOTES); ?>">
					<label><?php echo T_('Password'); ?></label>
						<input type="password" name="password" class="span3" placeholder="">
						<span class="help-inline"><?php echo T_('Leave blank for no change'); ?></span>
					<label><?php echo T_('Password'); ?></label>
						<input type="password" name="password2" class="span3" placeholder="">
					<label><?php echo T_('Language'); ?></label>
						<select class="span2" name="language">
<?php
//---------------------------------------------------------+
foreach ($languages as $key => $value)
{
	if ($value == htmlspecialchars($rows['lang'], ENT_QUOTES))
	{
		$output = "\t\t\t\t\t\t\t<option value=\"".$value."\" selected=\"selected\">".$key."</option>\r\n";
		echo $output;
	}
	else
	{
		$output = "\t\t\t\t\t\t\t<option value=\"".$value."\">".$key."</option>\r\n";
		echo $output;
	}
}
//---------------------------------------------------------+
?>						</select>
					<div style="text-align: center;">
						<ul class="pager">
							<li>
								<button type="submit" class="btn btn-primary"><?php echo T_('Save Changes'); ?></button>
							</li>
							<li>
								<button type="reset" class="btn"><?php echo T_('Cancel Changes'); ?></button>
							</li>
						</ul>
					</div>
				</form>
			</div>
<?php


include("./bootstrap/footer.php");
?>