<?php

if (file_exists('remote.cfg')){
	$remote=parse_ini_file('remote.cfg',true);
}
else {

	if (file_exists("settings.ini")) {
		$settings=parse_ini_file("settings.ini",true);
		$remote=$settings;
		unset($settings);
		unlink('settings.ini');
		write_ini_file('remote.cfg',$remote);
	}

	if (file_exists("omxplayer.ini")) {
		$omxplayer=parse_ini_file("omxplayer.ini",true);
		if (isset($omxplayer['options'])) {
			$remote['omxplayer']=$omxplayer['options'];
			$remote['omxplayer']['font']="css/NotoSans-Regular.ttf";
			$remote['omxplayer']['source_res']="disabled";
			$remote['omxplayer']['font_size']="66";
			unset($omxplayer);
			unlink('omxplayer.ini');
			write_ini_file('remote.cfg',$remote);
		}
	}

}
if (file_exists("channels.ini")){
	$channels=parse_ini_file("channels.ini",true);
}
if (file_exists("webtv.ini")){
	$webtv=parse_ini_file("webtv.ini",true);
}
if (file_exists("logo.ini")){
	$dimensions=parse_ini_file("logo.ini",true);
}

if ( !is_writable(getcwd()) ) {
	if (!chmod(getcwd(),PERM) ) {
		?>
		<script>
			window.location="report.php?report=1";		
		</script>
		<?php
		die(); 
	}
}

$apache_user = posix_getpwuid(posix_geteuid());

exec('which omxplayer',$status);

if (empty($status[0])) {

	?>
	<script>

		window.location="report.php?report=2";		

	</script>
	<?php		

	die();
}

unset($status);

if (!is_writable('/dev/vchiq') ) { 
				#/dev/fb0
	?>
	<script>
		window.location="report.php?report=3";		

	</script>
	<?php

	die();
}

if ( !is_executable(getcwd().'/remote') ) {
	if (!chmod(getcwd().'/remote',PERM) ) {
		?>
		<script>
			window.location="report.php?report=4";		

		</script>
		<?php

		die();
	}
}

if ( !is_executable(getcwd().'/monitor') ) {
	chmod(getcwd().'/monitor',PERM);
}

if ( !is_executable(getcwd().'/kodi') ) {
	chmod(getcwd().'/kodi',PERM);
}

exec ('who -q | head -n 1',$users); $users=explode(' ',$users[0]); $users=array_unique($users);

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

if (((empty($remote['options']['user'])) || (!isset($remote['options']['user']))) && is_array($users) && !empty($users)) {
	$remote['options']['user']=reset($users);
	write_ini_file('remote.cfg',$remote);
}

unset($key, $val);

exec ('locale -a | grep utf',$enc); $enc=array_unique($enc); 
foreach ($enc as $key => $val){
	if ((strpos($val,'en')) && (!end($enc))){
		array_push($enc, $val);
		unset($enc[$key]);
	}
}

if (((empty($remote['options']['locale'])) || (!isset($remote['options']['locale']))) && is_array($enc) && !empty($enc)) {
	$remote['options']['locale']=reset($enc);
	$remote['omxplayer']['lang']=mb_substr(reset($enc), 0, 2);
	write_ini_file('remote.cfg',$remote);
}
if (!isset($remote['options']['display'])){
	$lang_files = glob('*.{lng}', GLOB_BRACE);
	if ((!empty($lang_files)) && (!empty($enc))){
		foreach ($lang_files as $lang_file) {
			$file = pathinfo($lang_file);
			$external=parse_ini_file($lang_file,true);
			if (($file['filename']==mb_substr(reset($enc), 0, 2)) && ($lang_file['external']['lang']==mb_substr(reset($enc), 0, 2))){
				$remote['options']['display']=$lang_file['external']['lang'];
				$found=true;
				unset($file,$external);
				break;
			}
			if ((end($lang_files)) && (!$found)) {
				if (array_key_exists(mb_substr(reset($enc), 0, 2),$lang['lang'])) {
					$remote['options']['display']=mb_substr(reset($enc), 0, 2);
				}
				else {
					$remote['options']['display']="en";
				}
				unset($file);
				break;
			}

		}
	}
	else {
		if (array_key_exists(mb_substr(reset($enc), 0, 2),$lang['lang'])) {
			$remote['options']['display']=mb_substr(reset($enc), 0, 2);
		}
		else {
			$remote['options']['display']="en";
		}
	}

	unset($lang_file,$lang_files);
	if ($remote['options']['display']=="el") {
		copy('logo/greek/webtv.ini',getcwd());
		copy('logo/greek/logo.ini',getcwd());
	}
	write_ini_file('remote.cfg',$remote);
	unset($postpone);
}

if (!isset($remote['options']['theme'])) {
	$remote['options']['theme']="raspberry";
	write_ini_file('remote.cfg',$remote);
}

require 'define.php';

unset($key, $val);

if (!isset($remote['omxplayer']['subtitles'])){
	$remote['omxplayer']['subtitles']="local";
	write_ini_file(REMOTE,$remote);
}

if (!file_exists('paths')){

	$cmd='echo ~'.$remote['options']['user'];
	exec($cmd,$path);
	if (is_dir($path[0])){
		$home_path=$path[0].'/Videos';
		if (!is_dir($home_path)){
			$home_path=$path[0];
		}
	}
	$generate_paths = getcwd().PHP_EOL.$home_path;
	file_put_contents('paths', $generate_paths);
	unset($apache_user);
}

if ((!isset($remote['alsa']['card'])) || (!isset($remote['alsa']['control']))) {
	exec('amixer sget PCM',$result,$error);
	if (!$error) {
		$control='PCM';
	}
	else {
		exec('amixer sget Master',$result,$error);
		if (!$error) {
			$control='PCM'; // Ignore Master
		}
	}
	if (isset($control)) {
		$cmd='amixer sget '.$control.' | head -n 1';
		exec($cmd,$result,$error);
		if (!$error){
			$card=$result[0];
			$card=filter_var($result[0], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	if (isset($card)) {
		$remote['alsa']['card']=$card;
		$remote['alsa']['control']=$control;
		write_ini_file(REMOTE,$remote);
	}
	unset($control,$card,$result,$error,$cmd);
}

exec('pgrep tvheadend',$error);
if (empty($error)){
	if (!empty($remote['options']['tvheadend'])){
		$remote['options']['tvheadend']="";
		write_ini_file(REMOTE,$remote);
	}
}
else {
	if (!isset($remote['options']['tvheadend'])){
		$remote['options']['tvheadend']="1";
		write_ini_file(REMOTE,$remote);
	}
	$tvheadend=true;
}
unset ($error);

if (!isset($remote['options']['webtv'])) {
	$remote['options']['webtv']="1";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['options']['font'])) {
	$remote['options']['font']="NotoSans";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['options']['cache'])) {
	$remote['options']['cache']="1";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['options']['autoupdate'])) {
	$remote['options']['autoupdate']="1";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['tvheadend']['url'])) {
	$remote['tvheadend']['url']="/stream/channel/";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['tvheadend']['port'])) {
	$remote['tvheadend']['port']="9981";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['tvheadend']['username'])) {
	$remote['tvheadend']['username']="";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['tvheadend']['password'])) {
	$remote['tvheadend']['password']="";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['tvheadend']['path'])) {
	$remote['tvheadend']['path']="localhost";
	write_ini_file(REMOTE,$remote);
}
if (!isset($remote['tvheadend']['conf'])) {
	$remote['tvheadend']['conf']=shell_exec('awk -F: -v v="hts" \'{if ($1==v) print $6}\' /etc/passwd');
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['passthrough'])) {
	$remote['omxplayer']['passthrough']="disabled";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['source_res'])) {
	$remote['omxplayer']['source_res']="disabled";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['audio_out'])) {
	$remote['omxplayer']['audio_out']="hdmi";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['audio_lay'])) {
	$remote['omxplayer']['audio_lay']="none";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['tv_sd'])) {
	$remote['omxplayer']['tv_sd']="0,0,719,575";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['tv_hdr'])) {
	$remote['omxplayer']['tv_hdr']="0,0,1279,719";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['tv_hd'])) {
	$remote['omxplayer']['tv_hd']="0,0,1919,1079";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['align_sub'])) {
	$remote['omxplayer']['align_sub']="center";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['font_size'])) {
	$remote['omxplayer']['font_size']="66";
	write_ini_file(REMOTE,$remote);
}

if (!isset($remote['omxplayer']['font'])) {
	if (file_exists('css/Comfortaa-Regular.ttf')) {
		$remote['omxplayer']['font']="css/NotoSans-Regular.ttf";
		write_ini_file(REMOTE,$remote);
	}
}

if (!isset($remote['omxplayer']['compatible'])) {
	$remote['omxplayer']['compatible']="yes";
	write_ini_file(REMOTE,$remote);
}

if ((empty($remote['options']['console'])) || (empty($remote['options']['nextconsole']))) {

			#exec('sudo fgconsole',$status); 
	$remote['options']['console']="7";
			#$status=exec('sudo fgconsole -n',$status);
	$remote['options']['nextconsole']="8";
	write_ini_file(REMOTE,$remote);
	unset($status);
}

exec('which xscreensaver',$status);

if ((!empty($status[0])) && (empty($remote['options']['xscreensaver']))){ 
	$remote['options']['xscreensaver']=$status[0];
	write_ini_file(REMOTE,$remote);
}
elseif ((empty($status[0])) && (!empty($remote['options']['xscreensaver']))) {
	$remote['options']['xscreensaver']="";
	write_ini_file(REMOTE,$remote);
}
unset($status);

exec('which tvservice',$status);
if ((!empty($status[0])) && (empty($remote['options']['tvservice']))){ 
	$remote['options']['tvservice']=$status[0];
	write_ini_file(REMOTE,$remote);
}
elseif ((empty($status[0])) && (!empty($remote['options']['tvservice'])))	{
	$remote['options']['tvservice']="";
	write_ini_file(REMOTE,$remote);
}
unset($status);

exec('which subliminal',$status);
if ((!empty($status[0])) && (empty($remote['options']['subliminal']))){ 
	$remote['options']['subliminal']=$status[0];
	write_ini_file(REMOTE,$remote);
	
}
elseif ((empty($status[0])) && (!empty($remote['options']['subliminal']))){
	$remote['options']['subliminal']="";
	write_ini_file(REMOTE,$remote);
	
}
unset($status);

exec('which recode',$status);
if ((!empty($status[0])) && (empty($remote['options']['recode']))){ 
	$remote['options']['recode']=$status[0]; 
	write_ini_file(REMOTE,$remote);
	
}
elseif ((empty($status[0])) && (!empty($remote['options']['recode'])))
{
	$remote['options']['recode']="";$remote['omxplayer']['recode']="";
	write_ini_file(REMOTE,$remote);
	
}
unset($status);

if ($tvheadend){

	if ($sudoer) {
		create_channels();
	}
}

if (!file_exists('webtv.ini')){
	if ($remote['options']['display']=="el") {
		if ((!copy('logo/greek/webtv.ini',getcwd().'/webtv.ini')) || (!copy('logo/greek/logo.ini',getcwd().'/logo.ini'))) {
			create_web_channels();
		}
	}
	else {
		create_web_channels();
	}
}
if (!file_exists(FILES)){
	cache_files();
}

?>
