<?php
$page = 'configcron';
$tab = 5;
$return = 'configcron.php';


require("../configuration.php");
require("./include.php");


$title = T_('Cron Settings');


include("./bootstrap/header.php");


?>
			<div class="alert alert-info">
				<h4 class="alert-heading"><?php echo T_('Tip'); ?></h4>
				<?php echo T_('To enable server monitoring, set up the cron job to run every'); ?> <?php echo (CRONDELAY / 60); ?> <?php echo T_('minutes.'); ?><br />
				<?php echo T_('More information at'); ?>:&nbsp;<a target="_blank" href="http://wiki.bgpanel.net/doku.php?id=wiki:setting_up_cron_job"><b><u><?php echo T_('Setting Up Cron Job'); ?></u></b></a>
			</div>
			<legend><?php echo T_('Create the following Cron Job using PHP'); ?>:</legend>
			<div>
				<pre style="text-align: center;"><?php echo '*/'.(CRONDELAY / 60).' * * * * php -q '.substr(@$_SERVER['SCRIPT_FILENAME'], 0, @strrpos(@$_SERVER['SCRIPT_FILENAME'], "/")).'/cron.php > /dev/null 2>&1'; ?></pre>
			</div>
<?php


include("./bootstrap/footer.php");
?>
