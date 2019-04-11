<?php
$page = 'utilitiesphpinfo';
$tab = 4;
$return = 'utilitiesphpinfo.php';

require("../configuration.php");
require("./include.php");

$title = T_('PHP Info');

include("./bootstrap/header.php");
/**
 * Notifications
 */
include("./bootstrap/notifications.php");


?>
<div class="well">
	<div style="width:auto;height:480px;overflow:scroll;overflow-y:scroll;overflow-x:hidden;">
<?php

/**
 * php at SPAMMENOT dot tof2k dot com 10-Sep-2006 03:32
 * http://php.net/manual/fr/function.phpinfo.php
 * "obtain a phpinfo without headers (and css)"
 */

ob_start();
phpinfo();
$info = ob_get_contents();
ob_end_clean();
$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);

echo "\r\n<!--PHP Info-->\r\n";
echo $info;
echo "\r\n<!--END : PHP Info-->\r\n";
?>
	</div>
</div>
<?php


include("./bootstrap/footer.php");
?>