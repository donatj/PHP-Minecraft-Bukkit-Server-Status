<?php

date_default_timezone_set( 'America/Chicago' );
define('USER_YAML', '/opt/craftbukkit/plugins/Essentials/userdata/');

$online = @fsockopen("127.0.0.1", 25565, $errno, $errstr, 1);

$player_files = scandir( USER_YAML );
$users = array();
foreach( $player_files as $pfile ) {
	if( $pfile[0] != '.' ) {

		$user = array();

		$data = file_get_contents(USER_YAML . $pfile);
		$user['username'] = str_replace('.yml', '', $pfile);
		if (preg_match_all('/(?P<action>login|logout): (?P<timestamp>[0-9]+)/', $data, $regs, PREG_SET_ORDER)) {
			foreach( $regs as $reg ) {
				$user[ $reg['action'] ] = (int)bcdiv( $reg['timestamp'], 1000 );
			}

			$users['all'][$user['username']] = $user;

			if( $user['login'] > $user['logout'] ) {
				$users['online'][$user['username']] =& $users['all'][$user['username']];
			}else{
				$users['offline'][$user['username']] =& $users['all'][$user['username']];
			}

		}

	}
}

?>
<!doctype html>
<html lang="en">
	<head>
		<title>Donat Studios Minecraft Server</title>
		<link href='http://fonts.googleapis.com/css?family=Aldrich' rel='stylesheet' type='text/css'>
		<style>
		body {
			background: url(images/bg.png);
		}
		
		h1, h2 {
			font-family: 'Aldrich', Arial;
		}
		
		#main {
			width: 600px;
			background: white;
			margin: 0 auto;
		}
		</style>
	</head>
	<body>
		<div id="main">
		<h1>Minecraft</h1>
		<?php
					
			if($online) {
				echo '<h2 style="color: green">Server is Online!</h2>';
			}else{
				echo '<h2 style="color: red">Server is Offline!</h2>';
			}
			
			echo '<hr />';
			
			
			foreach( $users['online'] as $user => $user_data ) {
				echo '<div><img src="images/person.png" width="32" valign="top"><strong>'.$user.'</strong> online since '.date('r', $user_data['login']).'<div>';
			}
			
			foreach( $users['offline'] as $user => $user_data ) {
				echo '<div><img src="images/person_grey.png" width="32" valign="top"><strong>'.$user.'</strong> offline since '.date('r', $user_data['logout']).'<div>';
			}

		?>
		</div>
	</body>
</html>