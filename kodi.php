<html>
<head>

	<title>Kodi Launcher</title>
	
	<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
	<meta name="application-name" content="Kodi Launcher"/>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
	<meta name="keywords" content="iPhone, iPod, iPad, remote, control, php" />
	<meta name="description" content="Kodi Launcher" />
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes"  />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<link rel="apple-touch-icon" sizes="120x120" href="img/kodi-ios-120.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="img/kodi-ios-152.png" />
	<link rel="apple-touch-icon" sizes="180x180" href="img/kodi-ios-180.png" />
	<link rel="shortcut icon" sizes="192x192" href="img/kodi-android.png">
	<link rel="shortcut icon" href="img/kodi-fav.ico">
	<meta name="msapplication-TileImage" content="img/kodi-win-270.png">
	<meta name="msapplication-TileColor" content="#12B2E7">
</head>

<?php

$ip=$_SERVER['SERVER_ADDR'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$remote=parse_ini_file("remote.cfg",true);

function screenref($force){
	
	GLOBAL $remote;

	exec('lsb_release -si',$os);
	if (!stristr($os[0],'Ubuntu')) {
		exec('fbset -depth 8 && fbset -depth 16');
		if ($console!=$remote['options']['console']){
			exec('sudo chvt '.$remote['options']['console']);				
		}
	}
	else {
		exec('sudo chvt '.$remote['options']['nextconsole'].'; sudo chvt '.$remote['options']['console']); 
	}

	
	exec('DISPLAY="${DISPLAY:-:0}" sudo -u '.$remote['options']['user'].' xrefresh');
	unset($os);
}

function isIphone($user_agent=NULL) {
	if(!isset($user_agent)) {
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}
	return (strpos($user_agent, 'iPhone') !== FALSE);
}

function isIpad($user_agent=NULL) {
	if(!isset($user_agent)) {
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}
	return (strpos($user_agent, 'iPad') !== FALSE);
}
?>

<?php

$pd_=shell_exec('pgrep omxplayer');
if (empty($pd_)){
	$pd=shell_exec('pgrep kodi');
	if (empty($pd)){
		$pid=shell_exec('pgrep lightdm');
		if (!empty($pid)) {
			exec('pgrep xscreensaver', $pid);
			if (!empty($pid)) { 
				exec ('sudo pkill xscreensaver');
				$out=shell_exec('tvservice -s | grep -o off');	    
				if ($out == "off") {
					exec('tvservice -p');
					sleep(2);
					screenref(true);
				}
			}
		}

		$cmd='bash -c "sudo -u '.$remote['options']['user'].' '.getcwd().'/kodi &>/dev/null &"';
		exec($cmd);
		sleep(7);
	}
	$pid=shell_exec('pgrep kodi');
	if (empty($pid)){
		if (isset($_GET['kodi'])) {
			?>
			<script>
				window.location.href = 'index.php?kodi=';
			</script>								
			<?php
		}
		else{
			?>
			<h3>Error Loading Kodi...</h3>
			<?php
			die();
		}
	} 
	if ((!empty($remote['kodi']['ios'])) && ((isIphone()) || (isIpad()))) {

		?>

		<script>
			window.location.href = <?php echo json_encode($remote['kodi']['ios']); ?>;
		</script>

		<?php
	}
	else { 
		if (!empty($remote['kodi']['port'])) 
		{ 
			if (!empty($remote['options']['mdns'])) { 
				$ip=$remote['options']['mdns'];
			} 
			if ((empty($remote['kodi']['username'])) || (empty($remote['kodi']['password']))) 
			{ 
				$credit='';
			} 
			else 
			{
				$credit=$remote['kodi']['username'].':'.$remote['kodi']['password'].'@';
			} 
			$addr='http://'.$credit.$ip.':'.$remote['kodi']['port']; 
		}

		?>
		<script>
			window.location.href = <?php echo json_encode($addr)?>;
		</script>

		<?php
	}
}
?>

</html>