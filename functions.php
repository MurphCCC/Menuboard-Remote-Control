<?php

function history_write($url) {
	
	GLOBAL $remote;

	if (file_exists('history.ini')) {

		$history=parse_ini_file("history.ini",true);	
		$last=end($history);
		$last=array_search($last,$history);

	}
	else {

		$history=array();
		$last=0;
	}

	$local=array('localhost','.local','10.0.0','192.168','127.0.0','169.254','172.16','224.0.0');

	foreach ($local as $path) {
		if (stristr($url,$path)) {
			$islocal=true;
			break;
		}
	}

	unset ($local);

	switch (true) {
		
		case (!empty($url)):

		foreach ($history as $key => $val) {

			if (($history[$key]['url']==$url) && ($key!=$last)){

				$last=$last+1;
				$history[$last]['name']=$history[$key]['name'];
				$history[$last]['url']=$history[$key]['url'];
				if (isset($history[$key]['direct'])){
					$history[$last]['direct']=$history[$key]['direct'];
				}
				unset($history[$key]);
				write_ini_file('history.ini',$history);
				break 2;
			}
		}

		if (($history[$last]['url']!=$url) && (file_exists(getcwd().'/youtube-dl')) && (!$islocal)) {

			
			touch(YT);

			mb_internal_encoding('UTF-8');
			setlocale(LC_ALL, LOCALE);
			putenv('LC_ALL='.LOCALE);

			if (strpos($url, 'http') !== false) {
				$cmd=getcwd().'/youtube-dl -ge --playlist-end 1 -f best '.escapeshellarg($url);
			}
			else {
				$cmd=getcwd().'/youtube-dl -ge --playlist-end 1 -f best '.'https://www.youtube.com/results?search_query='.urlencode($url).'&page=1';

			}	
			exec($cmd,$out,$error); 

			if (!$error) {
				foreach ($history as $key => $val){
					if ($history[$key]['name']==$out[0]){
						$last=$last+1;
						$history[$last]['name']=$history[$key]['name'];
						$history[$last]['url']=$out[1];
						if (isset($history[$key]['direct'])){
							$history[$last]['direct']=$url;
						}
						unset($history[$key]);
						write_ini_file('history.ini',$history);
						$url=$history[$last]['url'];
						break 2;
					}	
				}
				$last=$last+1;
				if (stristr($out[1],$out[0])) {		       	
					$name=$out[1];
				}
				else {
					$name=$out[0];
					$history[$last]['direct']=$url;
				}
				$history[$last]['name']=$name;
				$history[$last]['url']=$out[1];
				write_ini_file('history.ini',$history);
				$url=$history[$last]['url'];
			}
			else {
				unlink(YT);
				shell_exec(getcwd().'/youtube-dl -U');

			}
		}

		elseif (($history[$last]['url']!=$url) && ((!file_exists(getcwd().'/youtube-dl')) || ($islocal)))  {
			$last=$last+1;
			$history[$last]['name']=$url;
			$history[$last]['url']=$url;
			write_ini_file('history.ini',$history);
		}

		break;

	}
	return array($url, $history[$last]['name'], $error);
}

function play($file,$type) {

	GLOBAL $remote;

	if ((!isset($remote)) || (empty($remote))) {
		$remote=parse_ini_file(REMOTE,true);
	}

	GLOBAL $command;
	GLOBAL $sudoer;

	$get_direct=$error=false;

	if ($command!="seekto") {

		exec('pgrep omxplayer', $pid);

		while (!empty($pid)) {
			if (!file_exists(PIPE)) {
				posix_mkfifo(PIPE, PERM);
			}
			$pipe = fopen(PIPE, 'w') ;			
			stream_set_blocking($pipe, 0);
			fwrite($pipe, 'q');
			usleep(200000);
			fclose($pipe);
			if ($sudoer){
				if (empty($pid=shell_exec('pgrep omxplayer'))) {
					sleep(1);
					shell_exec('sudo pkill omxplayer');
				}
			}
			exec('pgrep omxplayer', $pid);
		}
	}
	if (file_exists(PIPE)) {
		unlink(PIPE);
	}
	if (!file_exists(PIPE)) {
		posix_mkfifo(PIPE, PERM);
	}

	if ($command!="seekto") {
		if ($type=="tv"){
			$channels=parse_ini_file("channels.ini",true);
			$name=$channels[$file]['name'];
			$pos=$file;
			$file='http://'.$remote['tvheadend']['username'].':'.$remote['tvheadend']['password'].'@'.$remote['tvheadend']['path'].':'.$remote['tvheadend']['port'].$remote['tvheadend']['url'].$channels[$file]['index'];

		}
		elseif ($type=="webtv"){
			$webtv=parse_ini_file("webtv.ini",true);
			$name=$webtv[$file]['name'];
			if (isset($webtv[$file]['hls'])){
				$get_direct=true;
			}
			$pos=$file;
			$file=$webtv[$file]['url'];


		}
		elseif ($type=="file"){
			$name=$file;
			$pos=$file;

		}
		elseif ($type=="url"){

			$pos=$file;

		}

		if (($type=="url") || ($get_direct)) {
			list($file, $name, $error) = history_write($file);
		}

		if ($remote['omxplayer']['media']!=$file) {
			if ($type=="file") {
				$remote['omxplayer']['pos']='1';
			}
			else {
				$remote['omxplayer']['pos']='0';
			}
			$remote['omxplayer']['media']=$file;
		}
		elseif ($type=="tv") {
			$remote['omxplayer']['pos']='0';		
		}
		elseif ((!isset($remote['omxplayer']['pos'])) || (empty($remote['omxplayer']['pos']))) {
			$remote['omxplayer']['pos']='0';
		}
		unset($get_direct);	
		GLOBAL $session;
		$remote['options']['client']=$session;
		write_ini_file(REMOTE,$remote);	
	}

	if (($type=="file") && ($command!="seekto")){

		if (file_exists($file)){
			$exists=true;
			
			exec("stat -c %s ".escapeshellarg($file),$filesize);
			
			if ($filesize[0]>MIN_SIZE) {

				$parse_srt=pathinfo($file);
				$srt=$parse_srt['dirname'].'/'.$parse_srt['filename'].'.srt';
				if (file_exists($srt)) {

					static $enclist = array( 
						'ASCII', 
						'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 
						'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 
						'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16', 
						'Windows-1251', 'Windows-1252', 'Windows-1254', '8bit' 
						);

					exec('file -bi '.escapeshellarg($srt),$enc);
					if (!stristr($enc[0],'utf-8')){
						foreach ($enclist as $val) {
							if (stristr($enc[0],$val)){
								if ($remote['omxplayer']['lang']=="el"){
									shell_exec('sudo recode -f ISO-8859-7..utf-8 '.escapeshellarg($srt));
								}
							}
						}
					}
				}

				if (!file_exists($srt)) {
					if ((!empty($remote['options']['subliminal'])) && ($remote['omxplayer']['subtitles']=="internet") && (is_dir(getcwd().'/subs')) &&(!file_exists(getcwd().'/subs/'.$parse_srt['filename'].'.srt'))) {
						$cmd='subliminal download -l '.$remote['omxplayer']['lang'].' -e UTF-8 -s -d '.getcwd().'/subs '.escapeshellarg($file);

						shell_exec($cmd);

						if (file_exists(getcwd().'/subs/'.$parse_srt['filename'].'.srt')) {
							$subminimal= array (
								'0' => true,
								'1' => true
								);
						}
						else {
							$subminimal= array (
								'0' => true,
								'1' => false
								);						
						}
					}
				}
			}		
		}
	}


	if ((((!$error) && ($type!="file")) || ($exists)) && ($command!="seekto")){

		if (($command!="prev") && ($command!="next")) {
			//screenref('blank');
		}

		$cmd=getcwd().'/remote '.escapeshellarg($file);
		shell_exec($cmd);
		if (file_exists(YT)) {
			unlink(YT);
		}

		if ($type=="file") {
			$remote['options']['last']=$file;
			if (!$subminimal[0]) {
				$out = OUT_PREPARE.' "'.basename($file).'" ';
			}
			else {
				if ($subminimal[1]==true) {
					$out = OUT_SUBS_TRUE.' ...';
				}
				else {
					$out = OUT_SUBS_FALSE.' ...';
				}
				if (isset($subminimal)) {
					unset($subminimal);
				}
			}
		}
		else {
			$remote['options']['last']=$name;
			$out= OUT_CONNECTING.' "'.$name.'" ';
		}
		write_ini_file(REMOTE,$remote);

		if ($type=="url") {
			$pos=$file;
		}

	}

	if ($command=="seekto") {

		$cmd=getcwd().'/remote';
		shell_exec($cmd);	
		$out=OUT_SEEK.' ...';	
	}

	elseif((!$error) && (!$exists) && ($type=="file"))	{

		if (empty($file)) {
			$out = OUT_SELECT_FILE.' ... ';
		}
		else {
			$out = OUT_FILE_N_F.' "'.$file.'". '.OUT_GENERATE_PATHS.' ... ';
		}				
	}
	elseif($error) {
		$name="";				
		$out=OUT_ERROR_YT.' ... ';
	}

	return array ($out, $pos, $type, $name);
}

function call($cmd,$arg) {

	GLOBAL $remote;
	if (!isset($remote)) {
		$remote=parse_ini_file(REMOTE,true);
	}
	GLOBAL $command;
	GLOBAL $sudoer;

	if (!empty($pid=shell_exec('pgrep omxplayer'))) {

		if (!file_exists(PIPE)) {
			posix_mkfifo(PIPE, PERM);
			chmod(PIPE, PERM);
		}

		$pipe = fopen(PIPE, 'w');
		stream_set_blocking($pipe, 0);
		fwrite($pipe, $cmd);
		usleep(200000);
		fclose($pipe);

		switch ($cmd){

			case 'q':
			if ($command=="seekto") {
				$out=OUT_SEEK.' ...';
				break;
			}
			$pid=shell_exec('pgrep omxplayer');
			while (!empty($pid)) {
				if (!file_exists(PIPE)) {
					posix_mkfifo(PIPE, PERM);
				}
				$pipe = fopen(PIPE, 'w') ;			
				stream_set_blocking($pipe, 0);
				fwrite($pipe, 'q');
				fclose($pipe);
				if ($sudoer){	
					if (!empty($pid=shell_exec('pgrep omxplayer'))) {
						sleep(1);
						shell_exec('sudo pkill omxplayer');
					}
				}
				unset($pid);
				$pid=shell_exec('pgrep omxplayer');
			}
			unlink(PIPE);
			$out = OUT_STOPPED;
			break;
			case 'pause':
			$out = OUT_PAUSED;
			break;
			//case pack('n',0x5b44):
			//case pack('n',0x5b43):
			case pack('n',0x5b42):
			case pack('n',0x5b41):
			case 'z':
			case 'd':
			case 'f':
			case '+':
			case '-':
			case '1':
			case '2':

			$file = fopen(OUT_LOG, 'r+');
			$output=fgets($file);
			fclose($file);

			if (preg_match('/Seek to:\s*([0-9:]+)\s*$/', $output, $match)) {
				$out=str_replace("Seek to:",OUT_SEEKTO.' ',$match[0]);
			}
			elseif (preg_match('/Playspeed\s*([0-9.]+)\s*$/', $output, $match)) {
				$out=str_replace("Playspeed",OUT_PLAYSPEED,$match[0]);
			}
			elseif (preg_match('/Current Volume:\s*([0-9a-zA-Z.]+)\s*$/', $output, $match)) {
				$out=str_replace("Current Volume:",OUT_VOLUME,$match[0]);
			}			
			else {
				$out=OUT_REQUESTING;
			}

			break;
			case 'k':
			case 's':
			case 'm':
			case 'j':
			case 'n':
			case 'i':
			case 'o':
			$out=OUT_REQUESTING;
			break;
		} 
	}
	elseif ($cmd=='q') {
		unlink(PIPE);
		unlink(OUT_LOG);
		unlink(YT);
		unlink(LENGTH);
		unlink(INFO);
		unlink(RES);
		unlink(SIZE);
		$out=OUT_SCREEN_REF.' ...';
	}
	else {
		$out=OUT_NTH;		
	}

	return $out;
}

function read_time($secs) {

	$secs=intval($secs);
	$hours=floor($secs / 3600);
	$minutes=floor(($secs - ($hours * 3600)) / 60);
	$seconds=$secs - ($hours * 3600) - ($minutes * 60 );
	if ($hours < 10) {
		$hours="0".$hours;
	}
	if ($minutes <10) {
		$minutes = "0".$minutes;
	}
	if ($seconds < 10) {
		$seconds = "0".$seconds;
	}
	return $hours.':'.$minutes.':'.$seconds;	

}

function read_files() {

	GLOBAL $vids;
	$vids = preg_split('/\n|\r\n?/', file_get_contents(FILES));
	foreach($vids as $key => $dat)
	{
		if(empty($dat))
		{
			unset($vids[$key]);
		}
	}
}

function cache_files() {

	$files = array();
	$directories = array();
	$dirs = preg_split('/\n|\r\n?/', file_get_contents('paths'));

	foreach ($dirs as $pth){
		if (empty($pth)) {
			continue;
		}
		$directories[]=$pth;	
	}
	foreach ($directories as $pth) {
		foreach(glob($pth.'/*/*/*.{[mM][kK][vV],[mM][pP][4],[mM][oO][vV],[aV][vV][iI]}', GLOB_BRACE) as $file) {
			$files[]=$file;

		}
		foreach(glob($pth.'/*/*.{[mM][kK][vV],[mM][pP][4],[mM][oO][vV],[aV][vV][iI]}', GLOB_BRACE) as $file) {
			$files[]=$file;

		}
		foreach(glob($pth.'/*.{[mM][kK][vV],[mM][pP][4],[mM][oO][vV],[aV][vV][iI]}', GLOB_BRACE) as $file) {
			$files[]=$file;
		}

	}
	GLOBAL $vids;
	$vids=$files;
	$cache = implode("\n", $files);
	file_put_contents('files', $cache);

}

function checkurl($this){
	$headers = get_headers($this->_value);
	if(strpos($headers[0],'200')===false)return false;
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
function write_ini_file($file, array $options){

	$tmp='';

	foreach($options as $section => $values){
		$tmp .= "[$section]\n";
		foreach($values as $key => $val){
			if(is_array($val)){
				foreach($val as $k =>$v){
					$tmp .= "{$key}[$k] = \"".trim($v)."\"\n";
				}
			}
			else
				$tmp .= "$key = \"".trim($val)."\"\n";
		}
		$tmp .= "\n";
	}
	file_put_contents($file, $tmp);
	unset($tmp);
}

function var_ini(array $options){

	foreach($options as $section => $values){
		$tmp .= "[$section]\n";
		foreach($values as $key => $val){
			if(is_array($val)){
				foreach($val as $k =>$v){
					$tmp .= "{$key}[$k] = \"$v\"\n";
				}
			}
			else {
				$tmp .= "$key = \"$val\"\n";
			}
			$tmp .= "\n";
		}
		return $tmp;
	}
}

function swap($opt) {

	GLOBAL $remote;

	if ((!isset($remote)) || (empty($remote))) {
		$remote=parse_ini_file(REMOTE,true);
	}

	switch ($opt) {
		case 'passthrough':
		$values = array ( "enabled", "disabled" );
		break;
		case 'audio_lay':
		$values = array ( "none", "21", "51" );
		break;
		case 'source_res':
		$values = array ( "enabled", "disabled" );
		break;
		case 'audio_out':
		$values = array ( "hdmi", "local", "both" );
		break;
	}

	$cur=$remote['omxplayer'][$opt];

	$first=reset($values);
	$last=end($values);

	foreach ($values as $dat){
		if ($cur == $dat) { 
			if ($cur != $last) {
				prev($values);$cur=next($values);
			} 
			else {
				reset($values);$cur=reset($values);
			} 
			break;
		}

	}

	$remote['omxplayer'][$opt]=$cur;

	switch ($opt) {
		case 'passthrough':
		if ($remote['omxplayer']['passthrough'] == "enabled") {
			$remote['omxplayer']['audio_out']="hdmi";
			$remote['omxplayer']['audio_lay']="none";
			$out=OUT_PASS_E.' ...';
		}
		else{
			$out=OUT_PASS_D.' ...';
		}
		break;
		case 'audio_lay':
		if ($remote['omxplayer']['audio_lay'] != "none") {
			$remote['omxplayer']['passthrough']="disabled";
			if ($remote['omxplayer']['audio_lay'] == "21"){
				$out=OUT_SWITCH_AL.' 2.1 channels ...';
			}
		}
		else {
			$out=OUT_SWITCH_AL.' None';
		}
		if ($remote['omxplayer']['audio_lay'] == "51"){
			$remote['omxplayer']['audio_out']="hdmi";
			$out=OUT_SWITCH_AL.' 5.1 channels ...';
		}
		break;
		case 'source_res':
		if ($remote['omxplayer']['source_res'] == "enabled") {
			$out=OUT_AUTO_RES_ON.' ...';
		}
		else {
			$out=OUT_AUTO_RES_OFF.' ...';
		}
		break;
		case 'audio_out':
		if ($remote['omxplayer']['audio_out'] != "hdmi"){
			$remote['omxplayer']['passthrough']="disabled";
			if ($remote['omxplayer']['audio_out'] == "both"){
				$out=OUT_AUDIO_OUT.' HDMI & Analogue ...';
			}
			else {
				$out=OUT_AUDIO_OUT.' Analogue ...';
			}
		}
		else {
			$out=OUT_AUDIO_OUT.' HDMI ...';
		}
		break;
	}
	write_ini_file(REMOTE, $remote);
	return $out;
}

function create_channels(){

	GLOBAL $remote;

	if (!isset($remote)) {
		$remote=parse_ini_file(REMOTE,true);
	}

	exec('sudo test -d '.$remote['tvheadend']['conf'].'/.hts/tvheadend/channel/config',$result,$not_exists);

	if ($not_exists) {
		return false;
	}

	exec('sudo find '.$remote['tvheadend']['conf'].'/.hts/tvheadend/channel/config -type f -printf "%f\n"',$out);

	$num_channels=count($out);

	if (empty($out)) {
		return false;
	}

	if (!file_exists('channels.ini')){
		for ($i = 0; $i < $num_channels ; $i++) {
			$channels[$i]['name']=$i+1;
			$channels[$i]['index']=$out[$i];
		}
		write_ini_file('channels.ini', $channels);
		return true;
	}

	else {
		$channels=parse_ini_file("channels.ini",true);
		for ($i = 0; $i < $num_channels ; $i++) {
			foreach ($channels as $key => $dat) {
				if  ($out[$i]==$channels[$key]['index']) {
					continue 2;
				}
			}
			$end_array=count($channels)+1;
			$channels[$end_array]['name']=DVB_NEW;
			$channels[$end_array]['index']=$out[$i];
			write_ini_file('channels.ini', $channels);
			$service=true;
		}
		if ($service) {
			return true;
		}
		else {
			return false;
		}
	}
}

function name_channels() {

	GLOBAL $remote;
	if (!isset($remote)) {
		$remote=parse_ini_file(REMOTE,true);
	}

	foreach ($remote['tvheadend'] as $dat) {
		if (empty($dat)) {
			return;
		}
	}
	$channels=parse_ini_file("channels.ini",true);
	foreach ($channels as $key => $dat) {
		$channels[$key]['name']=sync_name($channels[$key]['index']);
		write_ini_file('channels.ini', $channels);
	}
}

function sync_name($index) {

	GLOBAL $remote;
	if (!isset($remote)) {
		$remote=parse_ini_file(REMOTE,true);
	}

	$not_null=true;
	$i=0;

	while ($not_null) {

		$cmd='omxplayer -i '.escapeshellarg('http://'.$remote['tvheadend']['username'].':'.$remote['tvheadend']['password'].'@'.$remote['tvheadend']['path'].':'.$remote['tvheadend']['port'].$remote['tvheadend']['url'].$index).' 2>&1';
		exec($cmd,$out);

		foreach ($out as $dat) {
			if(strpos($dat, 'service_name') !== false) {
				$info=trim(str_replace("      service_name    : ","",$dat));
				$not_null=false;
			}
		}

		if ($not_null) {
			// i > 4
			if ($i > 2) {
				$not_null=false;
				$info=DVB_NEW;
			}
			else {
				$not_null=true;
				$i++;
			}
		}

	}
	return $info;
}

function create_web_channels(){

	for ($i = 1; $i <= 12; $i++) {
		$webtv[$i]['name']=$i;
		$webtv[$i]['url']="";
	}
	write_ini_file('webtv.ini', $webtv);
}

function screenref($arg,$force){

	GLOBAL $remote;

	if (!isset($remote)) {
		$remote=parse_ini_file(REMOTE,true);
	}

	GLOBAL $sudoer;

	switch ($arg) {

		case 'blank':

		if ($sudoer){

			if (!empty($pid=shell_exec('pgrep monitor'))) {
				shell_exec('sudo pkill xscreensaver');
			}

			if (empty($pid=shell_exec('pgrep xscreensaver'))) {

				shell_exec('bash -c "DISPLAY=\"${DISPLAY:-:0}\" sudo -u '.$remote['options']['user'].' xscreensaver -no-splash &>/dev/null &"');
				usleep(300000);
				
			}

			shell_exec('DISPLAY=:0 sudo -u '.$remote['options']['user'].' xscreensaver-command -activate &>/dev/null');
		}	

		exec($remote['options']['tvservice'].' -s | grep -o off',$pid);	 

		if ($pid[0] == "off") {
			shell_exec($remote['options']['tvservice'].' -p');
			usleep(200000);

		}

		break;

		case 'unblank':

		if ($force) {
			exec($remote['options']['tvservice'].' -s | grep -o off', $sh);	    
			if ($sh[0] == "off") {
				shell_exec($remote['options']['tvservice'].' -p ;');
				if ($sudoer){
					usleep(500000);
					
				}
			}	
		}

		if ($sudoer){
			if (!empty($pid=shell_exec('pgrep xscreensaver'))) {
				exec ('DISPLAY=:0 sudo -u '.$remote['options']['user'].' xscreensaver-command -deactivate &>/dev/null');
			}
		}		

		if ($sudoer) {
			if (($remote['omxplayer']['source_res']=="enabled") || ($force)) {
				if (empty($pid=shell_exec('pgrep omxplayer'))) {
					exec('lsb_release -si',$os);
					if (!stristr($os[0],'Ubuntu')) {
						shell_exec('fbset -depth 8 && fbset -depth 16');
					}
					else {
						shell_exec('sudo chvt '.$remote['options']['nextconsole'].'&& sudo chvt '.$remote['options']['console']); 
					}
					unset($os);
				}
				
				if (empty($pid=shell_exec('pgrep omxplayer'))) {
					shell_exec('DISPLAY="${DISPLAY:-:0}" sudo -u '.$remote['options']['user'].' xrefresh');	
				}
			}
		}
	
		break;
	}
	
}		

if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
	function mb_ucfirst($string) {
		$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
		return $string;
	}
}


?>
