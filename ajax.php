<?php
/*ini_set("log_errors", 1);
ini_set("error_log", "debug");
error_log("");*/

foreach ($_POST['constants'] as $key => $val) {
	define($key,$val);
}

$command = $_POST['command'];	
$arg = $_POST['arg'];

if (isset($_POST['arg1'])) {	
	$arg1 = $_POST['arg1'];	
}

$type = $_POST['type'];	
$pos = $_POST['pos'];	
$out = $_POST['out'];	
$sudoer = $_POST['sudoer'];
$session = $_POST['session'];

switch ($command) {

	case 'run':
	case 'clear_history':
	case 'halt':
	case 'out':
	break;
	default:
	require_once 'functions.php';
	break;

}

switch ($command) {

	case 'run':
	exec($arg,$output);
	$out[0]=ST_EXECUTING.' ...';
	break;
	case 'clear_history':
	unlink('history.ini');
	$out[0]=OUT_APPLYING.' ...';
	break;
	case 'autoupdate':
	$remote=parse_ini_file(REMOTE,true);
	if (!empty($remote)) {
		if ($remote['options']['autoupdate']=="yes") {
			$remote['options']['autoupdate']="";
		}
		else {
			$remote['options']['autoupdate']="yes";
		}
	}
	if (!empty($remote)) {
		write_ini_file(REMOTE,$remote);
	}
	$out[0]=OUT_APPLYING.' ...';
	break;
	case 'maintain':
	$remote=parse_ini_file(REMOTE,true);
	require_once 'setup.php';
	if (!file_exists('youtube-dl')){
		exec('wget https://yt-dl.org/latest/youtube-dl -O youtube-dl || rm youtube-dl');
		if (file_exists('youtube-dl')) {
			chmod('youtube-dl',PERM);
		}
	}
	if (($remote['options']['autoupdate']=="yes") && (empty($pid=shell_exec('pgrep omxplayer')))){
		if (!file_exists('prevent')) {
			exec('git pull',$update); sleep(1);
			$update=implode("\n",$update);
		}

		if (strpos($update, 'Already up-to-date') !== false) {
			$out[0]=OUT_CHK.' ...';
		}
		elseif (strpos($update, 'Updating') !== false) {
			if (strpos($update, 'Your local changes to the following files would be overwritten by merge') !== false) { 

				$out[0]=OUT_MRG.' ...';
			}
			else {
				$out[0]=OUT_CHK.' ...';
			}
			$arg=true;
		}
		else {
			$out[0]=OUT_SETUP;
		}
		
	}
	else {
		$out[0]=OUT_SETUP;
	}
	break;
	case 'reallocate':
	if (empty($pid=shell_exec('pgrep omxplayer'))) {
		name_channels();
		$out[0]=OUT_OMX_END;
	}	
	else {
		$out[0]=OUT_OMX_RUN.' ...';
	}
	break;
	case 'passthrough':
	case 'audio_lay':
	case 'source_res':
	case 'audio_out':
	
	$out[0]=swap($command);
	if (empty($pid=shell_exec('pgrep omxplayer'))) {
		break;
	}
	$command='seekto';
	if (!empty($remote)) {
		$remote['omxplayer']['seek']=1;
		write_ini_file(REMOTE,$remote);
	}
	call('q');
	if ((int)$out[4]==-1) {
		if (!empty($remote)) {
			$remote['omxplayer']['pos']=0;
		}
	}
	else {
		if (!empty($remote)) {
			$remote['omxplayer']['pos']=$out[5];
		}
	}	
	play();
	if (!empty($remote)) {
		$remote['omxplayer']['seek']='';
		write_ini_file(REMOTE,$remote);
	}
	
	break;
	case 'tvheadend':
	$remote=parse_ini_file(REMOTE,true);
	
	if (empty($remote['options']['tvheadend'])) {
		if (!empty($remote)) {
			$remote['options']['tvheadend']="1";
		}
	}
	else {
		if (!empty($remote)) {
			$remote['options']['tvheadend']="";
		}
	}
	if (!empty($remote)) {
		write_ini_file(REMOTE,$remote);
	}
	$out[0]=OUT_APPLYING.' ...';
	break;
	case 'webtv':
	$remote=parse_ini_file(REMOTE,true);
	
	if (empty($remote['options']['webtv'])) {
		if (!empty($remote)) {
			$remote['options']['webtv']="1";
		}
	}
	else {
		if (!empty($remote)) {
			$remote['options']['webtv']="";
		}
	}
	if (!empty($remote)) {
		write_ini_file(REMOTE,$remote);	
	}				
	$out[0]=OUT_APPLYING.' ...';
	break;
	case 'cache':
	$remote=parse_ini_file(REMOTE,true);
	
	if (empty($remote['options']['cache'])){
		if (!empty($remote)) {
			$remote['options']['cache']="1";
		}
	}
	else {
		if (!empty($remote)) {
			$remote['options']['cache']="";
		}
	}
	if (!empty($remote)) {
		write_ini_file(REMOTE,$remote);
	}
	$out[0]=OUT_APPLYING.' ...';
	break;
	case 'edid':
	
	$remote=parse_ini_file(REMOTE,true);
	if ($remote['omxplayer']['compatible']=="no"){
		if (!empty($remote)) {
			$remote['omxplayer']['compatible']="yes";
		}
	}
	else {
		if (!empty($remote)) {
			$remote['omxplayer']['compatible']="no";
		}
	}
	if (!empty($remote)) {
		write_ini_file(REMOTE,$remote);
	}
	$out[0]=OUT_APPLYING.' ...';
	break;
	case 'play':
	
	screenref('blank');
	list($out[0], $pos, $type, $out[7]) = play($pos,$type);

	break;
	
	case 'seekto':

	$remote=parse_ini_file(REMOTE,true);
	if (!empty($remote)) {	
		$remote['omxplayer']['pos']=$arg;
		$remote['omxplayer']['seek']=1;
		write_ini_file(REMOTE,$remote);
	}
	$out[0]=call('q');
	play('','');
	if (!empty($remote)) {
		$remote['omxplayer']['seek']='';
		write_ini_file(REMOTE,$remote);
	}
	break;

	case 'pause':
	if ($out[4]>0) {
		$out[0]=call('p');
	}
	else {
		$out[0]=OUT_NTH;
	}
	break;
	
	case 'stop':
	if (empty($pid=shell_exec('pgrep omxplayer'))) {
		screenref('unblank',true);
	}

	$out[0]=call('q');
	if ($type=='webtv') {
		//PREVENT RECONNECTION
		$type='';
	}
	break;
	
	case 'info':
	
	$out[0]=call('z');
	break;
	
	case 'subsd':
	
	$out[0]=call('f');
	break;
	case 'subsd_':
	
	$out[0]=call('d');
	break;
	case 'subs':
	
	$out[0]=call('m');
	break;
	case 'subs_':
	
	$out[0]=call('n');
	break;

	case 'speed':
	
	$out[0]=call('1');
	break;
	
	case 'speed_':
	
	$out[0]=call('2');
	break;
	
	case 'audio':
	
	$out[0]=call('k');
	break;
	
	case 'audio_':
	
	$out[0]=call('j');
	break;

	case 'chapter':
	if ($type=='file') {
		$out[0]=call('o');
	}
	else {
		$out[0]=OUT_NTH;
	}
	
	break;
	case 'chapter_':
	if ($type=='file') {
		$out[0]=call('i');
	}
	else {
		$out[0]=OUT_NTH;
	}
	break;	
	case 'tsubs':
	
	$out[0]=call('s');
	break;
	case 'vol':
	if (empty($pid=shell_exec('pgrep omxplayer'))) {
		$remote=parse_ini_file(REMOTE,true);
		exec('groups | grep audio',$sh,$error);
		if (!$error) {
			$cmd='amixer -c '.$remote['alsa']['card'].' set '.$remote['alsa']['control'].' 2dB+ | grep % | awk \'{print $4}\' | grep -o \'[0-9]\+\'';
			exec($cmd,$out_,$error);
			if (!$error){
				$out[0]=OUT_VOLUME.' '.$out_[0].'%';
			}
			else {
				if ($remote['alsa']['control']=='PCM') {
					$ctrl='Master';
				}
				else {
					$ctrl='PCM';
				}
				$cmd='amixer -c '.$remote['alsa']['card'].' set '.$ctrl.' 2dB+ | grep % | awk \'{print $4}\' | grep -o \'[0-9]\+\'';
				exec($cmd,$out_,$error);				
				if (!$error){
					$out[0]=OUT_VOLUME.' '.$out_[0].'%';
				}				
				else {
					$out[0]=OUT_ERROR_ALSA.' ...';
				}
			}
		}
		else {
			$apache_user=posix_getpwuid(posix_geteuid());
			$out_=OUT_USER.' '.$apache_user['name'].' '.OUT_ALSA_PERM.' ...';
			$out[0]=$out_;
		}
		unset($error, $apache_user);
	}
	else {
		
		$out[0]=call('+');
	}
	break;
	case 'vol_':
	if (empty($pid=shell_exec('pgrep omxplayer'))) {
		$remote=parse_ini_file(REMOTE,true);
		exec('groups | grep audio',$sh,$error);
		if (!$error) {
			$cmd='amixer -c '.$remote['alsa']['card'].' set '.$remote['alsa']['control'].' 2dB- | grep % | awk \'{print $4}\' | grep -o \'[0-9]\+\'';
			exec($cmd,$out_,$error);
			if (!$error){
				$out[0]=OUT_VOLUME.' '.$out_[0].'%';
			}
			else {
				if ($remote['alsa']['control']=='PCM') {
					$ctrl='Master';
				}
				else {
					$ctrl='PCM';
				}
				$cmd='amixer -c '.$remote['alsa']['card'].' set '.$ctrl.' 2dB- | grep % | awk \'{print $4}\' | grep -o \'[0-9]\+\'';
				exec($cmd,$out_,$error);				
				if (!$error){
					$out[0]=OUT_VOLUME.' '.$out_[0].'%';
				}				
				else {
					$out[0]=OUT_ERROR_ALSA.' ...';
				}
			}
		}
		else {
			$apache_user=posix_getpwuid(posix_geteuid());
			$out_=OUT_USER.' '.$apache_user['name'].' '.OUT_ALSA_PERM.' ...';
			$out[0]=$out_;
		}
		unset($error, $apache_user);
	}
	else {
		
		$out[0]=call('-');
	}
	break;
	case 'seek30m':
	if ($out[4]>0) {
		call(pack('n',0x5b44));
		$out[0]=OUT_SEEK;
	}
	else {
		$out[0]=OUT_NTH;
	}
	break;

	case 'seek600m':
	if ($out[4]>0) {
		$out[0]=call(pack('n',0x5b42));
	}
	else {
		$out[0]=OUT_NTH;
	}
	break;			
	
	case 'seek30':
	if ($out[4]>0) {
		if (((int)$out[4]-(int)$out[5])>29) {
			call(pack('n',0x5b43));
			$out[0]=OUT_SEEK;
		}
		else {
			$out[0]=OUT_NTH;
		}

	}
	else {
		$out[0]=OUT_NTH;
	}
	break;

	case 'seek600':
	if ($out[4]>0) {
		if (((int)$out[4]-(int)$out[5])>600) {
			$out[0]=call(pack('n',0x5b41));
		}
		else {
			$out[0]=OUT_NTH;
		}
	}
	else {
		$out[0]=OUT_NTH;
	}
	break;	

	case 'halt':
	if ($arg1=="halt"){
		shell_exec('sudo halt');
		$out[0]=OUT_RPI_H.' ...';
	}
	elseif ($arg1=="reboot") {
		shell_exec('sudo reboot');
		$out[0]=OUT_RPI_R.' ...';
	}
	elseif ($arg1=="1") {
		$apache_user = posix_getpwuid(posix_geteuid());
		$out[0]=OUT_USER.' '.$apache_user['name'].' '.OUT_RPI_SUDO.' ...';
	}
	break;
	case 'switch_hdmi' :
	if (empty($pid=shell_exec('pgrep omxplayer'))) {

		$remote=parse_ini_file(REMOTE,true);
		exec($remote['options']['tvservice'].' -s | grep -o off',$sh);
		if ($sh[0] == "off"){ 
			shell_exec($remote['options']['tvservice'].' -p;'); 
			usleep(500000);
			screenref('unblank',true);
			
			$out[0]=OUT_PON.' ...';
		} 
		else { 
			shell_exec($remote['options']['tvservice'].' -o;'); 
			$out[0]=OUT_POFF.' ...';
		}
	}
	else{
		$out[0]=OUT_OMX_RUN.' ...';
	}
	break;
	case 'ssaver' :
	$pid=shell_exec('pgrep lightdm');
	$remote=parse_ini_file(REMOTE,true);
	if (!empty($pid) && !empty($remote['options']['tvservice']) && !empty($remote['options']['xscreensaver']) && $sudoer && !empty($remote['options']['user'])) {
		
		if (!empty($pid=shell_exec('pgrep monitor'))) { 
			shell_exec ('sudo pkill monitor');
			screenref('unblank',true);
			$out[0]=OUT_SSAVER_OFF.' ...';
		}
		else { 
			shell_exec ('bash -c "sudo -u '.$remote['options']['user'].' '.getcwd().'/monitor &>/dev/null &"');
			$out[0]=OUT_SSAVER_ON.' ...';
		}	
	}
	else {
		$out[0]=OUT_ERRSS;
	}
	break;
	case 'out':
	switch (true) {
		case file_exists(INFO):
		$remote=parse_ini_file('remote.cfg',true);
		if ($remote['options']['client']==$session) {
			$length=file_get_contents(INFO);
			if (preg_match('/(?<=Duration: ).*?(?=,)/', $length, $match)) {
				if ($match[0]!='N/A') {
					$match=explode(":", $match[0]);
					$time=($match[0]*3600)+($match[1]*60)+floor($match[2]);
					if ($time>0) {
						$out[4]=(int)$time;
					}
					else {
						$out[4]=0;
						$out[3]='';
					}
				}
				else {
					$out[4]=-1;
					$out[3]='';
				}
			}
			else {
				$out[4]=0;
				$out[3]='';
			}		
			unlink(INFO);
			$out[0]=OUT_READ_INFO.' ...';
		}
		else {
			$out[0]=OUT_SESSION.' ...';

			// STOP LOOPING TO PREVENT CONFLICTS
			
			$out[8]=true;
			//$out[4]=0;
		}
		break;
		case file_exists(OUT_LOG):
		if (!empty($pid=shell_exec('pgrep omxplayer'))) {
			$sync=false;
			if ($type=='file') {
				$curr=basename($pos);
			}
			else {
				$curr=$out[7];
			}				
			$output='';
			$file = fopen(OUT_LOG, 'r+');
			$line = fseek($file, -120, SEEK_END);
			while (!feof($file)) {
				$output .= fgets($file);
			}
			ftruncate($file, 0);
			fclose($file);
			if (preg_match('/([0-9]+\b)/', $output, $time)) {
				(int)$time=floor((floor($time[0])/1000000));
				if ($time<=0){
					$time=$out[5];
				}
				$out[5]=$time;
				if ((int)$out[4]==0){
						//PREVENT LOOP
					if ((!file_exists(INFO)) && (!file_exists(LENGTH))) {
							//PREVENT LOOP+ TOUCHING THE EMPTY FILE
						touch(LENGTH);
						shell_exec('omxplayer -i '.escapeshellarg($pos).' >'.LENGTH.' 2>&1');
						$info=file_get_contents(LENGTH);
						
						unlink(LENGTH);
						if (preg_match('/(?<=Duration: ).*?(?=,)/', $info, $length)) {
							if ($length[0]!='N/A') {
								$length=explode(":", $length[0]);
								$time=($length[0]*3600)+($length[1]*60)+floor($length[2]);
								if ($time>0) {
									$out[4]=(int)$time;
								}
								else {
									$out[4]=-1;
									$out[3]='';
								}
							}
							else {
								$out[4]=-1;
								$out[3]='';
							} 
						}
						else {
							$out[4]=-1;
							$out[3]='';
						}
					}
				}
				else {
					require_once 'functions.php';
					if ((int)$out[4]>=0) {
						$out[3]=read_time((int)$out[4]-$time);
					}
					else {
						$out[3]='';
					}
					$out[6]=read_time((int)$time);
				}
			}
			else {
				
				$sync=true;
			}
			if (!$sync) {
				$out[2]=OUT_PLAYING.' \''.$curr.'\'';
			}
			else {
				if ((int)$out[4]!=-1) {
					$out[2]=OUT_SYNC.' ...';
				}
				else {
					$out[2]=OUT_PLAYING.' \''.$curr.'\'';
				}
			}
		}
		else {
			$output=file_get_contents(OUT_LOG);
			switch (true) {
				case stristr($output,'Stopped at'):					
				require_once 'functions.php';
				$remote=parse_ini_file(REMOTE,true);
				$seek=$remote['omxplayer']['seek'];
				if ($seek=="1"){
					sleep(2);
					if (empty($pd=shell_exec('pgrep omxplayer'))) {
						if (file_exists(OUT_LOG)) {
							if (!empty($remote)) {
								$remote['omxplayer']['seek']='';
								write_ini_file(REMOTE,$remote);
							}
						}	
					}			
					break;
				}
				if (empty($pid=shell_exec('pgrep omxplayer'))) {
					if (file_exists(OUT_LOG)) {
						
						unlink(OUT_LOG);
					}	
				}	
				$out[0]=OUT_STOPPED;
				screenref('unblank');
				if (((int)$out[4]==-1) || ($type=="tv")){
					break;
				}
				$result = explode("\n", $output);
				foreach ($result as $line) {
					if (stristr($line,'Stopped at')){
						$time = explode("Stopped at: ", $line);
						$time = $time[1];
						$time = explode(":", $time);
						$time = ($time[2]+($time[1]*60)+($time[0]*3600));
						$remote=parse_ini_file(REMOTE,true);
						if (!empty($remote)) {
							$remote['omxplayer']['pos']=$time;
							write_ini_file(REMOTE,$remote);	
						}						
						break;	
					}
				}
				break;
				case stristr($output,'not found'):
				require_once 'functions.php';
				if ($type=='tv') {
					$out[0]=OUT_ERROR_TV.' ...';
				}
				elseif ($type=='file'){
					$out[0]=OUT_FILE_N_F.' ...';
				}
				else {
					$out[0]=OUT_ERROR_LNK.' ...';
				}
				unlink(OUT_LOG);
				screenref('unblank');
				break;
				case stristr($output,'473'):
				case stristr($output,'472'):
				case stristr($output,'OMXPLAYER_LIBS'):
				require_once 'functions.php';
				unlink(OUT_LOG);
				$out[0].=OUT_ERROR_HDMI.' ...';
				screenref('unblank',true);
				break;
				case stristr($output,'the remote application did not send a reply'):
				require_once 'functions.php';
				if ($type=="url") {
					unlink(OUT_LOG); 
					$history=parse_ini_file("history.ini",true);
					$last=end($history);
					$last=array_search($last,$history);
					if (isset($history[$last]['direct'])) {
						touch(YT);
						$pos=$history[$last]['direct'];
						unset($history[$last]);
						write_ini_file('history.ini',$history);
						list($out[0], $pos, $type, $out[7]) = play($pos,'url');
							//UPDATE HISTORY
						$command="play";
						break;
					}
					else {
						$out[0]=OUT_ERROR_ADD.' ...';
					}
				}
				elseif ($type=='tv') {
					unlink(OUT_LOG);
					$out[0]=OUT_ERROR_TV.' ...';
					screenref('unblank');
					break;
				}
				elseif ($type=='webtv') {
					unlink(OUT_LOG);
					$out[0]=OUT_ERROR_WEB.' ...';
					screenref('unblank');
					break;										
				}
				else {
					if (empty($pd=shell_exec('pgrep omxplayer'))) {
						unlink(OUT_LOG);
					}
					$out[0]=OUT_ERROR_CODEC.' ...';
					screenref('unblank');
					break;
				}						
				break;
				case stristr($output,'have a nice day ;)'):
				case stristr($output,'Terminated'):
				require_once 'functions.php';
				$remote=parse_ini_file(REMOTE,true);
				if ($type=="webtv") {
					unlink(OUT_LOG);
					$command='seekto';
					if (!empty($remote)) {
						$remote['omxplayer']['seek']=1;
						write_ini_file(REMOTE,$remote);
					}
					if (!empty($remote)) {
						$remote['omxplayer']['pos']=0;
					}
					play();
					if (!empty($remote)) {
						$remote['omxplayer']['seek']='';
						write_ini_file(REMOTE,$remote);
					}
					$out[0]=OUT_RECONNECTING.' ...';
					break;
				}
				else { 
					if (!empty($remote)) {
						$remote['omxplayer']['pos']=0;
						write_ini_file(REMOTE,$remote);
					}
				}
				$out[0]=OUT_OMX_END;
				if (empty($pd=shell_exec('pgrep omxplayer'))) {
					unlink(OUT_LOG);
				}
				screenref('unblank');
				break;
				default:
				require_once 'functions.php';
				if (empty($pd=shell_exec('pgrep omxplayer'))) {
					unlink(OUT_LOG);
				}					
				$out[0]=OUT_APPLYING.' ...';
				//screenref('unblank');
				break;
			}				
		}
		break;
		case (file_exists(YT)):
		$out[0]=OUT_PREPARE_DIRECT.' ...';
		break;
		default:
		$out[1]=OUT_IDLE;
		$out[4]=0;
		$out[5]=1;
		$out[6]=0;
		$out[3]='';
		$arg='idle';
		break;
	}
	break;
}
if (($command!="out") || ($command!="seekto")) {
	$arg1="0";
}

$return=array(
	'command' => $command, 
	'arg' => $arg, 
	'arg1' => $arg1,
	'type' => $type,
	'pos' => $pos,
	'out' => $out,
	);

echo json_encode($return);

?>
