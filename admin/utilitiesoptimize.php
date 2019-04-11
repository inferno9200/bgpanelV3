<?php
$page = 'utilitiesoptimize';
$tab = 4;
$return = 'utilitiesoptimize.php';


require("../configuration.php");
require("./include.php");


$title = T_('Optimize Database');


//---------------------------------------------------------+

/* ANALYZE BGP TABLES */
function analyze_database()
{
	$conn = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBNAME);
	$result = mysqli_query($conn, 'SHOW TABLES');
	$i = 0;

	while($table = mysqli_fetch_row($result))
	{
		if (preg_match("#^".DBPREFIX."#", $table[0]))
		{
			$analysis[$i] = query_fetch_assoc('ANALYZE TABLE '.$table[0]);
			$i++;
		}
	}

	unset($result);

	if (isset($analysis))
	{
		return $analysis;
	}
}

//---------------------------------------------------------+

$dbanalysis = analyze_database();

//---------------------------------------------------------+


include("./bootstrap/header.php");


/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
			<div class="alert alert-info">
				<h4 class="alert-heading"><?php echo T_('Tip'); ?></h4>
				<?php echo T_('This operation tells the MySQL server to clean up the database tables, optimizing them for better performance.'); ?><br />
				<?php echo T_('It is recommended that you run this at least once a month.'); ?>
			</div>
			<div class="container">
				<div style="text-align: center;">
					<a class="btn btn-large btn-large btn-primary" type="button" href="utilitiesoptimizeprocess.php?task=optimize"><i class="icon-wrench icon-white"></i>&nbsp;<?php echo T_('Optimize!'); ?></a>
				</div>
			</div> <!-- End Container -->
			<div class="pagination"></div>
			<div class="well">
				<div style="text-align: center; margin-bottom: 5px;">
					<span class="label label-info"><?php echo T_('Analysis Result'); ?></span>
				</div>
				<table id="dbanalysis" class="zebra-striped">
					<thead>
						<tr>
							<th><?php echo T_('Table'); ?></th>
							<th><?php echo T_('Operation'); ?></th>
							<th><?php echo T_('Msg_Type'); ?></th>
							<th><?php echo T_('Message'); ?></th>
						</tr>
					</thead>
					<tbody>
<?php

foreach($dbanalysis as $key => $value)
{
?>
						<tr>
							<td><?php echo $value['Table']; ?></td>
							<td><?php echo $value['Op']; ?></td>
							<td><?php echo $value['Msg_type']; ?></td>
							<td><?php echo $value['Msg_text']; ?></td>
						</tr>
<?php
}
unset($dbanalysis);

?>
					</tbody>
				</table>
				<script type="text/javascript">
				$(document).ready(function() {
					$("#dbanalysis").tablesorter({
						sortList: [[0,0]]
					});
				});
				</script>
			</div>
<?php


include("./bootstrap/footer.php");
?>