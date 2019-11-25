<?php
$page = 'utilitiesversion';
$tab = 4;
$return = 'utilitiesversion.php';


require("../configuration.php");
require("./include.php");


$title = T_('Version Check');


/**
 * REMOTE VERSION RETRIEVER
 * Retrieve the last version of the panel from www.bgpanel.net
 */
$url = 'https://raw.githubusercontent.com/DopeProjects/bgpanelV3/master/.version/remote.json';
$exo = file_get_contents($url);
$data = json_decode($exo);



include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


if ( (BRANCH != 'devel') && (version_compare(COREVERSION, $data->VR) == -1) )
{
?>
			<div class="alert">
				<strong><?php echo T_('Software Update Available!'); ?></strong>
				<p><?php echo T_('It is strongly recommended that you apply this update to BrightGamePanelV3 as soon as possible.'); ?></p>
			</div>
			<div class="container"><div style="text-align: center;"><a class="btn btn-large btn-large btn-primary" type="button" href="https://github.com/DopeProjects/bgpanelV3" target="_blank"><i class="icon-download-alt icon-white"></i>&nbsp;<?php echo T_('Download From Github'); ?></a></div></div>
<?php
}
else
{
?>
			<div class="alert alert-success">
				<strong>Your system is up-to-date!</strong>
			</div>
<?php
}

?>
			<div class="pagination"></div>
			<div class="well">
				<div class="row-fluid">
					<div class="span6">
						<legend><?php echo T_('Current Install'); ?></legend>
						<form>
							<label>Project</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo PROJECT; ?>">
							<label>Package</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo PACKAGE; ?>">
							<label>Branch</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo BRANCH; ?>">
							<label>Version</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo COREVERSION; ?>">
							<label>Release Date</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo RELEASEDATE; ?>">
						</form>
					</div>

					<div class="span6">
						<legend>Remote Version</legend>
						<form>
							<label>Project</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo $data->PR ?>">
							<label>Package</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo $data->PA ?>">
							<label>Branch</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo $data->BR ?>">
							<label>Version</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo $data->VR ?>">
							<label>Release Date</label>
								<input class="input-xlarge disabled" type="text" disabled="" placeholder="<?php echo $data->RE ?>">
						</form>
					</div>
				</div>
			</div>
<?php
unset($request, $data);


include("./bootstrap/footer.php");
?>