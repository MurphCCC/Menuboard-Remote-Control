<?php 

if (file_exists('remote.cfg')){
	if ((!isset($remote)) || (empty($remote))) {
		$remote=parse_ini_file('remote.cfg',true);
	}
}

if (isset($remote['options']['locale']) && !empty($remote['options']['locale'])) {		
	header('Content-Type: text/html; charset=utf-8;');
	mb_internal_encoding('UTF-8');
	setlocale(LC_ALL, $remote['options']['locale']);
	putenv('LC_ALL='.$remote['options']['locale']);
	define('LOCALE',$remote['options']['locale']);
}

if (!empty($remote['options']['display'])) {
	if (file_exists($remote['options']['display'])) {
		$external=parse_ini_file($remote['options']['display'],true);
		if (!empty($external['external']['lang'])) {
			$prefix=$external['external']['lang'];
			if (array_key_exists($prefix,$lang)){
				$prefix.=rand(100,1000);
			}

			foreach (array_keys($lang) as $key) {
				if ($key!='lang'){
					if (!empty($external[$key][$external['external']['lang']])) {
						$lang[$key][$prefix]=$external[$key][$external['external']['lang']];
					}
					else {
						$lang[$key][$prefix]=$lang[$key]['en'];
					}
				}
			}
			unset($external);
		}
		else {
			$prefix="en";	
		}
	}
	
	elseif (array_key_exists($remote['options']['display'],$lang['lang'])) {
		switch ($remote['options']['display']) {
			case 'en':
			case 'el':
			break;
			default:
			foreach ($lang as $key => $val) {
				if ($key!="lang"){
					if (empty($lang[$key][$remote['options']['display']])) {
						$lang[$key][$remote['options']['display']]=$lang[$key]['en'];
					}
				}
			}
			break;
		}
		$prefix=$remote['options']['display'];
		
	}
	
	else {
		$postpone=true;
	}
	
}
else {
	$postpone=true;
}

if (!$postpone) {
	foreach (array_keys($lang) as $key){

		switch($key) {
			
			case 'INDEX_HALT':
			case 'INDEX_RE':
			case 'ST_SETUSER': 
			case 'ST_SYSTEM': 
			case 'ST_SUBS': 
			case 'ST_SUBSLOCAL': 
			case 'ST_SUBSINTERNET': 
			case 'ST_FONTSIZE': 
			case 'ST_MEDIAPATHS': 
			case 'ST_UN': 
			case 'ST_PW': 
			case 'ST_PORT': 
			case 'ST_AC': 
			case 'ST_LANG':
			case 'ST_BACK':
			case 'ST_SCR_CAP':
			case 'ST_CMD':
			case 'ST_COLOR':
			case 'ST_RUNAS':
			case 'ST_FONT':	
			case 'DVB_NEW':	
			case 'ST_THEMES':
			case 'ST_IMAGE':
			define($key,mb_convert_case($lang[$key][$prefix], MB_CASE_TITLE));
			break;
			case 'ST_SETTINGS': 
			case 'ST_GENERAL': 
			case 'ST_APPLY': 
			case 'ST_LOCAL': 
			case 'ST_PATHS': 
			case 'ST_TV':
			case 'ST_LINK':
			case 'ST_ALLOCATE': 
			case 'ST_REMOVE': 
			case 'ST_WEBTV': 
			case 'ST_ADD': 
			case 'ST_LOGO': 
			case 'ST_UPLOAD': 
			case 'ST_GENERATE': 
			case 'ST_BROWSE': 
			case 'ST_SAVE':
			case 'INDEX_VOLUME':
			case 'INDEX_PCHAP':
			case 'INDEX_SPEEDM':
			case 'INDEX_NCHAP':
			case 'INDEX_PSTREAM':
			case 'INDEX_SPEED':
			case 'INDEX_NSTREAM':
			case 'INDEX_PSUBS':
			case 'INDEX_TSUBS':
			case 'INDEX_NSUBS':
			case 'INDEX_SMDELAY':
			case 'INDEX_SDELAY':
			case 'INDEX_SSAVER':
			case 'INDEX_SHDMI':
			case 'INDEX_PASSE':
			case 'INDEX_PASSD':
			case 'INDEX_ARESD':
			case 'INDEX_ARESE':
			case 'INDEX_LAY0':
			case 'INDEX_LAY51':
			case 'INDEX_LAY21':
			case 'ST_RESETCH':
			case 'ST_RESETALL':
			case 'ST_PP':
			case 'ST_COMPVIEW': 
			case 'ST_SCROLL': 
			case 'ST_VERB': 
			case 'ST_CACHE': 
			case 'ST_SCRIPTEDIT':
			case 'ST_EXEC':
			case 'ST_EXECUTE':
			case 'ST_UPDATE':
			case 'ST_FORCE_UP':
			define($key,mb_convert_case($lang[$key][$prefix],MB_CASE_UPPER));
			break;
			case 'INDEX_RECENT':
			case 'INDEX_SEL_FILE':
			case 'INDEX_SUBS':
			case 'ST_SEL_SERV': 
			case 'ST_ADDUP': 
			case 'ST_EDITUP': 
			case 'ST_HD':
			case 'ST_SHOWCH': 
			case 'ST_MOVEUP':
			case 'OUT_STOP_CUR_VID':
			case 'OUT_PREPARE':
			case 'OUT_CONNECTING':
			case 'OUT_SELECT_FILE':
			case 'OUT_FILE_N_F':
			case 'OUT_GENERATE_PATHS':
			case 'OUT_ERROR_YT':
			case 'OUT_REQUESTING':
			case 'OUT_SCREEN_REF':
			case 'OUT_NTH':
			case 'OUT_PASS_E':
			case 'OUT_PASS_D':
			case 'OUT_SWITCH_AL':
			case 'OUT_AUTO_RES_ON':
			case 'OUT_AUTO_RES_OFF':
			case 'OUT_AUDIO_OUT':
			case 'OUT_APPLYING':
			case 'OUT_PLAYING':
			case 'OUT_SUBS_TRUE':
			case 'OUT_SUBS_FALSE':
			case 'OUT_PREPARE_DIRECT':
			case 'OUT_GET_SUBS':
			case 'OUT_ERROR_TV':
			case 'OUT_ERROR_LNK':
			case 'OUT_ERROR_HDMI':
			case 'OUT_ERROR_CODEC':
			case 'OUT_ERRSS':
			case 'OUT_GENERATE_LNK':
			case 'OUT_ERROR_ADD':
			case 'OUT_OMX_END':
			case 'OUT_OMX_RUN':
			case 'OUT_IDLE':
			case 'OUT_SSAVER_OFF':
			case 'OUT_SSAVER_ON':
			case 'OUT_VOLUME':
			case 'OUT_ERROR_ALSA':
			case 'OUT_USER':
			case 'OUT_RPI_H':
			case 'OUT_RPI_R':
			case 'OUT_RPI_SUDO':
			case 'OUT_KODI_LOAD':
			case 'OUT_ERROR_DIR':
			case 'OUT_CACHE_OK':
			case 'OUT_WELCOME':
			case 'OUT_NO_FILES':
			case 'OUT_PAUSED':
			case 'OUT_STOPPED':
			case 'OUT_SEEKTO':
			case 'OUT_PLAYSPEED':
			case 'OUT_ERRSS':
			case 'OUT_JUMP':
			case 'OUT_PON':
			case 'OUT_POFF':
			case 'OUT_RECONNECTING':
			case 'OUT_READ_INFO':
			case 'OUT_CHK':
			case 'OUT_SETUP':
			case 'ST_EXECUTING':
			case 'ST_FONT_DEF':
			case 'OUT_SYNC':
			case 'OUT_SEEK':
			case 'OUT_ERROR_WEB':
			case 'INDEX_RESO':
			case 'DVB_SYNC':
			case 'DVB_SYNC_ALERT':
			case 'OUT_UPDATE':
			case 'OUT_MRG':
			case 'OUT_SESSION':
			case 'OUT_SESS_ALERT':
			define($key, mb_ucfirst($lang[$key][$prefix]));
			break;
			case '-':
			case 'INDEX_CLEAR_HIS':
			define($key,mb_convert_case($lang[$key][$prefix],MB_CASE_LOWER));
			break;
			case 'INDEX_URL':
			define($key,mb_convert_case($lang[$key]['en'], MB_CASE_LOWER));
			break;			
			case 'OUT_ALSA_PERM':
			define($key,$lang[$key][$prefix]);
			break;
		}
		
	}		
}

define('OUT_VOLUME',mb_convert_case($lang['INDEX_VOLUME'][$prefix], MB_CASE_TITLE));
define('INDEX_URL','url');
define('OUT_NTH', ':)'); 

define('REV', '100416');
define('PIPE', getcwd().'/fifo');
define('MIN_SIZE', 100000000);
define('PERM', 0777);
define('OUT_LOG', 'out.log');
define('LENGTH', 'length');
define('INFO', 'info');
define('YT', 'yt');
define('FILES', 'files');
define('PATHS', 'paths');
define('REMOTE', 'remote.cfg');
define('RES', 'res');
define('SIZE', 'size');


$themes= array ( 

'themes' =>	array (
	'raspberry' => ST_FONT_DEF,
	'navy' => 'Navy',
	'terminal' => 'Terminal'
),

// BACKGROUND IMAGE

'size' => array (
	'raspberry' => 'initial',
	'navy' => '100% 100%',
	'terminal' => '100% 100%'
	),
'repeat' => array (
	'raspberry' => '',
	'navy' => 'repeat',
	'terminal' => 'repeat'
	),
'position' => array (
	'raspberry' => 'center top',
	'navy' => 'center top',
	'terminal' => 'center top'
	),

//COLOR

'group1' => array ( //TEXT
	'raspberry' => '#fff', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group1b' => array ( //URL
	'raspberry' => '#000', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group1c' => array ( //PLACEHOLDER
	'raspberry' => 'gray', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group1d' => array ( //SUBMIT REMOVE 
	'raspberry' => 'gray', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group1e' => array ( //SUBMIT REMOVE HOVER
	'raspberry' => '#fff', 
	'navy' => 'rgba(0,159,228,1)',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group2' => array ( //LINKS
	'raspberry' => 'gray', 
	'navy' => '#fff', 
	'terminal' => '#4c9900'
	),
'group2b' => array ( //CHECK OPTIONS
	'raspberry' => 'gray', 
	'navy' => '#fff', 
	'terminal' => '#4c9900'
	),
'group3' => array ( // HOVER TEXT
	'raspberry' => '#626262', 
	'navy' => '#4169E1',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group3b' => array ( // WRAP HOVER TEXT
	'raspberry' => '#626262', 
	'navy' => '#fff',
	'terminal' => '#000'
	),
'group3c' => array ( // CHANNEL HOVER
	'raspberry' => '', 
	'navy' => '',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group4' => array ( // WRAP
	'raspberry' => '#404040', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group4b' => array ( // CHECK OPTIONS (CHECKED)
	'raspberry' => '#404040', 
	'navy' => 'rgba(0,159,228,1)',
	'terminal' => 'rgba(120,250,0,1)'
	),
'group5' => array ( // CONSOLE
	'raspberry' => '#606060', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group6' => array ( // TIMELINE THUMB COLOR
	'raspberry' => '#1c1c1c', 
	'navy' => 'transparent',
	'terminal' => ''
	),
'group7' => array ( // TAG GRAY HOVER
	'raspberry' => '#fff', 
	'navy' => '#fff',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group7b' => array ( // TAG DARK HOVER
	'raspberry' => '#fff', 
	'navy' => '#fff',
	'terminal' => 'rgba(0,255,0,1)'
),

//BACKGROUND

'group8' => array ( // BACKGROUND
	'raspberry' => '#99004c', 
	'navy' => 'rgba(17,17,74,1)',
	'terminal' => '#000'
	),
'group8b' => array ( // HALT
	'raspberry' => '#99004c', 
	'navy' => 'transparent',
	'terminal' => '#000'
	),
'group8c' => array ( // ROUND BUTTON HOVER
	'raspberry' => '#99004c', 
	'navy' => 'transparent',
	'terminal' => '#000'
	),
'group9' => array ( //ROUND BUTTON
	'raspberry' => '#4c9900', 
	'navy' => 'transparent',
	'terminal' => '#000'
	),
'group9b' => array ( //CHANNEL HOVER
	'raspberry' => '#4c9900', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => '#000'
	),
'group9c' => array ( //TRACK THUMB ENABLED
	'raspberry' => '#4c9900', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => '#4c9900'
	),
'group10' => array ( //POPUP BACKGROUND
	'raspberry' => '#1c1c1c', 
	'navy' => '#191970',
	'terminal' => '#000'
	),
'group10b' => array ( //REMOTE BACKGROUND
	'raspberry' => '#404040', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => '#000'
	),
'group11' => array ( // INSIDE BACKGROUND COLOR/IMAGE
	'raspberry' => '#404040', 
	'navy' => 'rgba(25,25,112,0.7)',
	'terminal' => 'transparent'
	),
'group11b' => array ( // INSIDE OPTION 1 (POSITION)
	'raspberry' => '', 
	'navy' => '',
	'terminal' => ''
	),
'group11c' => array ( // INSIDE OPTION 2 (SIZE)
	'raspberry' => '', 
	'navy' => '',
	'terminal' => ''
	),
'group11d' => array ( // INSIDE OPTION 3 (REPEAT)
	'raspberry' => '', 
	'navy' => '',
	'terminal' => ''
	),
'group11e' => array ( // TAG GRAY
	'raspberry' => '#404040', 
	'navy' => 'rgba(37,60,153,0.7)',
	'terminal' => '#000'
	),
'group11f' => array ( // OPTION SELECT BACKGROUND
	'raspberry' => '#404040', 
	'navy' => 'rgba(37,60,153,0.9)',
	'terminal' => '#000'
	),
'group12' => array ( //CHANNELS & WRAP BUTTONS & TOP HOVER
	'raspberry' => '#606060', 
	'navy' => 'rgba(25,25,112,1)',
	'terminal' => '#000'
	),
'group12b' => array ( // CONFIG CHECK BUTTON BACKGROUND
	'raspberry' => '#606060', 
	'navy' => '#191970',
	'terminal' => '#000'
	),
'group12c' => array ( //LINE SEPARATOR HOVER
	'raspberry' => '#505050', 
	'navy' => '#4169E1',
	'terminal' => '#000'
	),
'group13' => array ( //POPUP SELECTED OPTION BACKGROUND
	'raspberry' => '#2c2c2c', 
	'navy' => '#4169E1',
	'terminal' => '#000'
	),
'group14' => array ( //CHANNEL BACKGROUND
	'raspberry' => '#444', 
	'navy' => 'rgba(25,25,112,1)',
	'terminal' => '#000'
	),
'group14b' => array ( //RASPBERRY REMOTE BOX
	'raspberry' => '#444', 
	'navy' => 'transparent',
	'terminal' => '#000'
	),
'group15' => array ( // LINE SEPARATOR
	'raspberry' => '#444', 
	'navy' => '#191970',
	'terminal' => '#4c9900'
	),
'group16' => array ( // OPTION SELECT HOVER
	'raspberry' => '#505050', 
	'navy' => '#4169E1',
	'terminal' => '#000'
	),
'group17' => array ( // POPUP NOT CHECKED BACKGROUND
	'raspberry' => '#fff', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group18' => array ( // TRACK BACKGROUND
	'raspberry' => '#101010', 
	'navy' => 'rgba(25,25,112,0.3)',
	'terminal' => 'rgba(0,0,0,0.5)'
	),
'group19' => array ( // TAG DARK
	'raspberry' => '#000', 
	'navy' => 'rgba(25,25,112,1)',
	'terminal' => '#000'
	),
'group19b' => array ( // TAG KODI
	'raspberry' => '#12B2E7', 
	'navy' => '#12B2E7',
	'terminal' => '#000'
	),
'group19c' => array ( // TAG VNC
	'raspberry' => 'green', 
	'navy' => 'green',
	'terminal' => '#000'
	),
'group20' => array ( // TRACK
	'raspberry' => '#303030', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group21' => array ( // TRACK THUMB DISABLED
	'raspberry' => '#202020', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => '#4c9900'
	),
'group22' => array ( // WRAP SCROLL BAR
	'raspberry' => 'gray', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => '#4c9900'
	),
'group23' => array ( // POPUP INNER SCROLL BAR
	'raspberry' => '#1c1c1c', 
	'navy' => '#191970',
	'terminal' => '#000'
	),
'group23b' => array ( // POPUP INNER SCROLL BAR
	'raspberry' => '#101010', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group24' => array ( //FILE SELECT HOVER
	'raspberry' => 'rgba(153,0,76,0.9)', 
	'navy' => 'rgba(0,159,228,0.6)',
	'terminal' => '#000'
	),
'group25' => array ( // IE FILE SELECT COLOR HOVER
	'raspberry' => '#99004c', 
	'navy' => 'transparent',
	'terminal' => ''
	),
'group26' => array ( // IE FILE SELECT COLOR HOVER (ALT)
	'raspberry' => '#99004c', 
	'navy' => 'transparent',
	'terminal' => ''
	),
'group28' => array ( // FILE SELECT
	'raspberry' => 'rgba(76,153,0,0.9)', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => '#000'
	),
'group29' => array ( // IE FILE SELECT COLOR
	'raspberry' => '#4c9900', 
	'navy' => 'transparent',
	'terminal' => ''
	),
'group30' => array ( //IE FILE SELECT COLOR (ALT)
	'raspberry' => '#4c9900', 
	'navy' => 'transparent',
	'terminal' => ''
	),
'group31' => array ( // URL INSIDE BEGIN
	'raspberry' => '#fff', 
	'navy' => 'transparent',
	'terminal' => 'transparent'
	),
'group32' => array ( // URL INSIDE END
	'raspberry' => '#c4c4c4', 
	'navy' => 'transparent',
	'terminal' => 'transparent'
	),
'group33' => array ( // TAG SCROLL BAR START & END
	'raspberry' => 'rgba(64, 64, 64, 0)', 
	'navy' => 'rgba(0,159,228,0.75)',
	'terminal' => '#4c9900'
	),
'group34' => array ( // TAG SCROLL BAR MIDDLE
	'raspberry' => 'rgba(28, 28, 28, 0.75)', 
	'navy' => 'rgba(0,159,228,0.74)',
	'terminal' => '#4c9900'
	),
'group53' => array ( // PLAY BACKGROUND
	'raspberry' => 'transparent', 
	'navy' => 'transparent',
	'terminal' => 'transparent'
	),

//BORDER

'group38' => array ( //POPUP INPUT BORDER
	'raspberry' => '#404040', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group39' => array ( //POPUP HEADER BORDER & BUTTON BORDER
	'raspberry' => '#4c9900', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group40' => array ( // BORDER FRONT LAYER
	'raspberry' => '#626262', 
	'navy' => 'transparent',
	'terminal' => 'transparent'
	),
'group40b' => array ( // SUBMIT BUTTON BORDER
	'raspberry' => '#626262', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group40c' => array ( // SUBMIT BUTTON BORDER HOVER
	'raspberry' => '#fff', 
	'navy' => 'rgba(0,159,228,1)',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group41' => array ( // PLAY
	'raspberry' => '#606060', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group42' => array ( // ROUND BUTTON
	'raspberry' => '#fff', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group42b' => array ( // ROUND BORDER HOVER
	'raspberry' => '#fff', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group43' => array ( // URL
	'raspberry' => '#808080', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group44' => array ( // PLAY HOVER
	'raspberry' => 'gray', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group45' => array ( //POPUP CHECK BORDER
	'raspberry' => '#ddd', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),
'group46' => array ( //CONFIG SUBTITLE BOTTOM LINE
	'raspberry' => '#505050', 
	'navy' => '#fff',
	'terminal' => '#4c9900'
	),

//OUTLINE

'group47' => array ( // SELECT OUTLINE
	'raspberry' => '#fff', 
	'navy' => 'transparent',
	'terminal' => ''
	),
'group48' => array ( //POPUP CHECK OUTLINE
	'raspberry' => 'transparent', 
	'navy' => 'transparent',
	'terminal' => 'transparent'
	),

//MISC

'group49' => array ( // ROUND BOX SHADOW
	'raspberry' => 'gray', 
	'navy' => 'rgba(37,60,153,0.7)',
	'terminal' => '#000'
	),
'group50' => array ( // URL HOVER
	'raspberry' => '#a0a0a0', 
	'navy' => '#B0E0E6',
	'terminal' => 'rgba(0,255,0,1)'
	),
'group51' => array ( // DOTTED CLEAR
	'raspberry' => 'darkred', 
	'navy' => 'rgba(0,159,228,1)',
	'terminal' => '#4c9900'
	),
'group52' => array ( // HALT HOVER
	'raspberry' => '#DC143C', 
	'navy' => '#191970',
	'terminal' => '#000'
	),
'group54' => array ( // URL SHADOW
	'raspberry' => '3px 3px 0px #626262', 
	'navy' => '0',
	'terminal' => '0'
	),
'group55' => array ( // WRAP HOVER BACKGROUND
	'raspberry' => '#404040', 
	'navy' => 'rgba(0,159,228,0.9)',
	'terminal' => '#4c9900'
	),
'icons' => array ( // CHANNEL ICONS
	'raspberry' => true, 
	'navy' => true,
	'terminal' => false
)


);

switch ($remote['options']['theme']) {
	case 'raspberry':
	default:
		foreach ($themes as $key => $val) {
			if ($key!='themes') {
				define($key,$val['raspberry']);
			}
		}
		$remote['options']['theme']="raspberry";
	break;
	case 'navy':
	foreach ($themes as $key => $val) {
		if ($key!='themes') {
			define($key,$val['navy']);
		}
	}
	break;	
	case 'terminal':
	foreach ($themes as $key => $val) {
		if ($key!='themes') {
			define($key,$val['terminal']);
		}
	}
	break;	
}

define('theme',$remote['options']['theme']);

$constants=get_defined_constants(true);
$constants=$constants['user'];

?>
