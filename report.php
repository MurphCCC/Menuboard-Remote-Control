<html>
<head>
	<title>Raspberry Remote Report Page</title>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
	<body>

		<?php

		$apache_user = posix_getpwuid(posix_geteuid());
		$report = array (
			
			'1' => 'Path is not writable for user '.$apache_user['name'], 
			'2' => 'Omxplayer is not installed. Install omxplayer running, "sudo apt-get install omxplayer" (without quotes).',
			'3' => 'Video is not writable for user '.$apache_user['name'].'. Grant video access running "sudo usermod -a -G video '.$apache_user['name'].'" (without quotes). This will allow omxplayer to access video. You can grant audio access too, running "sudo usermod -a -G audio '.$apache_user['name'].'" (without quotes).', 
			'4' => 'Some files are not executale, please check permissions.' 
			
			);

		if (isset($_GET['report'])){
			print '<h3>Error!</h3>';
			print '<h4>'.$report[$_GET['report']].'</h4>';
		}
		?>	

	</body>
</head>
</html>
