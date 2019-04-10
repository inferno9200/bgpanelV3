<?php
$page = 'scriptcatedit';
$tab = 5;
$isSummary = TRUE;
###
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
	$catid = $_GET['id'];
}
else
{
	exit('Error: CatID error.');
}
###
$return = 'scriptcatedit.php?id='.urlencode($catid);


require("../configuration.php");
require("./include.php");


$title = T_('Edit Script Category');


if (query_numrows( "SELECT `name` FROM `".DBPREFIX."scriptCat` WHERE `id` = '".$catid."'" ) == 0)
{
	exit('Error: CatID is invalid.');
}


$rows = query_fetch_assoc( "SELECT * FROM `".DBPREFIX."scriptCat` WHERE `id` = '".$catid."' LIMIT 1" );


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="well">
				<form method="post" action="scriptprocess.php">
					<input type="hidden" name="task" value="scriptcatedit" />
					<input type="hidden" name="catid" value="<?php echo $catid; ?>" />
					<label><?php echo T_('Category Name'); ?></label>
						<input type="text" name="name" class="span4" value="<?php echo htmlspecialchars($rows['name'], ENT_QUOTES); ?>">
					<label><?php echo T_('Category Description'); ?></label>
						<textarea name="notes" class="textarea span10"><?php echo htmlspecialchars($rows['description'], ENT_QUOTES); ?></textarea>
					<div style="text-align: center; margin-top: 19px;">
						<button type="submit" class="btn btn-primary"><?php echo T_('Save Changes'); ?></button>
						<button type="reset" class="btn"><?php echo T_('Cancel Changes'); ?></button>
					</div>
					<div style="text-align: center; margin-top: 19px;">
						<ul class="pager">
							<li>
								<a href="scriptcatmanage.php"><?php echo T_('Back to Scripts Categories'); ?></a>
							</li>
						</ul>
					</div>
				</form>
			</div>
<?php


include("./bootstrap/footer.php");
?>
