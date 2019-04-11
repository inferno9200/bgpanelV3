<?php
$page = 'boxprofile';
$tab = 3;
$isSummary = TRUE;
###
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
	$boxid = $_GET['id'];
}
else
{
	exit('Error: BoxID error.');
}
###
$return = 'boxprofile.php?id='.urlencode($boxid);


require("../configuration.php");
require("./include.php");

$title = T_('Box Profile');

if (query_numrows( "SELECT `name` FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."'" ) == 0)
{
	exit('Error: BoxID is invalid.');
}


$rows = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."box` WHERE `boxid` = '".$boxid."' LIMIT 1" );


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<ul class="nav nav-tabs">
				<li><a href="boxsummary.php?id=<?php echo $boxid; ?>"><?php echo T_('Summary'); ?></a></li>
				<li class="active"><a href="boxprofile.php?id=<?php echo $boxid; ?>"><?php echo T_('Profile'); ?></a></li>
				<li><a href="boxip.php?id=<?php echo $boxid; ?>"><?php echo T_('IP Addresses'); ?></a></li>
				<li><a href="boxserver.php?id=<?php echo $boxid; ?>"><?php echo T_('Servers'); ?></a></li>
				<li><a href="boxchart.php?id=<?php echo $boxid; ?>"><?php echo T_('Charts'); ?></a></li>
				<li><a href="boxgamefile.php?id=<?php echo $boxid; ?>"><?php echo T_('Game File Repositories'); ?></a></li>
				<li><a href="boxlog.php?id=<?php echo $boxid; ?>"><?php echo T_('Activity Logs'); ?></a></li>
			</ul>
			<div class="well">
				<form method="post" action="boxprocess.php">
					<input type="hidden" name="task" value="boxprofile" />
					<input type="hidden" name="boxid" value="<?php echo $boxid; ?>" />
					<label><?php echo T_('Server Name'); ?></label>
						<input type="text" name="name" class="span4" value="<?php echo htmlspecialchars($rows['name'], ENT_QUOTES); ?>">
					<label><?php echo T_('IP Address'); ?></label>
						<input type="text" name="ip" class="span3" value="<?php echo htmlspecialchars($rows['ip'], ENT_QUOTES); ?>">
					<label><?php echo T_('SSH Login'); ?></label>
						<input type="text" name="login" class="span3" value="<?php echo htmlspecialchars($rows['login'], ENT_QUOTES); ?>">
					<label><?php echo T_('SSH Password'); ?></label>
						<input type="password" name="password" class="span3">
						<span class="help-inline"><?php echo T_('Leave blank for no change'); ?></span>
					<label><?php echo T_('SSH Port'); ?></label>
						<input type="text" name="sshport" class="span1" value="<?php echo htmlspecialchars($rows['sshport'], ENT_QUOTES); ?>">
					<label><?php echo T_('OS Type'); ?></label>
						<input type="text" class="input-xlarge disabled" disabled="" placeholder="Linux">
					<label><?php echo T_('Admin Notes'); ?></label>
						<textarea name="notes" class="textarea span10"><?php echo htmlspecialchars($rows['notes'], ENT_QUOTES); ?></textarea>
					<label class="checkbox">
						<input type="checkbox" name="verify" checked="checked">&nbsp;<?php echo T_('Verify Login &amp; Password'); ?>
					</label>
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