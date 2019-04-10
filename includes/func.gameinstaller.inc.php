<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}



/**
 * Game Server Path Hotfix
 *
 * Add to the game server path its associated binary (depending the full game name)
 *
 * Only required by serveradd.php during form process
 */
function addBin2GameServerPath( $path, $game )
{
	// Known List
	$binaries = array(
		"Minecraft"							=> "minecraft_server.jar",
		"Multi Theft Auto"					=> "mta-server",
		"San Andreas: Multiplayer (SA-MP)"	=> "samp03svr" );

	// Fix path
	$len = strlen($path);
	if ( $path[$len-1] != '/' ) {
			// Add ending slash
			$path = $path.'/';
	}

	// Process
	if (array_key_exists( $game, $binaries )) {
		return $path.$binaries[$game];
	}
	else {
		return $path.'bin.bin';
	}
}

?>
