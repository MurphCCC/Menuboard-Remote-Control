<html>
<head>

	<title>Raspberry Remote</title>
	
	<meta Http-Equiv="Cache-Control" Content="no-cache">
	<meta Http-Equiv="Pragma" Content="no-cache">
	<meta Http-Equiv="Expires" Content="0">
	<meta Http-Equiv="Pragma-directive: no-cache">
	<meta Http-Equiv="Cache-directive: no-cache">
	<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
	<meta name="application-name" content="Raspberry Remote"/>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
	<meta name="keywords" content="iPhone, iPod, iPad, remote, control, php" />
	<meta name="description" content="Web based remote control for Raspberry Pi" />
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes"  />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<link rel="apple-touch-icon" sizes="120x120" href="img/remote-ios-120.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="img/remote-ios-152.png" />
	<link rel="apple-touch-icon" sizes="180x180" href="img/remote-ios-180.png" />
	<link rel="shortcut icon" sizes="192x192" href="img/remote-android.png">
	<meta name="msapplication-TileImage" content="img/remote-win-270.png">
	<meta name="msapplication-TileColor" content="#fff">
	<link rel="shortcut icon" href="img/remote-fav.ico">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="jquery-1.12.0.js"></script>
	<script charset="utf-8" src="ajax.js"></script>

	<?php 

	header("Pragma-directive: no-cache");
	header("Cache-directive: no-cache");
	header("Cache-control: no-cache");
	header("Pragma: no-cache");
	header("Expires: 0");

	$ip=$_SERVER['SERVER_ADDR'];

	exec ('sudo -n true',$result,$sudoer);

	if ($sudoer) {
		$sudoer=false;
	}
	else {
		$sudoer=true;
	}

	require_once 'languages.php';
	require_once 'functions.php';

	if (file_exists('scripts.xml')) {
		$scripts=simplexml_load_file("scripts.xml");
	}

	if (!file_exists('remote.cfg')) {
		require_once 'setup.php';		
	}

	exec ('who -q | head -n 1',$users); $users=explode(' ',$users[0]); $users=array_unique($users);

	if (isIphone() || isIpad()) {
		$ios=true;
	}

	foreach ($users as $key => $val){
		if (($val=="pi") && (!reset($users))) {
			array_pop($users, $val);
			unset($users[$key]);
		}
		elseif (($val=="root") && (!end($users))) {
			array_push($users, $val);
			unset($users[$key]);
		}
	}

	unset($key, $val);

	exec ('locale -a | grep utf',$enc); $enc=array_unique($enc); 
	foreach ($enc as $key => $val){
		if ((strpos($val,'en')) && (!end($enc))){
			array_push($enc, $val);
			unset($enc[$key]);
		}
	}

	unset($key, $val);

	$status=shell_exec('pgrep tvheadend');
	if ((!empty($status)) && ($sudoer)){
		$tvheadend=true;
	}

	unset($status);

	$status=shell_exec('which kodi');
	if (!empty($status)){
		$kodi=true;
	}
	unset($status);

	$status=shell_exec('pgrep x11vnc');
	if (!empty($status)){
		$vnc=true;
	}
	unset($status);

	if  (!isset($channels)){			
		$channels=parse_ini_file("channels.ini",true);
	}

	if (!isset($webtv)) {			
		$webtv=parse_ini_file("webtv.ini",true);
	}

	if (!isset($dimensions))	{			
		$dimensions=parse_ini_file("logo.ini",true);
	}

	if (file_exists('history.ini')) {
		$history=parse_ini_file("history.ini",true);
	}

	require_once 'define.php';
	require_once 'css/colors.php';


	if ((isset($_GET['font'])) && (!empty($remote))) {
		if ($remote['options']['font']=="Comfortaa") {
			$remote['options']['font']="NotoSans";
			$remote['omxplayer']['font']="css/NotoSans-Regular.ttf";
		}
		else {
			$remote['options']['font']="Comfortaa";
			$remote['omxplayer']['font']="css/Comfortaa-Regular.ttf";
		}
		write_ini_file(REMOTE,$remote);
		?>
		<script>
			window.open("index.php","_self");
		</script>		
		<?php 

	}

	if (isset($_GET['forceupdate'])) {
		
		if (!empty($pid=shell_exec('pgrep omxplayer'))) {
			call('q');
		}

		if (!file_exists('prevent')) {
			exec('git fetch --all',$results); sleep(1);
			exec('git reset --hard origin/master',$results);
		}
		
		?>
		<script>
			window.open("index.php?update=","_self");
		</script>		
		<?php 

	}

	if (isset($_POST['submit-scripts'])) {

		$node=explode("|||",$_POST['script_edit']);

		if (isset($scripts)) {
			foreach ($scripts->script as $script) {

				if ($script->sh==$node[2]) {
					$script->caption=mb_convert_case($_POST['script_pre'],MB_CASE_UPPER);
					$script->color=$_POST['colors'];
					$script->sh=$_POST['script_sh'];
					$scripts->asXml('scripts.xml');
					unset($node);
					$flag=true;
					break;
				}
			}
		}

		if (!isset($flag)) {

			if (!file_exists('scripts.xml')) {
				$scripts = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><scripts></scripts>');
				$scripts->asXml('scripts.xml');
			}

			$script = $scripts->addChild('script');
			$script->addChild('caption', mb_convert_case($_POST['script_pre'],MB_CASE_UPPER));
			$script->addChild('color', $_POST['colors']);
			$script->addChild('sh', $_POST['script_sh']);
			$scripts->asXml('scripts.xml');

		}
		else {
			unset($flag);
		}
	}

	if (isset($_POST['remove-script'])) {

		$node=explode("|||",$_POST['script_edit']);
		foreach ($scripts->script as $script) {
			if ($script->sh==$node[2]) {
				$dom=dom_import_simplexml($script);
				$dom->parentNode->removeChild($dom);
				$scripts->asXml('scripts.xml');
			}
		}
	}

	if ((isset($_POST['submit-general'])) && (!empty($remote))) {

		$remote['options']['user']=$_POST['users'];
		$remote['options']['mdns']=$_POST['mdns'];
		if ((isset($_POST['themes'])) && (!empty($_POST['themes']))) {
			$remote['options']['theme']=$_POST['themes'];
		}
		if (isset($_POST['pure'])) {
			$remote['options']['pure']="1";
		}
		else {
			$remote['options']['pure']="";
		}	
		write_ini_file(REMOTE,$remote);
	?>
	<script>
		window.open("index.php","_self");
	</script>
	<?php		
	}

	if ((isset($_POST['submit-localization'])) && (!empty($remote))){

		$remote['options']['display']=$_POST['lang-option'];
		$remote['options']['locale']=$_POST['enc-option'];
		$remote['omxplayer']['lang']=mb_substr($_POST['enc-option'], 0, 2);
		if ($_POST['radio-group']=="internet") {
			if (!is_dir('./subs')) {
				if (mkdir('./subs')) {
					$remote['omxplayer']['subtitles']=$_POST['radio-group'];
				}
			}
			else {
				$remote['omxplayer']['subtitles']=$_POST['radio-group'];
			}
		}
		else {
			$remote['omxplayer']['subtitles']=$_POST['radio-group'];			
		}
		$remote['omxplayer']['font_size']=$_POST['font-size'];
		$remote['omxplayer']['font']=$_POST['fonts'];

		write_ini_file(REMOTE,$remote);

		?>
		<script>
			window.open("index.php","_self");
		</script>
		<?php	
	}

	if (isset($_POST['submit-paths'])){

		$paths = file_get_contents('paths');
		if ($_POST['folders']!=$paths) {
			file_put_contents('paths', $_POST['folders']);
			?>
			<script>
				window.open("index.php?generate=","_self");
			</script>
			<?php			
		}
	}

	if ((isset($_POST['submit-tv'])) && (!empty($remote))) {
		if ($_POST['dvbtv-option']!=null){
			$option=explode("||",$_POST['dvbtv-option']);
			if ($option[1] != $_POST['dvb_name']) {
				unset($ext);
				$image_files = glob('logo/*.{jpg,png,gif}', GLOB_BRACE);
				foreach ($image_files as $image_file) {
					$file = pathinfo($image_file);
					if ($file['filename']==$channels[$option[0]]['name']) {
						$ext='.'.$file['extension'];
						break;
					}
				}
				if (isset($ext)){
					foreach ($dimensions as $key => $val){
						if ($file['basename'] == $key) {
							$tmp1=$dimensions[$key]['width'];
							$tmp2=$dimensions[$key]['height'];
							unset($dimensions[$key]);
							$dimensions[$_POST['dvb_name'].$ext]['width']=$tmp1;
							$dimensions[$_POST['dvb_name'].$ext]['height']=$tmp2;
							$oldfile='logo/'.$key;
							$newfile='logo/'.$_POST['dvb_name'].$ext;
							if (file_exists($oldfile)){
								rename($oldfile, $newfile);
							}
							write_ini_file('logo.ini',$dimensions);
						}
					}
				}
				$channels[$option[0]]['name']=$_POST['dvb_name'];
				write_ini_file('channels.ini',$channels);
			}

		}

		unset($option,$key,$val,$tmp1,$tmp2,$oldfile,$newfile,$ext,$file,$image_file,$image_files);
	}	

	if ((isset($_POST['remove-tv'])) && (!empty($remote))){
		$option=explode("||",$_POST['dvbtv-option']);
		unset($channels[$option[0]]);
		write_ini_file('channels.ini',$channels);
		unset($option);
	}

	if (isset($_POST['submit-webtv'])){

		$options=explode("||",$_POST['webtv-option']);
		if ((!empty($_POST['name'])) && (!empty($_POST['url']))){
			if ($options[1] == $_POST['name']) {
				$webtv[$options[0]]['url']=$_POST['url'];
				if (isset($_POST['hls'])){
					$webtv[$options[0]]['hls']=1;
				}
				else {
					if (isset($webtv[$options[0]]['hls'])){
						unset($webtv[$options[0]]['hls']);
					}
				}
			}
			elseif ($options[2] == $_POST['url']) {
				unset($filename);
				$image_files = glob('logo/*.{jpg,png,gif}', GLOB_BRACE);
				foreach ($image_files as $image_file) {
					$file = pathinfo($image_file);
					if ($file['filename']==$webtv[$options[0]]['name']) {
						$filename=$file['basename'];
						rename('logo/'.$filename, 'logo/'.$_POST['name'].'.'.$file['extension']);
						break;
					}
				}
				$webtv[$options[0]]['name']=$_POST['name'];
				if (isset($_POST['hls'])){
					$webtv[$options[0]]['hls']=1;
				}
				else {
					if (isset($webtv[$options[0]]['hls'])){
						unset($webtv[$options[0]]['hls']);
					}
				}
			}
			else {
				$id=count($webtv);$id++;
				$webtv[$id]['name']=$_POST['name'];
				$webtv[$id]['url']=$_POST['url'];	
				if (isset($_POST['hls'])){
					$webtv[$id]['hls']=1;
				}

			}
		}
		write_ini_file('webtv.ini',$webtv);
	}
	if (isset($_POST['remove-webtv'])){

		$options=explode("||",$_POST['webtv-option']);
		unset($webtv[$options[0]]);
		write_ini_file('webtv.ini',$webtv);
	}

	if (isset($_POST['submit-logo'])){

		if (!empty($_POST['dimension-option'])){
			$options=explode("|",$_POST['dimension-option']); 
			if ((!empty($_POST['dimension_width'])) && (!empty($_POST['dimension_height']))){
				$dimensions[$options[0]]['height']=$_POST['dimension_height'];
				$dimensions[$options[0]]['width']=$_POST['dimension_width'];
			}
			write_ini_file('logo.ini',$dimensions);
		}

		elseif (!empty($_POST['channel_list-option'])){

			$target_dir="logo/";
			$source_ext = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
			$channel_info=explode("|",$_POST['channel_list-option']);
			$file_name=$channel_info[1].'.'.$source_ext;				
			$target_file=$target_dir.$file_name;
			$file = pathinfo($target_file);
			foreach ($dimensions as $key => $val){
				$key_fn = substr($key, 0 , (strrpos($key, ".")));
				if ($file['filename']==$key_fn) {
					unset($dimensions[$key]);
					write_ini_file('logo.ini',$dimensions);
					unlink('logo/'.$key);
				}
			}
			move_uploaded_file($_FILES['upload']['tmp_name'],$target_file);
			if ((!empty($_POST['dimension_width'])) && (!empty($_POST['dimension_height']))){
				$dimensions[$file_name]['height']=$_POST['dimension_height'];
				$dimensions[$file_name]['width']=$_POST['dimension_width'];
			}
			write_ini_file('logo.ini',$dimensions);
		}
	}
	if (isset($_POST['remove-logos'])){

		if (!empty($_POST['dimension-option'])){
			$options=explode("|",$_POST['dimension-option']); 
			unlink('logo/'.$options[0]);
			unset($dimensions[$options[0]]);
			write_ini_file('logo.ini',$dimensions);	
		}
	}

	if (isset($_POST['reset-all'])){
		mkdir('backup');
		foreach (glob('*.ini') as $ini) {
			copy($ini,getcwd().'/backup/'.$ini);
			unlink($ini);
		}
		copy(PATHS,getcwd().'/backup/'.PATHS);
		unlink(PATHS);
		copy(FILES,getcwd().'/backup/'.FILES);
		unlink(FILES);
		copy('scripts.xml',getcwd().'/backup/'.'scripts.xml');
		unlink('scripts.xml');
		unlink('youtube-dl');

		?>
		<script>
			window.open("index.php?generate=","_self"); 
		</script>
		<?php
	}
	if (isset($_POST['reset-channels'])){

		copy('channels.ini',getcwd().'/backup/channels.ini');
		unlink('channels.ini');
		copy('webtv.ini',getcwd().'/backup/webtv.ini');
		unlink('webtv.ini');
		?>
		<script>
			window.open("index.php","_self");
		</script>
		<?php
	}
	if ((isset($_POST['submit-tvheadend'])) && (!empty($remote))){

		$remote['tvheadend']['username']=$_POST['username_tvheadend'];
		$remote['tvheadend']['password']=$_POST['password_tvheadend'];
		$remote['tvheadend']['port']=$_POST['port_tvheadend'];
		$remote['tvheadend']['path']=$_POST['path'];
		write_ini_file(REMOTE,$remote);
	}

	if ((isset($_POST['submit-link'])) && (!empty($remote))){

		if ($kodi) {
			$remote['kodi']['username']=$_POST['username_kodi'];
			$remote['kodi']['password']=$_POST['password_kodi'];
			$remote['kodi']['port']=$_POST['port_kodi'];
			$remote['kodi']['ios']=$_POST['scheme_kodi'];
			$remote['kodi']['theme']=$_POST['theme'];
		}
		if ($vnc) {
			$remote['vnc']['username']=$_POST['username_vnc'];
			$remote['vnc']['password']=$_POST['password_vnc'];
			$remote['vnc']['port']=$_POST['port_vnc'];
			$remote['vnc']['ios']=$_POST['scheme_vnc'];
		}
		write_ini_file(REMOTE,$remote);
	}

	if (!empty($remote['options']['mdns'])) {
		$ip=$remote['options']['mdns'];
	}

	if (!empty($remote['options']['pure'])) {
		$pure=true;
	}

	?>

	<script>

		var command;
		var arg;
		var arg1;
		var type;
		var pos;
		var out = [];
		var delay=[];
		var filename;
		var constants = [];
		var sudoer = false;
		var width;
		var text;
		var timer=false;
		var counter=0;
		var session;
		var timeout;
		var seek30;
		var longpress;
		var paused=false;
		var webseek=0;
		var isTouchSupported = "ontouchend" in document;
		var scroll=false;
		var act;
		var zap;
		var i=0;
		var pure = false;
		

		constants=<?php echo json_encode($constants);?>;
		dvb_sync=<?php echo json_encode(DVB_SYNC.' ...');?>;
		dvb_sync_alert=<?php echo json_encode(DVB_SYNC_ALERT);?>;
		git_update=<?php echo json_encode(OUT_UPDATE.'.');?>;
		seek=<?php echo json_encode(OUT_SEEKTO.': ');?>;
		session=<?php echo json_encode($_SERVER['REMOTE_PORT']);?>;
		<?php 

		if ($sudoer) {
			?>
			sudoer=true;
			<?php
		}

		if ($pure) {
			?>
			pure=true;
			<?php
		}

		?>

		var subtitles;
		var waiting_for_subs;

		<?php 	
		if ($remote['omxplayer']['subtitles']=='internet') {
			?>
			subtitles=true;
			waiting_for_subs='<?php echo INDEX_SUBS.' ...';?>';
			<?php 
		}
		?>

		var waiting='<?php echo OUT_APPLYING.' ...';?>';
		var pause='<?php echo OUT_PAUSED;?>';
		var index_aresd=<?php echo json_encode(INDEX_ARESD); ?>;
		var index_arese=<?php echo json_encode(INDEX_ARESE); ?>;
		var index_passe=<?php echo json_encode(INDEX_PASSE); ?>;
		var index_passd=<?php echo json_encode(INDEX_PASSD); ?>;
		var index_lay0=<?php echo json_encode(INDEX_LAY0); ?>;
		var index_lay51=<?php echo json_encode(INDEX_LAY51); ?>;
		var index_lay21=<?php echo json_encode(INDEX_LAY21); ?>;
		var resolution=<?php echo json_encode(INDEX_RESO.' ...'); ?>;

	</script>

	<style type="text/css">

		#gray_layer	{

			width:100%;
			border-radius:5px;
			border-top: 2px solid;
			border-bottom: 2px solid;

		}
	</style>

</head>

<body>  
	<center>
		<div id="top"></div>
		<br>
		<br>

		<div class="wrapper_bar-nav">

			<div id="navi">
			<!--	<button class="dark" id="menu" onclick="drop(general_set);" style="">+</button>
				<button class="dark" onclick="location.href='#extra'" style="">C</button> -->
				<?php

				if ($remote['options']['webtv'] == "1") {
					?>
			<!--		<button class="dark" onclick="location.href='#webtv_tag'" style="">W</button> -->
					<?php 
				}

				if ($vnc && $ios) {

					if (empty($remote['vnc']['password']))
					{ 
						$alias='';
					} 
					else 
					{
						$alias=$remote['vnc']['username'].':'.$remote['vnc']['password'].'@';
					} 

					$addr=$remote['vnc']['ios'].$alias.$ip.':'.$remote['vnc']['port']; 
					?>

<!--					<button class="vnc" onclick="window.open('<?php echo $addr;?>','_self');" style="">V</button> -->

					<?php 
				}

				if ($kodi && $sudoer) {

					?>
					<button class="kodi" onclick="window.open('kodi.php?kodi=','_self')" style="">K</button>
					<?php 
				}

				if ($remote['options']['cache']=="1") {
					?>
				<!--	<button class="dark" onclick="ajax('generate');window.open('index.php?generate=','_self');" style="">R</button> -->
					<?php 
				}
				if ((strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)) { 
					?>
<!--					<button class="dark" id="hidescrollbars" style="">S</button> -->
					<?php
				}
					?>

<!--				<button class="dark" data-popup-open="faq" style="">?</button> -->
<!--				<button class="gray" data-popup-open="run" onclick="setTimeout(function() { $('#shell_run').focus(); }, 500);" style="">X</button>  -->

				<?php
				if (isset($scripts)) {
					foreach ($scripts->script as $script) {
						?>
						<button class="<?php if ($script->color=='black') { echo 'dark'; }else {echo 'custom';} ?>" style="background-color:<?php echo $script->color;?>;" onclick="ajax('run','','','<?php echo $script->sh;?>');"><?php echo $script->caption;?></button>
						<?php

					}
				}

				?>
				<button class="gray" data-popup-open="scripts" onclick="setTimeout(function() { $('#script_sh').focus(); }, 500);" style="">+</button>
			</div>

		</div>

		<div id="osd"></div>

		<p class="main_select" id="file_select">

			<select id="selected">
				<?php
				$last_played = $remote['options']['last'];
				if ((empty($remote['options']['cache'])) || (isset($_GET['generate'])))	
				{
					cache_files();
				}
				else 
				{
					read_files();
				}
				if (empty($vids))
				{
					?>
					<option value="" disabled selected><?php echo INDEX_SEL_FILE.' ...';?></option>
					<?php 
					$report.=OUT_NO_FILES.' ... ';

				}
				else 
				{
					foreach ($vids as $key=>$val) 
					{
						if ($remote['options']['last']!=$val) 
						{
							?>
							<option value="<?php echo $val; ?>" ><?php if ($remote['options']['theme']=='terminal') { echo '/'; } echo basename($val); ?></option>
							<?php			
						}
						else 
						{
							?>
							<option value="<?php echo $val; ?>" selected><?php if ($remote['options']['theme']=='terminal') { echo '/'; } echo basename($val); ?></option>
							<?php
							$selected=true;
						}

					} 
					unset($key, $val);
					if (!$selected)
					{
						?>
						<option value="" disabled selected><?php echo INDEX_SEL_FILE.' ...';?></option>
						<?php
					}
				}
				?>
			</select>
		</p>

		<div id="gray_layer">
			<div id="general_set" class="sliding-effect">		

				<div class="wrapper_bar">
					<button class="internal wrap_button remote" href="#" style="cursor:default;" disabled>REMOTE
					<button class="internal wrap_button" data-popup-open="general" href="#"><?php echo mb_strtoupper(ST_GENERAL);?>
					<button class="internal wrap_button" data-popup-open="localization" href="#"><?php echo mb_strtoupper(ST_LOCAL);?>
					<button class="internal wrap_button" data-popup-open="paths" id="mediapaths" href="#"><?php echo mb_strtoupper(ST_PATHS);?>
					<?php 
					if ($tvheadend) {
						?>					
						<button class="internal wrap_button" data-popup-open="tv" href="#"><?php echo mb_strtoupper(ST_TV);?>
						<?php 		
					}
					?>
					<button class="internal wrap_button" data-popup-open="webtv" href="#">WEBTV
					<button class="internal wrap_button" data-popup-open="logo" href="#"><?php echo mb_strtoupper(ST_LOGO);?>
					<?php
					if (($kodi) || ($vnc)) {
						?>
						<button class="internal wrap_button" data-popup-open="link" href="#"><?php echo ST_LINK;?>
						<?php 		
					}

					if ($tvheadend) {
						?>					
						<button class="internal wrap_button" data-popup-open="tvheadend" href="#">TVHEADEND
						<?php 		
					}
					?>

					<button class="internal wrap_button" data-popup-open="faq" href="#">FAQ</button>
					
					<div class="internal" style="margin-left:1;"></div>
					<div class="check_options internal check_option_1"><label><input onclick="ajax('cache');" type="checkbox" <?php if ($remote['options']['cache']=="1") { echo " checked"; } ?>/><span><?php echo ST_CACHE;?></span></label></div>
					<div class="check_options internal check_option_2"><label><input onclick="ajax('edid');" type="checkbox" <?php if ($remote['omxplayer']['compatible']=="yes") { echo " checked"; } ?>/><span><?php echo ST_COMPVIEW;?></span></label></div>
					<div class="check_options internal check_option_3"><label><input onclick="ajax('autoupdate');" type="checkbox" <?php if ($remote['options']['autoupdate']=="yes") { echo " checked"; } ?>/><span><?php echo ST_UPDATE;?></span></label></div>
					<div class="internal" style="margin-left:10;"></div>
					
				</div>
			</div>

			<!--GENERAL-->

			<div class="popup" data-popup="general">
				<div class="popup-inner">
					<h3 id="header_style"><?php echo mb_strtoupper(ST_GENERAL);?></h3>
					<table id="table_style">
						<tr>
							<td>
								<form method="post" action="index.php">
									<h3>
										<div class="element"><?php echo ST_SETUSER;?></div>
									</h3>
									<div class="main_select" id="config_select">
										<p>
											<select id="users" name="users" <?php if (empty($users)) { print ' disabled'; }?>>

												<?php

												//exec ('who -q | head -n 1',$users); 
												//$users=explode(' ',$users[0]); $users=array_unique($users);

												foreach ($users as $key => $val){
													if (($val=="pi") && (!reset($users))) {
														array_pop($users, $val);
														unset($users[$key]);
													}
													elseif (($val=="root") && (!end($users))) {
														array_push($users, $val);
														unset($users[$key]);
													}
												}
												foreach ($users as $key) {
													if ($remote['options']['user']==$key) { 
														?>
														<option value="<?php echo $key;?>" selected><?php echo $key;?></option>
														<?php	
													}
													else { 
														?>
														<option value="<?php echo $key;?>"><?php echo $key; ?></option>
														<?php	
													}
												}
												unset($key, $val);
												?>

											</select>
										</p>
									</div>
									<h3>
										<div class="element">M-DNS</div>
									</h3>
									<div class="main_input" id="config_input">
										<p>
											<input name="mdns" id="mdns" type="text" placeholder="raspberrypi.local" value="<?php if (!empty($remote['options']['mdns'])) { echo $remote['options']['mdns'];}?>">
										</p>
									</div>								
									<h3>
										<div class="element"><?php echo ST_THEMES;?></div>
									</h3>
									<div class="main_select" id="config_select">
										<p>
											<select id="themes" name="themes">

												<?php

												foreach ($themes['themes'] as $key => $val){
													if ($remote['options']['theme']==$key) {
												?>
														<option value="<?=$key?>" selected><?=$val?></option>
												<?php	
													}
													else { 
														?>
														<option value="<?=$key?>"><?=$val?></option>
														<?php	
													}
												}
												unset($key, $val);
												?>
											</select>
										</p>
									</div>	
									<div style="padding:3;"></div>
									<div style="position:absolute;left: 50%;transform: translateX(-55%);">
									<input type="checkbox" name="pure" id="pure" class="purebox" <?php if (!empty($remote['options']['pure'])) { echo " checked"; } ?>/><label for="pure" class="purelabel"><?=ST_IMAGE?></label>
									</div>
									<br><br>
									<p>
										<button name="reset-all" type="submit" class="remove"><?php echo ST_RESETALL;?></button>
									</p>
									<p>
										<button name="reset-channels" type="submit" class="remove"><?php echo ST_RESETCH;?></button>
									</p>
									<br>
									<p>
										<div class="submit"><button class="submit" name="submit-general" id="submit-general" type="submit" disabled><?php echo ST_SAVE;?></button></div>
									</p>
								</form>
								<div style="padding-top:1;padding-bottom:1;"></div>
								<p><button id="forceupdate" type="button" onclick="window.open('index.php?forceupdate=','_self');" class="trans" style="font-weight:600;"><?php echo ST_FORCE_UP;?></button></p>									
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">			
									<div id="don">
										<p style="position:absolute; left: 50%; transform: translateX(-50%); margin-top:12;"><?php echo ST_PP;?></p>
										<input type="hidden" name="cmd" value="_s-xclick">
										<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHFgYJKoZIhvcNAQcEoIIHBzCCBwMCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYC5bkF//z/B938W/+rfE9aqFcrDhB9aCj5IIevyPQMu2X+2voWwefRRoLtPjtqatfDMSiMgCsK4AnGnrj61TvilT/qhEUueJ1atfNBNpCpyKF56SWskulUcNDkuSrK8bxPkl0bwFRBPadE0ygdmQ8Gee5dl09o6/mdufyI1Cx1pDDELMAkGBSsOAwIaBQAwgZMGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQI1QxkgKoAPS2AcEQrq+jJs7hj/HBvT/BgZ6Q1b0OHci+VR4xmA+YZbfel5Wg1qjqrNXtTuNx01loWukx6OGXzIdywRO8P+LRburPle8xbgzx2gSBD1cfLtWXKGByR4qFs/mZG5qv4bpfE4029Qt1n6oN7btiyR/hayUygggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xNTExMTcyMjM4MzhaMCMGCSqGSIb3DQEJBDEWBBR+UXgbZEeCdVc5kVsGVrJdkNHCiDANBgkqhkiG9w0BAQEFAASBgHKrj2Aou+gd11J9p0qWDHfJ9VzPWQmM7pX0PniL3tSACeiNOncMPO5ZBWYJfPlwKf+rS8iiCFXIIy0Jd3MuLH1GPEbYqIxcwPwLED8kxyIAO3dsne9aT5YwBcwuc6o1VRCGCsHib49PTd7aGHc0CwbKvHfO5f3Ob3iAxUtjyMYX-----END PKCS7-----">
										<input id="donn" type="submit" src="" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
										<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
									</div>
								</form>	
								<br>
								<div class="email" style="text-align:center;font-size:14;margin-bottom:-5;">
									<p>dennmtr@gmail.com</p>
								</div>
							</td>
						</tr>
					</table>	
					<p><a data-popup-close="general" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
				</div>
			</div>

			<!--LOCALIZATION-->

			<div class="popup" data-popup="localization">
				<div class="popup-inner">
					<h3 id="header_style"><?php echo mb_strtoupper(ST_LOCAL);?></h3>
					<form method="post" action="index.php">
						<table id="table_style">
							<tr>
								<td>
									<h3>
										<div class="element"><?php echo ST_SYSTEM;?></div>
									</h3>
									<div class="main_select" id="config_select">
										<p> 
											<select id="enc" name="enc-option">
												<?php
												foreach ($enc as $key) {
													if ($remote['options']['locale']==$key) { ?>
													<option value="<?php echo $key;?>" selected><?php echo mb_substr($key, 0, 5); ?></option>
													<?php	}
													else { ?>
													<option value="<?php echo $key; ?>"><?php echo mb_substr($key, 0, 5); ?></option>
													<?php	}
												}
												unset($enc, $key, $val);
												?>
											</select>
										</p>
										<h3>
											<div class="element"><?php echo ST_LANG;?></div>
										</h3>
										<p>
											<select id="lang" name="lang-option">
												<?php
												foreach ($lang['lang'] as $key => $dat) {
													if ($remote['options']['display']==$key) { ?>
													<option value="<?php echo $key;?>" selected><?php echo $dat ;?></option>
													<?php	}
													else { ?>
													<option value="<?php echo $key; ?>"><?php echo $dat; ?></option>
													<?php	}
												}
												unset($key, $val);
												$lang_files = glob('*.{lng}', GLOB_BRACE);

												if (!empty($lang_files)){
													foreach ($lang_files as $lang_file) {
														$external=parse_ini_file($lang_file,true);
														if (isset($external['external']['lang'])){
															if ($remote['options']['display']==$lang_file) { 
																?>
																<option value="<?php echo $lang_file;?>" selected><?php if (!empty($external['external']['display'])){echo $external['external']['display'];}else{echo "undefined";}?> <i>[external]</i></option>
																<?php
															}
															else { 
																?>
																<option value="<?php echo $lang_file;?>"><?php if (!empty($external['external']['display'])){echo $external['external']['display'];}else{echo "undefined";}?> <i>[external]</i></option>
																<?php 
															}
														}
													}
													unset ($external,$lang_files, $lang_file, $key, $dat);}
													?>
												</select>
											</p>
										</div>
										<div style="padding-top:1;padding-bottom:1;"></div>
										<p><button id="translate" type="button" onclick="window.open('translate.php','_self');" class="trans"><?php echo "easyTRANSLATE";?></button></p>
										<h3>
											<div class="element"><?php echo ST_SUBS;?></div>
										</h3>
										<div class="element" style="float:center; padding:0; border:0;">
											<h3>
												<input id="local" value="local" class="radio-custom" name="radio-group" type="radio" <?php if (($remote['omxplayer']['subtitles']=="local") || (empty($remote['omxplayer']['subtitles']))) { print "checked";} ?>>
												<label for="local" class="radio-custom-label"><?php echo ST_SUBSLOCAL;?></label>
											</h3>
											<h3>
												<input id="internet" value="internet" class="radio-custom" name="radio-group" type="radio" <?php if ($remote['omxplayer']['subtitles']=="internet"){ print "checked";} ?> <?php  if (empty($remote['options']['subliminal'])) { print "disabled";} ?>>
												<label for="internet" class="radio-custom-label"><?php echo ST_SUBSINTERNET;?></label>
											</h3>
										</div>
										<div class="subtitle_line" style="border-bottom:1px dotted;width:100%;"></div>
										<h3>
											<div class="element"><?php echo ST_FONTSIZE;?></div>
										</h3>
										<div class="main_select" id="config_select">
											<p>
												<select id="font-size" name="font-size">
													<?php
													for ($i=18;$i<=98;$i=$i+6) {
														if ($remote['omxplayer']['font_size']==$i) { ?>
														<option value="<?php echo $i; ?>" selected><?php echo $i; ?></option>
														<?php	}
														else { ?>
														<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
														<?php	}
													}
													unset($i);
													?>
												</select>
											</p>
										</div>								
										<h3>
											<div class="element"><?php echo ST_FONT;?></div>
										</h3>								
										<div class="main_select" id="config_select">
										<p>
											<select id="fonts" name="fonts">
											<?php
												foreach(glob('css/*.{[tT][tT][fF]}', GLOB_BRACE) as $font) {
												$fonts[]=$font;}

												?>
												<option value="" <?php if (empty($fonts) || empty($remote['omxplayer']['font'])) { echo 'selected'; } ?>><?php echo ST_FONT_DEF; ?></option>
												<?php			

												foreach ($fonts as $font) {
													$font_path=pathinfo($font);
													if ($remote['omxplayer']['font']==$font) { ?>
													<option value="<?php echo $font;?>" selected><?php echo $font_path['filename']; ?></option>
													<?php	}
													else { ?>
													<option value="<?php echo $font; ?>"><?php echo $font_path['filename']; ?></option>
													<?php	}
													unset($font_path);
												}
												unset($font, $fonts);
												?>
											</select>
										</p>
									</div>
									<br>

									<p>
										<div class="submit"><button class="submit" name="submit-localization" id="submit-localization" type="submit" disabled><?php echo ST_SAVE;?></button></div>
									</p>	
								</td>
							</tr>
						</table>						
						<p><a data-popup-close="localization" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
					</form>
				</div>
			</div>

			<!--PATHS-->

			<div class="popup" data-popup="paths">
				<div class="popup-inner">
					<h3 id="header_style"><?php echo mb_strtoupper(ST_PATHS);?></h3>
					<form method="post" action="index.php">
						<table id="table_style">
							<tr>
								<td>
									<h3>
										<div class="element"><?php echo ST_MEDIAPATHS;?></div>
									</h3>
									<div class="main_input">
										<p>
											<textarea name='folders' class="area_focus" id="text" type="text"><?php	$paths = file_get_contents('paths'); $str = trim(preg_replace('/\s+/',' ', $paths)); print($paths);	?></textarea>
										</p>
									</div>
									<br>
									<p>
										<div class="submit"><button class="submit" name="submit-paths" id="submit-paths" type="submit" disabled><?php echo ST_SAVE;?></button></div>
									</p>							
								</td>
							</tr>
						</table>						
						<p><a data-popup-close="paths" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
					</form>
				</div>
			</div>

			<!--TV-->

			<?php

			if ($tvheadend) 
			{
				?> 
				<div class="popup" data-popup="tv">
					<div class="popup-inner">
						<h3 id="header_style"><?php echo mb_strtoupper(ST_TV);?></h3>
						<form method="post" action="index.php">
							<table id="table_style">
								<tr>
									<td>
										<div class="main_select" id="config_select">
											<p>
												<select id="dvbtv" name="dvbtv-option">
													<option value="null" disabled selected><?php echo ST_SEL_SERV.'...';?></option>
													<?php
													foreach ($channels as $key => $dat) { 
														?>
														<option value="<?php echo $key; ?>||<?php echo $channels[$key]['name']; ?>"><?php echo $channels[$key]['name']; ?></option>
														<?php	
													}
													unset($key,$dat);
													?>
												</select>
											</p>
										</div>
										<div class="main_input" id="config_input">
											<p>
												<input name="dvb_name" type="text" placeholder="" id="dvb_name" disabled/>
											</p>
										</div>
										<p>
											<button id="remove-tv" name="remove-tv" type="submit" class="remove" disabled><?php echo ST_REMOVE;?></button>
										</p>
										<p>
											<div class="submit"><button class="submit" name="submit-tv" id="submit-tv" type="submit" disabled><?php echo ST_SAVE;?></button></div>
										</p>
										<br>
										<p><button data-popup-close="tv" id="reallocate" name="reallocate" class="remove" ><?php echo ST_ALLOCATE;?></button></p>															

									</td>
								</tr>
							</table>						
							<p><a data-popup-close="tv" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
						</form>
					</div>
				</div>

				<?php 
			}
			?>

			<!--WEBTV-->

			<div class="popup" data-popup="webtv">
				<div class="popup-inner">
					<h3 id="header_style"><?php echo mb_strtoupper('WEBTV');?></h3>
					<form method="post" action="index.php">
						<table id="table_style">
							<tr>
								<td>
									<div class="main_select" id="config_select">
										<p>
											<select id="webtv" name="webtv-option" <?php if (isset($_GET['webtv'])) { echo "disabled"; }?>>
												<option disabled selected><?php echo ST_ADDUP.'...';?></option>
												<?php
												if (!isset($_GET['webtv'])) { 
													foreach ($webtv as $key => $dat) {
														?>
														<option value="<?php echo $key; ?>||<?php echo $webtv[$key]['name']; ?>||<?php echo $webtv[$key]['url'];?><?php if (isset($webtv[$key]['hls'])) { echo '||'.$webtv[$key]['hls']; }?>"><?php echo $webtv[$key]['name']; ?></a></option>
														<?php
													}
												}
												?>
											</select>
										</p>
									</div>
									<div class="main_input" id="config_input">
										<p>
											<input name="name" type="text" placeholder="Name" id="webtv_name"  />
										</p>
									</div>
									<div class="main_input" id="config_input">
										<p>
											<input name="url" type="text" placeholder="Url" id="webtv_url"/>
										</p>									
									</div>
									<div style="padding:3;"></div>
									<div style="position:absolute;left: 49.9%;transform: translateX(-55%);">
									<input type="checkbox" name="hls" id="hls" class="hlsbox" disabled /><label for="hls" class="hlslabel">HTML Live Stream (HLS)</label>
									</div>
									<br>
									<p>
										<?php 
										if (!isset($_GET['webtv'])) { 
											?>
											
											<button id="remove-webtv" name="remove-webtv" type="submit" class="remove" disabled><?php echo ST_REMOVE;?></button>
										</p>

										<?php 
									} 
									?>
									<p>
										<div class="submit"><button class="submit" name="submit-webtv" id="submit-webtv" type="submit" disabled><?php echo ST_SAVE;?></button></div>
									</p>							
								</td>
							</tr>
						</table>						
						<p><a data-popup-close="webtv" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
					</form>
				</div>
			</div>

			<!--LOGO-->

			<div class="popup" data-popup="logo">
				<div class="popup-inner">
					<h3 id="header_style"><?php echo mb_strtoupper(ST_LOGO);?></h3>
					<form method="post" action="index.php" enctype="multipart/form-data">
						<table id="table_style">
							<tr>
								<td>
									<div class="main_select" id="config_select">
										<div id="select_service">
											<p>
												<select id="channel_list" name="channel_list-option" disabled>
													<option disabled selected><?php echo ST_SEL_SERV.'...';?></option>
													<?php
													if ($tvheadend){
														foreach($channels as $key => $dat) {
															?>
															<option value="<?php echo $key; ?>|<?php echo $channels[$key]['name']; ?>"><?php echo $channels[$key]['name']; ?></option>
															<?php
														}
													}
													foreach ($webtv as $key => $dat) {
														?>
														<option value="<?php echo $key; ?>|<?php echo $webtv[$key]['name']; ?>|<?php echo $webtv[$key]['url']; ?>"><?php echo $webtv[$key]['name']; ?></option>
														<?php 
													}

													?>
												</select>
											</p>
										</div>
									</div>
									<div class="main_select" id="config_select">
										<div id="select_dim">
											<p>
												<select id="dimension" name="dimension-option">
													<option disabled selected><?php echo ST_EDITUP.'...';?></option>
													<?php
													foreach ($dimensions as $key=>$val) {
														?>
														<option value="<?php echo $key; ?>|<?php echo $dimensions[$key]['width']; ?>|<?php echo $dimensions[$key]['height']; ?>"><?php echo $key; ?></option>
														<?php
													}
													?>
												</select>
											</p>
										</div>
									</div>
									<div id="input_dimension">
										<div id="input_name">
											<div class="main_input" id="config_input">
												<p>
													<input type="text" placeholder="" id="dimension_name" disabled>
												</p>
											</div>
										</div>
										<div class="main_input" id="config_input">
											<p>
												<input type="number" placeholder="Width" id="dimension_width" name="dimension_width" min="1" max="100" disabled>
											</p>
										</div>
										<div class="main_input" id="config_input">
											<p>
												<input type="number" placeholder="Height" id="dimension_height" name="dimension_height" min="1" max="100" value="" disabled>
											</p>
										</div>
									</div>
									<p>
										<button id="remove-logos" name="remove-logos" type="submit" class="remove" disabled><?php echo ST_REMOVE;?></button>
									</p>

									<p>
										<div class="submit"><button class="submit" name="submit-logo" id="submit-logo" type="submit" disabled><?php echo ST_SAVE;?></button></div>
									</p>
									<br>									
									<p>
										<div id="browse_button"><p style="cursor:pointer;text-align:center;margin:auto;padding-bottom:12px;padding-top:12px;word-wrap:break-word;"><?php echo ST_BROWSE;?></p><input id="browse" name="upload" type="file" style="" accept=".jpg,.png,.gif"/></div>
									</p>
								</td>
							</tr>
						</table>						
						<p><a data-popup-close="logo" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
					</form>
				</div>
			</div>

			<!--TVHEADEND-->

			<?php 
			if ($tvheadend) {
				?>
				<div class="popup" data-popup="tvheadend">
					<div class="popup-inner">
						<h3 id="header_style"><?php echo mb_strtoupper('TVHEADEND');?></h3>
						<form method="post" action="index.php">
							<table id="table_style">
								<tr>
									<td>
										<h3>
											<div class="element"><?php echo ST_UN;?></div>
										</h3>
										<div class="main_input" id="config_input">
											<p>
												<input type="text" id="username_tvheadend" name="username_tvheadend" placeholder="" value="<?php echo $remote['tvheadend']['username']?>">
											</p>
										</div>
										<h3>
											<div class="element"><?php echo ST_PW;?></div>
										</h3>
										<div class="main_input" id="config_input">
											<p>
												<input type="password" id="password_tvheadend" name="password_tvheadend" placeholder="" value="<?php echo $remote['tvheadend']['password']?>">
											</p>
										</div>
										<h3>
											<div class="element"><?php echo ST_PORT;?></div>
										</h3>
										<div class="main_input" id="config_input">
											<p>
												<input type="number" id="port_tvheadend" name="port_tvheadend" placeholder="9981" value="<?php echo $remote['tvheadend']['port']?>">
											</p>
										</div>
										<h3>
											<div class="element"><?php echo ST_AC;?></div>
										</h3>
										<div class="main_input" id="config_input">
											<p>
												<input type="text" id="path" name="path" placeholder="localhost" value="<?php echo $remote['tvheadend']['path']?>">
											</p>
										</div>
										<p>
											<div class="submit"><button class="submit" name="submit-tvheadend" id="submit-tvheadend" type="submit" disabled><?php echo ST_SAVE;?></button></div>
										</p>								
									</td>
								</tr>
							</table>						
							<p><a data-popup-close="tvheadend" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
						</form>
					</div>
				</div>

				<?php 
			}

			if (($kodi) || ($vnc)) {

				?>
				<!--LINKS-->

				<div class="popup" data-popup="link">
					<div class="popup-inner">
						<h3 id="header_style"><?php echo ST_LINK;?></h3>
						<form method="post" action="index.php">
							<table id="table_style">
								<tr>
									<td>
										<?php 

										if ($kodi) {

											?>
											<h3 class="links element">KODI</h3>
											<h3>
												<div class="element">iOS URL Scheme</div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="scheme_kodi" id="scheme_kodi" type="url" placeholder="xbmcremote://, sybu-xbmc://remote" value="<?php echo $remote['kodi']['ios']?>">
												</p>
											</div>
											<h3>
												<div class="element"><?php echo ST_UN.' (WebUi)';?></div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="username_kodi" id="username_kodi" type="text" placeholder="" value="<?php echo $remote['kodi']['username']?>">
												</p>
											</div>
											<h3>
												<div class="element"><?php echo ST_PW.' (WebUi)';?></div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="password_kodi" id="password_kodi" type="password" placeholder="" value="<?php echo $remote['kodi']['password']?>">
												</p>
											</div>
											<h3>
												<div class="element"><?php echo ST_PORT.' (WebUi)';?></div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="port_kodi" id="port_kodi" type="number" placeholder="8081" value="<?php echo $remote['kodi']['port']?>">
												</p>
											</div>
											<?php
										}

										if ($vnc) {

											?>
											<h3 class="element links">VNC</h3>
											<h3>
												<div class="element">iOS URL Scheme</div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="scheme_vnc" id="scheme_vnc" type="url" placeholder="vnc://" value="<?php echo $remote['vnc']['ios']?>">
												</p>
											</div>
											<h3>
												<div class="element"><?php echo ST_UN;?></div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="username_vnc" id="username_vnc" type="text" placeholder="Not required" value="<?php echo $remote['vnc']['username']?>">
												</p>
											</div>
											<h3>
												<div class="element"><?php echo ST_PW;?></div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="password_vnc" id="password_vnc" type="password" placeholder="" value="<?php echo $remote['vnc']['password']?>">
												</p>
											</div>
											<h3>
												<div class="element"><?php echo ST_PORT;?></div>
											</h3>
											<div class="main_input" id="config_input">
												<p>
													<input name="port_vnc" id="port_vnc" type="number" placeholder="5900, 0" value="<?php echo $remote['vnc']['port']?>">
												</p>
											</div>
											<?php
										}
										?>
										<p>
											<div class="submit"><button class="submit" name="submit-link" id="submit-link" type="submit" disabled><?php echo ST_SAVE;?></button></div>
										</p>							
									</td>
								</tr>
							</table>						
							<p><a data-popup-close="link" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
						</form>
					</div>
				</div>

				<?php 
			}
			?>

			<!--SCRIPTS-->

			<div class="popup" data-popup="scripts">
				<div class="popup-inner">
					<h3 id="header_style"><?php echo ST_SCRIPTEDIT;?></h3>
					<form method="post" action="index.php">
						<table id="table_style">
							<tr>
								<td>

									<?php if (isset($scripts)) {
										?>
										<div class="main_select" id="config_select">
											<p>
												<select id="script_edit" name="script_edit">
													<option disabled selected><?php echo ST_ADDUP.'...';?></option>
													<?php
													foreach ($scripts->script as $script) {
														?>
														<option value="<?php echo $script->caption; ?>|||<?php echo $script->color; ?>|||<?php echo $script->sh; ?>"><?php echo $script->caption; ?></option>
														<?php
													}

													?>
												</select>
											</p>
										</div>
										<?php }
										?>
										<h3>
											<div class="element"><?php echo ST_SCR_CAP;?></div>
										</h3>
										<div class="main_input" id="config_input">
											<p>
												<?php
												$count=1;
												if (isset($scripts)) {
													foreach ($scripts->script as $script) {
														$count++;
													}
												}
												?>
												<input id="script_pre" name="script_pre" type="text" placeholder="" maxlength="20" value="<?php echo $count;?>">
											</p>
										</div>
										<h3>
											<div class="element"><?php echo ST_CMD;?></div>
										</h3>
										<div class="main_input" id="config_input">
											<p>
												<input id="script_sh" name="script_sh" type="text" placeholder="" value="">
											</p>
										</div>
										<h3>
											<div class="element"><?php echo ST_COLOR;?></div>
										</h3>
										<div class="main_select" id="config_select">
											<p>
												<select id="colors" name="colors">

													<option value="#606060" selected>Gray</option>
													<option value="black">Black</option>
													<option value="aqua">Aqua</option>
													<option value="deepskyblue">DeepSkyBlue</option>
													<option value="dodgerblue">DodgerBlue</option>
													<option value="midnightblue">MidnightBlue</option>
													<option value="blueviolet">BlueViolet</option>
													<option value="darkorchid">DarkOrchid</option>
													<option value="deeppink">DeepPink</option>
													<option value="purple">Purple</option>
													<option value="red">Red</option>
													<option value="crimson">Crimson</option>
													<option value="darkred">DarkRed</option>
													<option value="orangered">OrangeRed</option>
													<option value="darkorange">Darkorange</option>
													<option value="gold">Gold</option>
													<option value="yellow">Yellow</option>
													<option value="brown">Brown</option>
													<option value="sienna">Sienna</option>
													<option value="green">Green</option>
													<option value="lime">Lime</option>

												</select>
											</p>
										</div>
										<br>
										<button id="remove-script" name="remove-script" type="submit" class="remove" disabled><?php echo ST_REMOVE;?></button>
									</p>	
									<p>
										<div class="submit"><button class="submit" name="submit-scripts" id="submit-scripts" type="submit" disabled><?php echo ST_SAVE;?></button></div>
									</p>							
								</td>
							</tr>
						</table>						
						<p><a data-popup-close="scripts" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
					</form>
				</div>
			</div>

			<!--EXEC-->

			<div class="popup" data-popup="run">
				<div class="popup-inner">
					<h3 id="header_style"><?php echo ST_EXEC;?></h3>
					<table id="table_style">
						<tr>
							<td>
								<h3>
									<div class="element"><?php echo ST_RUNAS.' ...';?></div>
								</h3>
								<div class="main_select" id="config_select">
									<p>
										<select id="sh_users" name="sh_users">

											<?php

											$cuser=shell_exec( 'whoami' );
											?>
											<option value="default" selected><?php echo $cuser; ?></option>

											<?php

											if ($sudoer) {
												foreach ($users as $key) {

														//if ($remote['options']['user']==$key) { 
													?>
													<option value="<?php echo $key;?>"><?php echo $key; ?></option>

													<?php	
														//}
												}

												unset($key, $val);
											}

											?>

											<option value="root">root</option>

										</select>
									</p>
								</div>
								<h3>
									<div class="element"><?php echo ST_CMD;?></div>
								</h3>
								<div class="main_input" id="config_input">
									<p>
										<input id="shell_run" name="shell_run" type="text" placeholder="" value="">
									</p>
								</div>
								<p>
									<div class="submit"><button class="submit" name="submit-run" id="submit-run" disabled><?php echo ST_EXECUTE;?></button></div>
								</p>							
							</td>
						</tr>
					</table>						
					<p><a data-popup-close="run" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
				</div>
			</div>

			<!--FAQ-->

			<div class="popup" data-popup="faq">
				<div class="popup-inner faq">
					<h3 id="header_style" class="faq_header">FAQ</h3>
					<br>
					<h3>
						<div id="header_style">How can i view online videos?</div>
					</h3>
					<p>1) Copy and paste the url address to the url input field 2) Type the video title instead, the first youtube matching video will play automatically 3) You can use a bookmarklet (read later)</p>
						<!-- <div id="black">
							<p><a style="color:white;" href="https://rg3.github.io/youtube-dl/download.html">https://rg3.github.io/youtube-dl/download.html</a></p>
						</div>
						<p> Don't try to apt-get install from raspbian repos. It's incompatible, old versions. After you wget and install dont forget to update for some reason. (edit, Jan.'16 youtube-dl is now embed, and auto updating)</p>
						<div id="black">
							<p>which youtube-dl; path-to-youtube-dl/youtube-dl -U</p>
						</div>-->
						<h3>
							<div id="header_style">Why youtube links and live tv delay so much time to connect?</div>
						</h3>
						<p>External youtube-dl app takes some seconds to parse direct links. If history links are expired they have to be generated again.</p>
						<h3>
							<div id="header_style">How can i view hls youtube videos?</div>
						</h3>
						<p>Youtube live streaming uses hls connection type. You can only view hls live streams adding the url address in the webtv section and check the hls option. Don't check this live streaming option for another type of links (direct links). Dailymotion live streams requires this option too.</p>
						<h3>
							<div id="header_style">How can i play videos using this page in my raspbian xorg system?</div>
						</h3>
						<p>You can use this app creating mime type or even from your console, direct, using the following command and arguments</p>
						<div id="black">
							<p>php -f index.php url=</p>
							<p>php -f index.php url=$1</p>
							<p>php -f index.php url=/home/pi/Videos/sample.mp4</p>
							<p>wget "http://localhost/remote/index.php?url=$1" -q -O ~/foo.html --delete-after</p>
						</div>
						<h3>
							<div id="header_style">How can i play online videos directly from the main streaming page in my web browser?</div>
						</h3>
						<p>You can make a bookmark in your desktop or mobile browser, like following, and use it when you open a youtube link for example. this will redirect the link to the remote control page and play automatically</p>
						<div id="black">
							<p>javascript:location.href='http://<?php echo $ip;?>/remote/index.php?url='+encodeURIComponent(location.href)</p>
						</div>
						<h3>
							<div id="header_style">How can i join audio group to modify audio volume level from this app</div>
						</h3>
						<p>You can join audio group executing this command,</p>
						<div id="black">
							<p>sudo usermod -a -G audio <?php echo get_current_user();?></p>
						</div>
						<h3>
							<div id="header_style">Whats the advantages being a sudo member using this app?</div>
						</h3>
						<p>Tvheadend doesnt have an API, needs sudo to import dvb channels. Being a sudo member you can use all the external features like, screen refresh after video ends, black screen while swapping, executing external apps like kodi and others directly from this app, accessing xorgs display being a plain user, shutdown and reboot system. You can grant sudo access adding this text at the end of <b>/etc/sudoers</b> file, remember, with your own risk! Its not essential.</p>
						<div id="black">
							<p><?php echo get_current_user();?> ALL=(ALL) NOPASSWD: ALL</p>
						</div>
						<p>You can sudo only with some applications being member only in sudo group but you have to put no password in %sudo for all those commands. This app doesnt have command input between user and shell.</p>
						<h3>
							<div id="header_style">How can i upgrade omxplayer to the newer version?</div>
						</h3>
						<p>You can download compiled binaries of omxplayer for raspbian os from the following site. Is essential for --live feature which makes playback better.</p>
						<div id="black">
							<p><a style="color:white;" href="http://omxplayer.sconde.net/">http://omxplayer.sconde.net/</a></p>
						</div>
						<h3>
							<div id="header_style">What is the auto blank feature?</div>
						</h3>
						<p>Its an embed idea from raspberry's community, using xscreensaver to auto power off video output if screen blanks. Very usefull scipt with some own adds. You can use it only with sudo because access x-orgs display and must run as default user (sudo -u). Xscreensaver must be installed too.</p>
						<h3>
							<div id="header_style">How can i reset settings?</div>
						</h3>
						<p>You can reset everything, or channels from the general settings. Remember this will remove all settings and backup everything in a "backup" folder is case of fail. You can only restore files manually from this folder later. Don't press these buttons more than one times because you will lose backup.</p>
						<h3>
							<div id="header_style">Why is my file list empty when generating files from some paths and what is file cache?</div>
						</h3>
						<p>Check if you have a remote path defined in list. Remote paths is usefull for raspberry playing files from desktop/laptop machines connected to a wi-fi or local network. If this client is powered-off maybe remote app fails to generate file list. Cache, makes things better for loading page because file list is stored in a file and there is no need to generate new file list every time.</p>
						<h3>
							<div id="header_style">New files are missing from the list when cache is enabled. Why?</div>
						</h3>
						<p>You can even disable cache or press the reload icon at the top-left screen to generate new file list again.</p>
						<h3>
							<div id="header_style">I cant view tvheadend live streams even if tvheadend is installed </div>
						</h3>
						<p>Tvheadend is a dvbtv streamer. Which means you must have a dvb usb device plugged in, compatible with raspbian, It streams via http terrestial mpeg muxes. Sometimes swapping channels make tvheadend become unusable for unknown reason. A stable option for delaying swapping is 3-4 seconds. If you have problem with dvb streaming you can reload tvheadend service or even reboot your system. (edit, some bug delays fixed after hts v4.x)</p>
						<div id="black">
							<p><a style="color:white;" href="http://apt.tvheadend.org/stable/pool/main/t/tvheadend/">http://apt.tvheadend.org/stable/pool/main/t/tvheadend/</a></p>
						</div>
						<h3>
							<div id="header_style">What is the kodi and vnc use of this app</div>
						</h3>
						<p> Nothing special, only redirecting or loading. For example you can load kodi or vnc and automatically redirect to the ios apps if is installed in your iphone. If no app is installed you can download it or redirect to the http webui (for kodi).You can use X11VNC for raspbian to access desktop gui from your phone or your desktop machine using VNC Ultra app.</p>
						<h3>
							<div id="header_style">How can i use subliminal to automatically download subtitles from the internet?</div>
						</h3>
						<p>You can install python's script 'subliminal' using pip, if you have pip installed on your system then skip the first command. Install this script and choose "internet" in Settings - > Localization -> Subtitles. You will then have subtitles automatically downloaded from the internet ...</p>
						<div id="black">
							<p>sudo apt-get install python-pip</p>
							<p>sudo pip install subliminal</p>
						</div>
						<h3>
							<div id="header_style">What is the Ignore EDID (Compatibility) option?</div>
						</h3>
						<p>Check this option if <b>OMXPLayer</b> fails to <i>sychronize</i> resolution input with some monitors when auto resolution is on. If edid fails and option is unchecked maybe you can see black boxes playing sd videos</p>
						<h3>
							<div id="header_style">What is the mdns address?</div>
						</h3>
						<p>If you have avahi service installed you can use mdns adress to access raspberry, from every local network you are connected, with a static dns address. You dont have to find raspberrys ip every time to connect. Default M-DNS: <i>raspberrypi.local</i>. You must have apple's bonjour app in windows to use mdns address</p>
						<br>						
						<p><a data-popup-close="faq" href="#" id="inner_back"><?php echo ST_BACK;?></a></p>
					</form>
				</div>
			</div>

			<div class="main_input url_address">
				
					<input type="text" placeholder=":<?php echo INDEX_URL;?>" style="font-size:18px" id="url_address">
					<div id="clear"></div>
				
			</div>
	
		
				<button class="main bb" id="bb"><img id="bb_img" style="height:30px;width:38px;" src="img/<?=theme?>/left_arrow.png">
				<button class="main play" id="play"><img id="play_img" style="height:22px;width:26px;" src="img/<?=theme?>/play.png">
				<button class="main ff" id="ff"><img id="ff_img" style="height:30px;width:38px;" src="img/<?=theme?>/left_arrow.png"></button>
			<div style="padding-bottom:6;"></div>
			<div id="playtime">00:00:00</div>
			<div style="padding-bottom:0;"></div>
			<div class="wrapper">

				<?php

				$show_history=array_reverse($history);			
				foreach ($show_history as $key=>$val) 
				{
					if ($show_history[$key]['name'] == "") 
					{
						if ( strlen($show_history[$key]['url']) > 45) {
							$caption=mb_substr($show_history[$key]['url'] ,0,45).'...';
						}
						else {
							$caption=$show_history[$key]['url'];
						}
						?>
						<a class="internal history" onclick="ajax('play','<?php echo $show_history[$key]['url'];?>','history');"><?php echo $caption; ?></a>
						<?php
					}
					else 
					{	
						if ( strlen($show_history[$key]['name']) > 45) {
							$caption=mb_substr($show_history[$key]['name'] ,0,45).'...';
						}
						else {
							$caption=$show_history[$key]['name'];
						}
						?>
						<a class="internal history" onclick="ajax('play','<?php echo $show_history[$key]['url'];?>','history');"><?php echo $caption; ?></a>
						<?php
					}

				}
				unset($show_history, $key, $val);
				?>
				
				<a class="internal history" id="clr" onclick="ajax('clear_history');"><?php echo INDEX_CLEAR_HIS; ?></a>

				<?php
				if (!empty($history)) {
					?>
					<script>
						$("#clr").css({"opacity":"1"});
					</script>
					<?php
				}
				?>

			</div>
			<?php
			if (empty($history)) {
				?>
				<script>
					$(".wrapper").hide();
				</script>
				<?php
			}
			?>
			<div style="padding:12;"></div>
			<div id="control"></div>
<!--			<table cellspacing="1" cellpadding="2">
				<tr>
					<td>
						<div class="round-button">
							<div class="round-button-circle extra_font vol voldown">
								<a class="round-button text" onclick=""><?php echo INDEX_VOLUME;?><br>-</a>
							</div>
						</div>
					</td>
					<td>
						<div class="round-button">
							<div class="round-button-circle halt">
								<a <?php if (!$sudoer) { echo 'onclick="ajax(\'halt\',\'\',\'\',\'\',\'1\');"'; } ?>class="round-button" id="halt">
									<img style="height:50px;width:50px;margin-left:0;" src="img/<?=theme?>/power.png" />
									<?php 
									if($sudoer) 
									{
										?>
										<div class="halt_select">
											<select id="sys" onchange="ajax('halt');">
												<option value="" selected disabled></option>
												<option value="halt"><?php echo INDEX_HALT;?></option>
												<option value="reboot"><?php echo INDEX_RE;?></option>
											</select>
										</div>
										<?php 
									} 
									?>
								</a>
							</div>
						</div>
					</td>
					<td>
						<div class="round-button">
							<div class="round-button-circle extra_font vol volup">
								<a class="round-button text" onclick=""><?php echo INDEX_VOLUME;?><br>+</a>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="round-button">
							<div class="round-button-circle img">
								<a class="round-button" onclick="ajax('chapter_');">
									<img style="height:32px;width:32px;margin-left:2;margin-top:8;transform: scaleX(-1);filter: FlipH;" src="img/<?=theme?>/chapter.png" />
								</a>
							</div>
						</div>
					</td>
					<td>
						<div class="round-button">
							<div class="round-button-circle img">
								<a class="round-button" onclick="ajax('pause');">
									<img style="height:48px;width:47px;margin-left:1;" src="img/<?=theme?>/pause.png"/>
								</a>
							</div>
						</div>
					</td>
					<td>
						<div class="round-button">
							<div class="round-button-circle img">
								<a class="round-button" onclick="ajax('chapter');">
									<img style="height:32px;width:32px;margin-left:2;margin-top:8;" src="img/<?=theme?>/chapter.png" />
								</a>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="round-button">
							<div class="round-button-circle img">
								<a class="round-button" onclick="ajax('seek600m');">
									<img style="height:37px;width:37px;margin-left:2;margin-top:5;transform: scaleX(-1);filter: FlipH;" src="img/<?=theme?>/seek600.png" />
								</a>
							</div>
						</div>
					</td>
					<td>
						<div class="round-button">
							<div class="round-button-circle img">
								<a class="round-button" id="stop" onclick="ajax('stop');">
									<img style="height:44px;width:44px;margin-top:2;margin-left:1;" src="img/<?=theme?>/stop.png" />
								</a>
							</div>
						</div>
					</td>
					<td>
						<div class="round-button">
							<div class="round-button-circle img">
								<a class="round-button" onclick="ajax('seek600');">
									<img style="height:37px;width:37px;margin-left:2;margin-top:5;" src="img/<?=theme?>/seek600.png" />
								</a>
							</div>
						</div>
					</td>
				</tr>
			</table> -->

			<div style="padding:14;"></div>

			<?php 
			if (($tvheadend) && (!empty($channels))) {
				?>

				<div class="line-separator tv-line" onclick="ajax('tvheadend');drop(tv);"></div>
				<div id="tv" class="sliding-effect">	
					<div style="margin-top:5;"></div>
				<!--	<div class="backarea">
						<table cellspacing="0" cellpadding="6">
							<tr>
								<?php 
								foreach($channels as $key=>$cap)
								{
									$count++;
									foreach(array_keys($separators['separators']) as $sp)
									{
										if ($sp == $channels[$key]['name']) 
										{ 
											?>
										</tr>
										<tr>
											<td colspan="3">
												<div class="line-separator">
													<br>
												</div>
											</td>
										</tr>
										<tr> 
											<?php 
											break;
										}
									}
									if ($i == "3")
									{ 
										?>
									</tr>
									<tr> 
										<?php 
										$i=0; 
									} 
									?>
									<td>
										<?php 	
										unset($filename);
										$image_files = glob('logo/*.{jpg,png,gif}', GLOB_BRACE);
										foreach ($image_files as $image_file) 
										{
											$file = pathinfo($image_file);
											if ($file['filename']==$channels[$key]['name']) 
											{
												$filename=$file['basename'];
												break;
											}
										}
										if ((isset($filename)) && (icons==true))
										{ 
											foreach(array_keys($dimensions) as $key_d)
											{
												if ($key_d == $filename) 
												{
													$width=$dimensions[$key_d]['width']; 
													$height=$dimensions[$key_d]['height'];
													break; 
												}
											}
											if (!isset($width)) { $width=50; $height=30; 
											} 
											?>
											<div class="button channels dvb">
												
													<button style="" onclick="ajax('play',<?php echo json_encode($key); ?>,'tv');" value=<?php echo json_encode($key.'||'.$channels[$key]['name']); ?>><img style="height:<?php echo $height; ?>px;width:<?php echo $width; ?>px;" src="logo/<?php echo $filename; ?>"></button>
												
											</div>
											<?php 
										} 
										else 
										{ 
											?>
											<div class="button channels dvb">
												
													<button style="" onclick="ajax('play',<?php echo json_encode($key); ?>,'tv');" value=<?php echo json_encode($key.'||'.$channels[$key]['name']); ?>><?php echo $channels[$key]['name'];?></button>
												
											</div> 
											<?php  
										}
										?>
									</td>
									<?php 
									$i++; unset($width);
								}
								unset ($key, $cap, $i, $filename, $key_d, $height, $sp);
								?>
							</tr>
						</table>
					</div>
					<br>
					<?php
					if ($count>30) {
						?>
						<button onclick="location.href='#top';" class="top up_tv"><img id="up_tv" src="img/<?=theme?>/up.png" style="height:40;width:40;margin-top:2;"></img></button>
						<?php 
					}
					unset($count);
					?>


				</div>
				<!-- TV Expansion -->

				<?php 
			}
			?>
			<div id="webtv_tag"></div>
			<div class="line-separator" onclick="ajax('webtv');drop(webtv_set);"></div>

			<div id="webtv_set" class="sliding-effect">		
				<div style="margin-top:5;"></div>
<!--				<div class="backarea">

					<table cellspacing="0" cellpadding="6">
						<tr>
							<?php 

							$last_web_link = key( array_slice( $webtv, -1, 1, TRUE ) );
							foreach(array_keys($webtv) as $cap)
							{ 
								$count++;
								foreach(array_keys($separators['separators']) as $sp)
								{
									if ($sp == $webtv[$cap]['name']) 
									{ 
										?> 
									</tr>
									<tr>
										<td colspan="3">
											<div class="line-separator">
												<br>
											</div>
										</td>
									</tr>
									<tr> 
										<?php 
										break;
									}
								} 
								if ($i == "3")
								{ 
									?>
								</tr>
								<tr> 
									<?php 
									$i=0; 
								} 
								?>
								<td>
									<?php 			
									unset($filename);
									$image_files = glob('logo/*.{jpg,png,gif}', GLOB_BRACE);
									foreach ($image_files as $image_file) 
									{
										$file = pathinfo($image_file);
										if ($file['filename']==$webtv[$cap]['name']) 
										{
											$filename=$file['basename'];
											break;
										}
									}
									if ((isset($filename)) && (icons==true))
									{ 
										foreach(array_keys($dimensions) as $key)
										{
											if ($key == $filename) 
											{
												$width=$dimensions[$key]['width']; 
												$height=$dimensions[$key]['height'];
												break; 
											}
										}
										if (!isset($width)) 
										{ 
											$width=50; 
											$height=30; 
										}
										?>
										<div class="button channels webtv_channels">
											<?php					if ($last_web_link == $cap) 
											{ 
												?>							<div id="lastwebtv"></div>

												<?php			
											} 
											?>
										
											<button style="" onclick="ajax('play',<?php echo json_encode($cap); ?>,'webtv');" value=<?php echo json_encode($cap.'||'.$webtv[$cap]['name']); ?>><img style="height:<?php echo $height; ?>px;width:<?php echo $width; ?>px;" src="logo/<?php echo $filename; ?>"></button>
										</div> 
										<?php 		
									} 
									else 
									{ 
										?>
										<div class="button channels webtv_channels">

											<?php	
											if ($last_web_link == $cap) 
											{ 
												?>									<div id="lastwebtv"></div>

												<?php	
											}
											?>
											
											<button style="" onclick="ajax('play',<?php echo json_encode($cap); ?>,'webtv');" value=<?php echo json_encode($cap.'||'.$webtv[$cap]['name']); ?>><?php echo $webtv[$cap]['name'];?></button>
										</div>
										<?php 	
									}
									?>
								</td>
								<?php 
								$i++; 
								unset($width);
							}
							unset ($key, $cap, $filename, $height, $sp); 
							if ($i=="3") 
							{
								?>
							</tr>
							<tr>
								<?php 
							} 
							?>
							<td>
								<div class="button">
									<button data-popup-open="webtv" onclick="" id="addnewlink" value=""><img style="height:28px;width:28px;" src="img/<?=theme?>/add.png"></button>
								</div> 
							</td>
							<?php 
							unset($i);
							?>
						</tr>
					</table>
				</div> -->
				<br>
				<?php
				if ($count>30) {
					?>
					<button onclick="location.href='#top';" class="top up_webtv"><img id="up_webtv" src="img/<?=theme?>/up.png" style="height:40;width:40;margin-top:2;"></img></button>
					<?php 
				}
				unset($count);
				?>

			</div>

			<!-- WEBTV Expansion -->

			<div style="padding:2;"></div>


			<div id="extra"></div>
		<!--	<div class="backarea">
				<table cellspacing="0" cellpadding="6" class="extra_font button">
					<tr>
						<td>
						<button style="" onclick="ajax('chapter_');"><?php echo INDEX_PCHAP;?></button>
						</td>
						<td>
						<button style="" onclick="ajax('speed_');"><?php echo INDEX_SPEED;?><br>-</button>
						</td>
						<td>
						<button style="" onclick="ajax('chapter');"><?php echo INDEX_NCHAP;?></button>
						</td>
					</tr>
					<tr>
						<td>
						<button style="" onclick="ajax('audio_');"><?php echo INDEX_PSTREAM;?></button>
						</td>
						<td>
						<button style="" onclick="ajax('speed');"><?php echo INDEX_SPEED;?><br>+</button>
						</td>
						<td>
						<button style="" onclick="ajax('audio');"><?php echo INDEX_NSTREAM;?></button>
						</td>
					</tr>
					<tr>
						<td>
						<button style="" onclick="ajax('subs_');"><?php echo INDEX_PSUBS;?></button>
						</td>
						<td>
						<button style="" onclick="ajax('tsubs');"><?php echo INDEX_TSUBS;?></button>
						</td>
						<td>
						<button style="" onclick="ajax('subs');"><?php echo INDEX_NSUBS;?></button>
						</td>
					</tr>
					<tr>
						<td>
						<button style="" onclick="ajax('subsd_');"><?php echo INDEX_SDELAY;?><br>-</button>
						</td>
						<td>
						</td>
						<td>
						<button style="" onclick="ajax('subsd');"><?php echo INDEX_SDELAY;?><br>+</button>
						</td>
					</tr>
					<tr>
						<td>
						<button style="" onclick="ajax('ssaver');"><?php echo INDEX_SSAVER;?></button>
						</td>
						<td>
							<a onclick="ajax('audio_out');" class="button">
								<p><input id="audio_out" type="image" style="height:20px;width:60px;" src="img/<?=theme?>/<?php if ($remote['omxplayer']['audio_out'] == "both"){ echo "both.png";} elseif ($remote['omxplayer']['audio_out'] == "hdmi"){ echo "hdmi.png";} elseif ($remote['omxplayer']['audio_out'] == "local"){ echo "rca.png"; } ?>" /></p>
							</a>
						</td>
						<td>
						<button style="" onclick="ajax('switch_hdmi');"><?php echo INDEX_SHDMI;?></button>
						</td>
					</tr>
					<tr>
						<td>
							<a id="source_res" onclick="alert(resolution);ajax('source_res');"></a>
							<?php
							if ($remote['omxplayer']['source_res'] == "disabled"){ 
								?>
								<script>
									$('<p>',{
										text: index_arese,
									}).appendTo('#source_res');
								</script>
								<?php
							} 
							else { 
								?>
								<script>
									$('<p>',{
										text: index_aresd,
									}).appendTo('#source_res');
								</script>
								<?php
							} ?>

						</td>
						<td>
							<a id="audio_lay" onclick="ajax('audio_lay');"></a>
							<?php
							if ($remote['omxplayer']['audio_lay'] == "none"){ 
								?>
								<script>
									$('<p>',{
										text: index_lay0,
									}).appendTo('#audio_lay');
								</script>
								<?php
							} 
							elseif ($remote['omxplayer']['audio_lay'] == "51"){ 
								?>
								<script>
									$('<p>',{
										text: index_lay51,
									}).appendTo('#audio_lay');
								</script>
								<?php
							}
							elseif ($remote['omxplayer']['audio_lay'] == "21"){ 
								?>
								<script>
									$('<p>',{
										text: index_lay21,
									}).appendTo('#audio_lay');
								</script>
								<?php
							}
							?>							
						</td>
						<td>
							<a id="passthrough" onclick="ajax('passthrough');"></a>
							<?php
							if ($remote['omxplayer']['passthrough'] == "disabled"){ 
								?>
								<script>
									$('<p>',{
										text: index_passe,
									}).appendTo('#passthrough');
								</script>
								<?php
							} 
							else { 
								?>
								<script>
									$('<p>',{
										text: index_passd,
									}).appendTo('#passthrough');
								</script>
								<?php
							} ?>
						</td>
					</tr>
				</table>
			</div>  -->
			<br>
			<button onclick="location.href='#top';" class="top up_cc"><img id="up_cc" src="img/<?=theme?>/up.png" style="height:40;width:40;margin-top:2;"></img></button>
			<?php 
				if ((theme!='terminal') && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') === false)) {
			?>
				<p>
					<a class="switchfont" onclick="window.open('index.php?font=','_self');" style="font-size:smaller;"><u>Switch font</u></a>
				</p>
			<?php 
				}
				else {
			?>
					<br>
			<?php
				}
			?>
			<br>
			<div class="backarea apptitle" style="word-warp:break-word;width:100%; max-width:99%; box-shadow:initial;">
				<p style=""><b>Raspberry Remote</b></p>
			</div>
			<div id="end"></div>
			<p><img style="height:45px;width:35px;" src="img/<?=theme?>/settings.png" /></p>
			<br>		
		</div>
	</center>
	<style>
		.navigate {
			display:none;
		}
		.navigate img {
			width:30px;
			height:30px;
		}
	</style>
	<div class="marquee">
		<!--<div class="navigate"></div>-->
		<div class="track">
			<input id="timeline" type="range" precision="float" min="0" max="1" value="1" step="any" <?php if (!file_exists(OUT_LOG)) { echo "disabled";}?>>
		
		<div class="minwidth">
		<div style="float:left;" id="console"></div>
		<div style="float:right;" id="time"></div>
		</div>

		</div>
		
	</div>

	<script charset="utf-8" src="functions.js"></script>

	<?php
	if (isset($_GET['generate']))
	{
		if (empty($vids))
		{
			$report=OUT_NO_FILES.' ...';
		}
		else 
		{
			if (isset($is_not_dir))
			{
				$report=OUT_ERROR_DIR.' ...';
			}
			else 
			{
				$report=OUT_CACHE_OK.' ...';
			}
		}	
		unset($vids);
	}
	if (isset($_GET['kodi'])){
		$report=OUT_KODI_LOAD.' ...';
	}
	if (isset($_GET['update'])){
		$report=OUT_UPDATE.' ...';
	?>
		<script>
			alert(git_update);
		</script>
	<?php
	}	
	?>

	<?php
	if (isset($_GET['url']))
	{ 
		if (!file_exists($_GET['url'])) {
			?>
			<script>
				ajax('play',<?php echo json_encode($_GET['url']); ?>,'ext');
			</script>
			<?php 
		} 
		else {
			play($_GET['url'],'file');
		}
	}

	?>

	<?php
	if (!empty($remote['options']['tvheadend'])) {
		?>
		<script>
			drop(tv);
		</script>	
		<?php
	}
	if (!empty($remote['options']['webtv'])) {
		?>
		<script>
			drop(webtv_set);
		</script>
		<?php
	}

	if (isset($_POST['submit-webtv'])){
		?>

		<script>
			location.hash = "#lastwebtv";
		</script>

		<?php	
	}

	?>
	<?php  
	
	//CASE OMXPLAYER IS RUNNING
	//SESSION CHECK

	if (!empty($pid=shell_exec('pgrep omxplayer'))) {
		if ($remote['options']['client']!=$_SERVER['REMOTE_PORT']) {
	?>
		<script>
			if (confirm(<?php echo json_encode(OUT_SESS_ALERT.'.');?>)) {
				delay[2]=true;
				document.getElementById('timeline').disabled = true;
			}
		</script>
	<?php	
		$report=OUT_SESSION.' ...';
		}

		$pos=$remote['omxplayer']['media'];
		if (file_exists($pos)) {
			$type='file';
			$found=true;
		}
		else {
			$last=end($history);
			$last=array_search($last,$history);
			$last_pos=$history[$last]['url'];
			if ($last_pos==$pos) {
				$out[7]=$history[$last]['name'];
				$type='url';
				$found=true;
			}
			else {

				foreach ($webtv as $key => $val) {
					if ($webtv[$key]['url']==$pos) {
						$out[7]=$webtv[$key]['name'];
						$type='webtv';
						$pos=$key;
						$found=true;							
						break;

					}
				}

				foreach ($channels as $key => $val) {
					if ($found) {
						break;
					}
					$tv='http://'.$remote['tvheadend']['username'].':'.$remote['tvheadend']['password'].'@'.$remote['tvheadend']['path'].':'.$remote['tvheadend']['port'].$remote['tvheadend']['url'].$channels[$key]['index'];
					if ($tv==$pos) {
						$out[7]=$channels[$key]['name'];
						$pos=$key;
						$type='tv';
						$found=true;
						break;
					}
				}

			}
		}

		if (!$found) {
			$type='url';
			$out[7]='N/A';
		}

		$out[4]=0;
		$out[5]=0;
		$out[0]=OUT_SYNC;
		$out[3]=OUT_SYNC.' -';
		?>
		<script>

			pos=<?php echo json_encode($pos);?>;
			type=<?php echo json_encode($type);?>;
			out=<?php echo json_encode($out);?>;

		</script>
		<?php

	}
	else {
		?>
		<script>
			ajax('maintain');
		</script>
		<?php
	}
	?>

	<script charset="utf-8">

		document.getElementById('console').textContent=<?php if (isset($report)) { echo json_encode($report); } else { echo json_encode(OUT_WELCOME); } ?>;
		if (delay[2]!==true) {
			window.setInterval(function(){ ajax('out');}, 1000);
			timer=true;
			counter=1;
		}

	</script>
	
	<?php

	$vars = array_keys(get_defined_vars());
	for ($i = 0; $i < sizeOf($vars); $i++) {
		unset($$vars[$i]);
	}
	unset($vars,$i);

	?>
</body>
</html>
