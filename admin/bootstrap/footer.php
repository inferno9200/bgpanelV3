<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}
?>
			<hr>
			<footer>
				<div class="pull-left">
					All Images Are Copyrighted By Their Respective Owners.
				</div>
				<div class="pull-right" style="text-align: right;">
					<!-- INTERNET RULES APPLY #1 DON'T BE A DICKHEAD -->
					<a href="https://github.com/DopeProjects/bgpanelV3" target="_blank">BGP V3</a><br />
				</div>
			</footer>
		</div><!--/container-->

<?php

if (isAdminLoggedIn() == TRUE)
{
?>
		<script type="text/javascript">
		$(document).ready(function() {
			<!-- Header Tooltips -->
			$('#gototop').tooltip({placement: 'bottom'});
			$('#clock').tooltip({placement: 'bottom'});
			$('#notificationsPopover').popover({placement: 'bottom', trigger: 'hover'});
			$('#me').tooltip({placement: 'bottom'});
			$('#logout').tooltip({placement: 'bottom'});
		});
		<!-- nav-scripts -->
		function doScript(id, name, action)
		{
			if (confirm("Are you sure you want to "+action+" script: "+name+"?"))
			{
				if (action == 'launch') { action = 'start'; }
				window.location="scriptprocess.php?task=script"+action+"&scriptid="+id;
			}
		}
		</script>

<?php
}

?>
	</body>
</html>
