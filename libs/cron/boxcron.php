<?php

$title = 'Cron Job';
$page = 'cron';

set_time_limit(60);

chdir(realpath(dirname(__FILE__)));
set_include_path(realpath(dirname(__FILE__)));

require('../../configuration.php');

require('../../includes/functions.php');
require('../../includes/mysql.php');
require '../vendor/autoload.php';
use phpseclib\Crypt\AES;
use phpseclib\Crypt\Random;
use phpseclib\Net\SSH2;


//------------------------------------------------------------------------------------------------------------+

query_basic( "UPDATE `".DBPREFIX."config` SET `value` = '".date('Y-m-d H:i:s')."' WHERE `setting` = 'lastcronrun'" );

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

/**
 * BOX MONITORING
 */

$boxData = array();

if (query_numrows( "SELECT `boxid` FROM `".DBPREFIX."box` ORDER BY `boxid`" ) != 0)
{
	$boxes = mysqli_query($conn, "SELECT `boxid`, `ip`, `login`, `password`, `sshport` FROM `".DBPREFIX."box`" );

	while ($rowsBoxes = mysqli_fetch_assoc($boxes))
	{
		$passphrased = file_get_contents('../../.ssh/passphrase');
		$aes = new AES(AES::MODE_ECB);
		$aes->setKeyLength(256);
		$aes->setKey($passphrased);

		$ssh = new SSH2($rowsBoxes['ip'], $rowsBoxes['sshport']);

		if (!$ssh->login($rowsBoxes['login'], $aes->decrypt($rowsBoxes['password'])))
		{
			$boxCache =	array(
				$rowsBoxes['boxid'] => array(
					'players'	=> array('players' => 0),

					'bandwidth'	=> array('rx_usage' => 0,
										 'tx_usage' => 0,
										 'rx_total' => 0,
										 'tx_total' => 0),

					'cpu'		=> array('proc' => '',
										 'cores' => 0,
										 'usage' => 0),

					'ram'		=> array('total' => 0,
										 'used' => 0,
										 'free' => 0,
										 'usage' => 0),

					'loadavg'	=> array('loadavg' => '0.00'),
					'hostname'	=> array('hostname' => ''),
					'os'		=> array('os' => ''),
					'date'		=> array('date' => ''),
					'kernel'	=> array('kernel' => ''),
					'arch'		=> array('arch' => ''),
					'uptime'	=> array('uptime' => ''),

					'swap'		=> array('total' => 0,
										 'used' => 0,
										 'free' => 0,
										 'usage' => 0),

					'hdd'		=> array('total' => 0,
										 'used' => 0,
										 'free' => 0,
										 'usage' => 0)
				)
			);

			query_basic( "UPDATE `".DBPREFIX."box` SET
				`cache` = '".mysqli_real_escape_string($conn, gzcompress(serialize($boxCache), 2))."' WHERE `boxid` = '".$rowsBoxes['boxid']."'" );

			unset($boxCache);
		}
		else
		{

			//------------------------------------------------------------------------------------------------------------+
			//We have to clean screenlog.0 files

			$servers = mysqli_query($conn, "SELECT `path` FROM `".DBPREFIX."server` WHERE `boxid` = '".$rowsBoxes['boxid']."' && `status` = 'Active'" );

			while ($rowsServers = mysqli_fetch_assoc($servers))
			{
				$screenlogExists = trim($ssh->exec('cd '.dirname($rowsServers['path']).'; test -f screenlog.0 && echo "true" || echo "false";'."\n"));

				if ( $screenlogExists == 'true' )
				{
					$ssh->exec('cd '.dirname($rowsServers['path']).'; tail -n 500 screenlog.0 > tmp; cat tmp > screenlog.0; rm tmp;'."\n");
				}

				unset($screenlogExists);
			}
			unset($servers);

			//------------------------------------------------------------------------------------------------------------+
			//Retrieves information from box

			// NETWORK INTERFACE
			/*
			$iface = trim($ssh->exec("netstat -r | grep default | awk '{print $8}'"));

			if ( !preg_match("#^eth[0-9]#", $iface) ) {
				$iface = 'eth0'; //Default value
			}
			*/
			$iface = 'eth0'; //Default value

			// BANDWIDTH
			$bandwidth_rx_total = intval(trim($ssh->exec('cat /sys/class/net/'.$iface.'/statistics/rx_bytes')));
			$bandwidth_tx_total = intval(trim($ssh->exec('cat /sys/class/net/'.$iface.'/statistics/tx_bytes')));

			// BANDWIDTH USAGE CALCULATION
			$previousBoxCache = query_fetch_assoc( "SELECT `cache` FROM `".DBPREFIX."box` WHERE `boxid` = '".$rowsBoxes['boxid']."' LIMIT 1" );

			if (!empty($previousBoxCache['cache'])) {
				$oldCache = unserialize(gzuncompress($previousBoxCache['cache']));

				$bandwidth_rx_usage = round(( $bandwidth_rx_total - $oldCache["{$rowsBoxes['boxid']}"]['bandwidth']['rx_total'] ) / ( CRONDELAY ), 2);
				$bandwidth_tx_usage = round(( $bandwidth_tx_total - $oldCache["{$rowsBoxes['boxid']}"]['bandwidth']['tx_total'] ) / ( CRONDELAY ), 2);

				// Hot fix in case of the following actions:
				// "stats have been reset"
				// "the box has been rebooted"
				if ( ($bandwidth_rx_usage < 0) || ($bandwidth_tx_usage < 0) ) {
					$bandwidth_rx_usage = 0;
					$bandwidth_tx_usage = 0;
				}
			}

			// No data
			if ( !isset($bandwidth_rx_usage) || !isset($bandwidth_tx_usage) ) {
				$bandwidth_rx_usage = 0;
				$bandwidth_tx_usage = 0;
			}

			unset($iface, $previousBoxCache, $oldCache);

			//---------------------------------------------------------+

			// CPU INFO
			$cpu_proc = trim($ssh->exec("cat /proc/cpuinfo | grep 'model name' | awk -F \":\" '{print $2}' | head -n 1"));
			$cpu_cores = intval(trim($ssh->exec("nproc")));

			// CPU USAGE
			$cpu_usage = intval(trim($ssh->exec("ps -A -u ".$rowsBoxes['login']." -o pcpu | tail -n +2 | awk '{ usage += $1 } END { print usage }'")));
			$cpu_usage = round(($cpu_usage / $cpu_cores), 2);

			//---------------------------------------------------------+

			// MEMORY INFO
			$ram_used = intval(trim($ssh->exec("awk '/^Mem/ {print $3}' <(free -b)")));
			$ram_free = intval(trim($ssh->exec("awk '/^Mem/ {print $7}' <(free -b)")));
			$ram_total = $ram_used + $ram_free;
			$ram_usage = round((($ram_used / $ram_total) * 100), 2);

			//---------------------------------------------------------+

			// LOAD AVERAGE
			$loadavg = trim($ssh->exec("top -b -n 1 | grep 'load average' | awk -F \",\" '{print $5}'"));

			//---------------------------------------------------------+

			// MISC INFO
			$hostname = trim($ssh->exec('hostname'));
			$os = trim($ssh->exec('hostnamectl | grep "Operating System" | sed "s/  Operating System: //"'));
			$date = trim($ssh->exec('date'));
			$kernel = trim($ssh->exec('uname -r'));
			$arch = trim($ssh->exec('uname -m'));

			//---------------------------------------------------------+

			// UPTIME
			$uptime = intval(trim($ssh->exec("cat /proc/uptime | awk '{print $1}'")));

			$uptimeMin = $uptime / 60;
			if ($uptimeMin > 59) {
				$uptimeH = $uptimeMin / 60;
				if ($uptimeH > 23) {
					$uptimeD = $uptimeH / 24;
				}
				else {
					$uptimeD = 0;
				}
			}
			else {
				$uptimeH = 0;
				$uptimeD = 0;
			}
			$uptime = floor($uptimeD).' days '.($uptimeH % 24).' hours '.($uptimeMin % 60).' minutes ';

			unset($uptimeMin, $uptimeH, $uptimeD);

			//---------------------------------------------------------+

			// SWAP INFO
			$swap_used = intval(trim($ssh->exec("free -b | grep 'Swap' | awk -F \":\" '{print $2}' | awk '{print $2}'")));
			$swap_free = intval(trim($ssh->exec("free -b | grep 'Swap' | awk -F \":\" '{print $2}' | awk '{print $3}'")));
			$swap_total = $swap_used + $swap_free;

			if ($swap_total != 0) {
				$swap_usage = round((($swap_used / $swap_total) * 100), 2);
			}
			else {
				$swap_usage = 0;
			}

			//---------------------------------------------------------+

			// HARD DISK DRIVE INFO
			$hdd_total = (intval(trim($ssh->exec("df -P / | tail -n +2 | head -n 1 | awk '{print $2}'"))) * 1024);
			$hdd_used = (intval(trim($ssh->exec("df -P / | tail -n +2 | head -n 1 | awk '{print $3}'"))) * 1024);
			$hdd_free = (intval(trim($ssh->exec("df -P / | tail -n +2 | head -n 1 | awk '{print $4}'"))) * 1024);
			$hdd_usage = intval(substr(trim($ssh->exec("df -P / | tail -n +2 | head -n 1 | awk '{print $5}'")), 0, -1));

			//------------------------------------------------------------------------------------------------------------+
			//Retrieves num players of the box

			$p = 0;

			$servers = mysqli_query($conn, "SELECT * FROM `".DBPREFIX."server` WHERE `boxid` = '".$rowsBoxes['boxid']."'" );

			while ($rowsServers = mysqli_fetch_assoc($servers))
			{
				$type = query_fetch_assoc( "SELECT `querytype` FROM `".DBPREFIX."game` WHERE `gameid` = '".$rowsServers['gameid']."' LIMIT 1");
				$serverIp = query_fetch_assoc( "SELECT `ip` FROM `".DBPREFIX."boxIp` WHERE `ipid` = '".$rowsServers['ipid']."' LIMIT 1" );

				if ($rowsServers['status'] == 'Active' && $rowsServers['panelstatus'] == 'Started')
				{
					//---------------------------------------------------------+
					//Querying the server
					include_once("../libs/lgsl/lgsl_class.php");

					$cache = lgsl_query_cached($type['querytype'], $serverIp['ip'], $rowsServers['port'], $rowsServers['queryport'], 0, 'sp');

					$p = $p + $cache['s']['players'];

					unset($cache);
					//---------------------------------------------------------+
				}

				unset($type);
				unset($serverIp);
			}
			unset($servers);

			//------------------------------------------------------------------------------------------------------------+
			//Data

			$boxCache =	array(
				$rowsBoxes['boxid'] => array(
					'players'	=> array('players' => $p),

					'bandwidth'	=> array('rx_usage' => $bandwidth_rx_usage,
										 'tx_usage' => $bandwidth_tx_usage,
										 'rx_total' => $bandwidth_rx_total,
										 'tx_total' => $bandwidth_tx_total),

					'cpu'		=> array('proc' => $cpu_proc,
										 'cores' => $cpu_cores,
										 'usage' => $cpu_usage),

					'ram'		=> array('total' => $ram_total,
										 'used' => $ram_used,
										 'free' => $ram_free,
										 'usage' => $ram_usage),

					'loadavg'	=> array('loadavg' => $loadavg),
					'hostname'	=> array('hostname' => $hostname),
					'os'		=> array('os' => $os),
					'date'		=> array('date' => $date),
					'kernel'	=> array('kernel' => $kernel),
					'arch'		=> array('arch' => $arch),
					'uptime'	=> array('uptime' => $uptime),

					'swap'		=> array('total' => $swap_total,
										 'used' => $swap_used,
										 'free' => $swap_free,
										 'usage' => $swap_usage),

					'hdd'		=> array('total' => $hdd_total,
										 'used' => $hdd_used,
										 'free' => $hdd_free,
										 'usage' => $hdd_usage)
				)
			);

			unset($p, $bandwidth_rx_total, $bandwidth_tx_total, $bandwidth_rx_usage, $bandwidth_tx_usage, $cpu_proc, $cpu_cores, $cpu_usage);
			unset($ram_used, $ram_free, $ram_total, $ram_usage, $loadavg, $hostname, $os, $date, $kernel, $arch, $uptime);
			unset($swap_used, $swap_free, $swap_total, $swap_usage, $hdd_total, $hdd_used, $hdd_free, $hdd_usage);

			//------------------------------------------------------------------------------------------------------------+
			//Update DB for the current box

			query_basic( "UPDATE `".DBPREFIX."box` SET
				`cache` = '".mysqli_real_escape_string($conn, gzcompress(serialize($boxCache), 2))."' WHERE `boxid` = '".$rowsBoxes['boxid']."'" );

			$boxData = $boxData + $boxCache;

			unset($boxCache);
		}

		usleep(2000);

		$ssh->disconnect();
	}
	unset($boxes);

	//------------------------------------------------------------------------------------------------------------+
	//Update dataBox table

	query_basic( "INSERT INTO `".DBPREFIX."boxData` SET
	`timestamp` = '".time()."',
	`cache` = '".mysqli_real_escape_string($conn, gzcompress(serialize($boxData), 2))."'" );

	unset($boxData);
}

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

/**
 * '*Data' table operations
 */

//---------------------------------------------------------+
// Remove old data

$time = time() - (60 * 60 * 24 * 7 * 4 * 3 + 3600);
$numOldData = mysqli_num_rows(mysqli_query($conn, "SELECT `id` FROM `".DBPREFIX."boxData` WHERE `timestamp` < '".$time."'" ));

if ($numOldData > 0)
{
	$oldData = mysqli_query($conn, "SELECT `id` FROM `".DBPREFIX."boxData` WHERE `timestamp` < '".$time."'" );
	while ($rowsData = mysqli_fetch_assoc($oldData))
	{
		query_basic( "DELETE FROM `".DBPREFIX."boxData` WHERE `id` = '".$rowsData['id']."'" );
	}
	unset($oldData);
}

//---------------------------------------------------------+
// Optimize table

$sql = "OPTIMIZE TABLE `".DBPREFIX."boxData`";

query_basic( $sql );

unset($sql);

//------------------------------------------------------------------------------------------------------------+
//------------------------------------------------------------------------------------------------------------+

/**
 * 'log' table operations
 */

//---------------------------------------------------------+
// Optimize table

$sql = "OPTIMIZE TABLE `".DBPREFIX."log`";

query_basic( $sql );

unset($sql);

//------------------------------------------------------------------------------------------------------------+
?>
