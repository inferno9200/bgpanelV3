<?php
//Prevent direct access
if (!defined('LICENSE'))
{
	exit('Access Denied');
}
$pageURL = 'http';

if (@$_SERVER["HTTPS"] == "on")
{
	$pageURL .= "s";
}

$pageURL .= "://";

if ($_SERVER["SERVER_PORT"] != "80")
{
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
}
else
{
	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
}

$systemurl = substr($pageURL, 0, -41);


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//MySQL


$mysql_link = mysqli_connect(DBHOST,DBUSER,DBPASSWORD);
if (!$mysql_link)
{
	die('Could not connect to MySQL: '.mysqli_error($conn));
}
else
{
	$mysql_database_link = mysqli_select_db($conn, DBNAME);
	if ($mysql_database_link == FALSE)
	{
		echo "Could not connect to MySQL database";
	}
	else
	{


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
		//Table structure for table "admin"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."admin`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."admin` (
		  `adminid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `username` text NOT NULL,
		  `password` text NOT NULL,
		  `firstname` text NOT NULL,
		  `lastname` text NULL,
		  `email` text NOT NULL,
		  `access` text NOT NULL,
		  `notes` text NULL,
		  `status` text NOT NULL,
		  `lang` text NOT NULL,
		  `lastlogin` datetime NOT NULL,
		  `lastactivity` text NOT NULL,
		  `lastip` text NOT NULL,
		  `lasthost` text NOT NULL,
		  `token` text NULL,
		  PRIMARY KEY  (`adminid`)
		)
		ENGINE=MyISAM  ; " );

		//Dumping data for table "admin"

			query_basic( "
		INSERT INTO `".DBPREFIX."admin` (`adminid`, `username`, `password`, `firstname`, `lastname`, `email`, `access`, `notes`, `status`, `lang`, `lastlogin`, `lastactivity`, `lastip`, `lasthost`, `token`)
		VALUES ('1', 'admin', 'b4ea66d0e3c992d2ede0070ebe521ced1d91867be182fb0a2ab620f8f66abef5dca8c785b4e4503bcae5bd42a823d5389acf639c76b62ad4959afe17cebe73ef', 'Admin', '', 'anon@nimus.com', 'Super', '', 'Active', '".DEFAULT_LOCALE."', '0000-00-00 00:00:00', '0', '~', '~', '')  ; " );

		//---------------------------------------------------------+

		//Table structure for table "box"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."box`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."box` (
		  `boxid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `name` text NOT NULL,
		  `ip` text NOT NULL,
		  `login` text NOT NULL,
		  `password` BLOB NOT NULL,
		  `sshport` text NOT NULL,
		  `notes` text NULL,
		  `cache` BLOB NULL,
		  PRIMARY KEY  (`boxid`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "boxData"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."boxData`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."boxData` (
		  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `timestamp` text NOT NULL,
		  `cache` BLOB NOT NULL,
		  PRIMARY KEY  (`id`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "boxIp"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."boxIp`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."boxIp` (
		  `ipid` int(8) unsigned NOT NULL AUTO_INCREMENT,
		  `boxid` int(8) unsigned NOT NULL,
		  `ip` text NOT NULL,
		  PRIMARY KEY (`ipid`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "client"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."client`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."client` (
		  `clientid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `username` text NOT NULL,
		  `password` text NOT NULL,
		  `firstname` text NULL,
		  `lastname` text NULL,
		  `email` text NOT NULL,
		  `notes` text NULL,
		  `status` text NOT NULL,
		  `lang` text NOT NULL,
		  `lastlogin` datetime NOT NULL,
		  `lastactivity` text NOT NULL,
		  `lastip` text NOT NULL,
		  `lasthost` text NOT NULL,
		  `created` date NOT NULL,
		  `token` text NULL,
		  PRIMARY KEY  (`clientid`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "config"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."config`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."config` (
		  `setting` varchar(255) NOT NULL,
		  `value` text NOT NULL,
		  KEY `setting` (`setting`)
		)
		ENGINE=MyISAM  ; " );

		//Dumping data for table "config"

			query_basic( "
		INSERT INTO `".DBPREFIX."config` (`setting`, `value`)
		VALUES
		  ('lastcronrun', 'Never'),
		  ('panelname', 'BrightGamePanel'),
		  ('systemurl', '".$systemurl."'),
		  ('panelversion', '".LASTBGPVERSION."'),
		  ('maintenance', '0'),
		  ('admintemplate', 'bootstrap.css'),
		  ('clienttemplate', 'bootstrap.css')  ; " );

		//---------------------------------------------------------+

		//Table structure for table "game"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."game`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."game` (
		  `gameid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `game` text NOT NULL,
		  `status` text NOT NULL,
		  `maxslots` int(4) UNSIGNED NOT NULL,
		  `defaultport` int(5) UNSIGNED NOT NULL,
		  `cfg1name` text NULL,
		  `cfg1` text NULL,
		  `cfg2name` text NULL,
		  `cfg2` text NULL,
		  `cfg3name` text NULL,
		  `cfg3` text NULL,
		  `cfg4name` text NULL,
		  `cfg4` text NULL,
		  `cfg5name` text NULL,
		  `cfg5` text NULL,
		  `cfg6name` text NULL,
		  `cfg6` text NULL,
		  `cfg7name` text NULL,
		  `cfg7` text NULL,
		  `cfg8name` text NULL,
		  `cfg8` text NULL,
		  `cfg9name` text NULL,
		  `cfg9` text NULL,
		  `startline` text NOT NULL,
		  `querytype` text NOT NULL,
		  `queryport` int(5) UNSIGNED NOT NULL,
		  `cachedir` text NULL,
		  PRIMARY KEY  (`gameid`)
		)
		ENGINE=MyISAM  ; " );

		//Dumping data for table "game"

			query_basic( "
		INSERT INTO `".DBPREFIX."game` (`gameid`, `game`, `status`, `maxslots`, `defaultport`, `cfg1name`, `cfg1`, `cfg2name`, `cfg2`, `cfg3name`, `cfg3`, `cfg4name`, `cfg4`, `cfg5name`, `cfg5`, `cfg6name`, `cfg6`, `cfg7name`, `cfg7`, `cfg8name`, `cfg8`, `cfg9name`, `cfg9`, `startline`, `querytype`, `queryport`, `cachedir`)
		VALUES
		  ('1', 'Counter-Strike: Source', 'Inactive', '32', '27015', 'Default Map', 'cs_assault', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './srcds_run -game cstrike -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', 'source', '27015', '/bgpanelV3/cs-source'),
		  ('2', 'Day of Defeat: Source', 'Inactive', '32', '27015', 'Default Map', 'dod_anzio', 'Tickrate', '100', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './srcds_run -game dod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate', 'source', '27015', '/bgpanelV3/day-of-defeat-source'),
		  ('3', 'Half-Life 2: Deathmatch', 'Inactive', '32', '27015', 'Default Map', 'dm_lockdown', 'Tickrate', '100', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './srcds_run -game hl2mp -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -tickrate {cfg2} -nohltv -autoupdate', 'source', '27015', '/bgpanelV3/halflife-2-deathmatch'),
		  ('4', 'Team Fortress 2', 'Inactive', '32', '27015', 'Default Map', 'ctf_2fort', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './srcds_run -game tf -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', 'source', '27015', '/bgpanelV3/team-fortress2'),
		  ('5', 'Left 4 Dead', 'Inactive', '8', '27015', 'Default Map', 'l4d_hospital01_apartment', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './srcds_run -game left4dead -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', 'source', '27015', '/bgpanelV3/left-4-dead'),
		  ('6', 'Left 4 Dead 2', 'Inactive', '8', '27015', 'Default Map', 'c1m1_hotel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './srcds_run -game left4dead2 -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', 'source', '27015', '/bgpanelV3/left-4-dead-2'),
		  ('7', 'Counter-Strike', 'Inactive', '32', '27015', 'Default Map', 'de_dust2', 'Pingboost', '2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './hlds_run -game cstrike +ip {ip} +port {port} +maxplayers {slots} +map {cfg1} -pingboost {cfg2} -autoupdate', 'halflife', '27015', '/bgpanelV3/cs-16'),
		  ('8', 'Killing Floor', 'Inactive', '6', '7707', 'Default Map', 'KF-Bedlam.rom', 'VACSecure', 'True', 'AdminName', 'admin', 'AdminPassword', 'passwd', 'INI File', 'KillingFloor.ini', '', '', '', '', '', '', '', '', './ucc-bin server {cfg1}?game=KFmod.KFGameType?VACSecure={cfg2}?MaxPlayers={slots}?AdminName={cfg3}?AdminPassword={cfg4} -nohomedir ini={cfg5}', 'killingfloor', '7708', '/bgpanelV3/killing-floor'),
		  ('9', 'Call of Duty 4: Modern Warfare', 'Inactive', '18', '28960', 'Server CFG File', 'server.cfg', 'fs_homepath', '/home/user/cod4', 'fs_basepath', '/home/user/cod4', '', '', '', '', '', '', '', '', '', '', '', '', './cod4_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2', 'callofduty4', '28960', '/bgpanelV3/cod4-mw'),
		  ('10', 'Minecraft', 'Inactive', '24', '25565', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'java -Xms1024M -Xmx1024M -jar minecraft_server.jar nogui', 'minecraft', '25565', '/bgpanelV3/minecraft/'),
		  ('11', 'Call of Duty: Modern Warfare 3', 'Inactive', '18', '27015', 'net_queryPort', '27014', 'net_authPort', '8766', 'net_masterServerPort', '27016', 'Server CFG File', 'server.cfg', '', '', '', '', '', '', '', '', '', '', 'xvfb-run -a wine iw5mp_server.exe +set sv_config {cfg4} +set sv_maxclients {slots} +start_map_rotate +set net_ip {ip} +set net_port {port} +set net_queryPort {cfg1} +set net_authPort {cfg2} +set net_masterServerPort {cfg3} +set dedicated 2', 'callofdutymw3', '27016', '/bgpanelV3/cod-mw3'),
		  ('12', 'Call of Duty 2', 'Inactive', '32', '28960', 'Server CFG', 'server.cfg', 'fs_homepath', '/home/user/cod2', 'fs_basepath', '/home/user/cod2', '', '', '', '', '', '', '', '', '', '', '', '', './cod2_lnxded +exec {cfg1} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2', 'callofduty2', '28960', '/bgpanelV3/cod2'),
		  ('13', 'Call of Duty: World at War', 'Inactive', '32', '28960', 'Server CFG File', 'server.cfg', 'fs_homepath', '/home/user/codwaw', 'fs_basepath', '/home/user/codwaw', '', '', '', '', '', '', '', '', '', '', '', '', './codwaw_lnxded +exec {cfg1} +set sv_maxclients {slots} +map_rotate +set net_ip {ip} +set net_port {port} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set dedicated 2', 'callofdutywaw', '28960', '/bgpanelV3/callofdutywaw'),
		  ('14', 'Wolfenstein: Enemy Territory', 'Inactive', '32', '27960', 'Server CFG File', 'server.cfg', 'fs_homepath', '/home/user/wolfet', 'fs_basepath', '/home/user/wolfet', '', '', '', '', '', '', '', '', '', '', '', '', './etded +exec {cfg1} +sv_maxclients {slots} +set fs_homepath {cfg2} +set fs_basepath {cfg3} +set net_port {port}', 'wolfet', '27960', '/bgpanelV3/wolfet'),
		  ('15', 'ArmA: 2', 'Inactive', '64', '2302', 'Server CFG File', 'server.cfg', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './server -config={cfg1} -netlog -port={port}', 'arma2', '2302', '/bgpanelV3/arma2'),
		  ('16', 'Garrysmod', 'Inactive', '16', '27015', 'Default Map', 'gm_construct', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './srcds_run -game garrysmod -ip {ip} -port {port} -maxplayers {slots} +map {cfg1} -nohltv -autoupdate', 'source', '27015', '/bgpanelV3/garrysmod'),
		  ('17', 'Counter-Strike: Global Offensive', 'Inactive', '24', '27015', 'Default Map', 'cs_italy', 'Map Group', 'mg_hostage', 'Game Type', '0', 'Game Mode', '0', 'Tickrate', '100', '', '', '', '', '', '', '', '', './srcds_run -game csgo -console -usercon -secure -nohltv -tickrate {cfg5} +ip {ip} +hostport {port} -maxplayers_override {slots} +map {cfg1} +mapgroup {cfg2} +game_type {cfg3} +game_mode {cfg4}', 'source', '27015', '/bgpanelV3/cs-go'),
		  ('18', 'ArmA: Armed Assault', 'Inactive', '64', '2302', 'Server CFG File', 'server.cfg', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './server -config={cfg1} -netlog -port={port}', 'arma', '2302', '/bgpanelV3/arma'),
		  ('19', 'Battlefield 2', 'Inactive', '64', '16567', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './start.sh', 'bf2', '29900', '/bgpanelV3/battlefield2'),
		  ('20', 'Battlefield 1942', 'Inactive', '64', '14567', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './start.sh +statusMonitor 1', 'bf1942', '23000', '/bgpanelV3/battlefield1942'),
		  ('21', 'Multi Theft Auto', 'Inactive', '128', '22003', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './mta-server -t --ip {ip} --port {port} --httpport {port} --maxplayers {slots}', 'mta', '22126', '/bgpanelV3/mta/'),
		  ('22', 'San Andreas: Multiplayer (SA-MP)', 'Inactive', '128', '7777', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './samp03svr', 'samp', '7777', '/bgpanelV3/samp/'),
		  ('23', 'Urban Terror', 'Inactive', '32', '27960', 'Server CFG File', 'server.cfg', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', './ioUrTded.i386 +set fs_game q3ut4 +set net_port {port} +set com_hunkmegs 128 +exec {cfg1} +set dedicated 2', 'urbanterror', '27960', '/bgpanelV3/urbanterror')  ; " );

		//---------------------------------------------------------+

		//Table structure for table "group"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."group`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."group` (
		  `groupid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `name` text NOT NULL,
		  `description` text NULL,
		  PRIMARY KEY  (`groupid`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "groupMember"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."groupMember`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."groupMember` (
		  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `clientid` int(8) NULL,
		  `groupids` text NULL,
		  PRIMARY KEY  (`id`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "lgsl"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."lgsl`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."lgsl` (
		  `id` int(11) UNSIGNED         NOT NULL AUTO_INCREMENT,
		  `type`       VARCHAR (50)     NOT NULL DEFAULT '',
		  `ip`         VARCHAR (255)    NOT NULL DEFAULT '',
		  `c_port`     VARCHAR (5)      NOT NULL DEFAULT '0',
		  `q_port`     VARCHAR (5)      NOT NULL DEFAULT '0',
		  `s_port`     VARCHAR (5)      NOT NULL DEFAULT '0',
		  `zone`       VARCHAR (255)    NOT NULL DEFAULT '',
		  `disabled`   TINYINT (1)      NOT NULL DEFAULT '0',
		  `comment`    VARCHAR (255)    NOT NULL DEFAULT '',
		  `status`     TINYINT (1)      NOT NULL DEFAULT '0',
		  `cache`      TEXT             NOT NULL,
		  `cache_time` TEXT             NOT NULL,
		  PRIMARY KEY  (`id`)
		)
		ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci  ; " );

		//---------------------------------------------------------+

		//Table structure for table "log"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."log`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."log` (
		  `logid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `clientid` int(8) UNSIGNED NULL,
		  `scriptid` int(8) UNSIGNED NULL,
		  `serverid` int(8) UNSIGNED NULL,
		  `boxid` int(8) UNSIGNED NULL,
		  `message` text NOT NULL,
		  `name` text NOT NULL,
		  `ip` text NOT NULL,
		  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`logid`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "script"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."script`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."script` (
		  `scriptid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `groupid` int(8) UNSIGNED NULL,
		  `boxid` int(8) UNSIGNED NOT NULL,
		  `catid` int(8) UNSIGNED NOT NULL,
		  `name` text NOT NULL,
		  `description` text NULL,
		  `status` text NOT NULL,
		  `panelstatus` text NULL,
		  `startline` text NOT NULL,
		  `filename` text NOT NULL,
		  `homedir` text NOT NULL,
		  `type` int(1) NOT NULL,
		  `screen` text NULL,
		  PRIMARY KEY  (`scriptid`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "scriptCat"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."scriptCat`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."scriptCat` (
		  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `name` text NOT NULL,
		  `description` text NULL,
		  PRIMARY KEY  (`id`)
		)
		ENGINE=MyISAM  ; " );

		//---------------------------------------------------------+

		//Table structure for table "server"

			query_basic( "DROP TABLE IF EXISTS `".DBPREFIX."server`  ; " );
			query_basic( "
		CREATE TABLE `".DBPREFIX."server` (
		  `serverid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `groupid` int(8) UNSIGNED NOT NULL,
		  `boxid` int(8) UNSIGNED NOT NULL,
		  `ipid` int(8) UNSIGNED NOT NULL,
		  `gameid` int(8) UNSIGNED NOT NULL,
		  `name` text NOT NULL,
		  `game` text NOT NULL,
		  `status` text NOT NULL,
		  `panelstatus` text NOT NULL,
		  `slots` int(4) UNSIGNED NOT NULL,
		  `port` int(5) UNSIGNED NOT NULL,
		  `queryport` int(5) UNSIGNED NOT NULL,
		  `priority` text NOT NULL,
		  `cfg1name` text NULL,
		  `cfg1` text NULL,
		  `cfg2name` text NULL,
		  `cfg2` text NULL,
		  `cfg3name` text NULL,
		  `cfg3` text NULL,
		  `cfg4name` text NULL,
		  `cfg4` text NULL,
		  `cfg5name` text NULL,
		  `cfg5` text NULL,
		  `cfg6name` text NULL,
		  `cfg6` text NULL,
		  `cfg7name` text NULL,
		  `cfg7` text NULL,
		  `cfg8name` text NULL,
		  `cfg8` text NULL,
		  `cfg9name` text NULL,
		  `cfg9` text NULL,
		  `startline` text NOT NULL,
		  `path` text NOT NULL,
		  `screen` text NOT NULL,
		  PRIMARY KEY  (`serverid`)
		)
		ENGINE=MyISAM  ; " );


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+


		mysqli_close($mysql_link);
	}
}


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+


?>
