<html>
<head>
	<title>Raspberry Remote Translation Page</title>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
	<meta name="description" content="WebUI Remote Control for Raspberry Pi - Translation Page" />
	<script type="text/javascript" src="jquery-1.12.0.js"></script>
	<?php 
	$ip=$_SERVER['SERVER_ADDR'];
	require_once 'languages.php';
	require_once 'functions.php';
	$remote=parse_ini_file('remote.cfg',true);
	header('Content-Type: text/html; charset=utf-8;');
	mb_internal_encoding('UTF-8');
	setlocale(LC_ALL, $remote['options']['locale']);
	putenv('LC_ALL='.$remote['options']['locale']);
	if (file_exists($remote['options']['display'])) {
		$external=parse_ini_file($remote['options']['display'],true);
	}
	elseif (array_key_exists($remote['options']['display'],$lang['lang'])) {
		switch ($remote['options']['display']) {	
			case 'en':
			case 'el':
			break;
			default:
			$external['external']['lang']=$remote['options']['display'];
			$external['external']['display']=$lang['lang'][$remote['options']['display']];
			foreach ($lang as $key => $val) {
				if ($key!="lang"){
					$external[$key][$external['external']['lang']]=$lang[$key][$external['external']['lang']];
				}
			}
			break;
		}
	}
	if ((isset($_POST['submit-form'])) && (!empty($remote))) {
		foreach ($_POST as $key => $val) {
			if (!empty($val)) {
				if (($key!="lang") && ($key!="display")){
					$external[$key][$_POST['lang']]=$_POST[$key];
				}
				else
				{
					$external['external'][$key]=$_POST[$key];
				}
			}
		}
		$remote['options']['display']=$_POST['lang'].'.lng';
		write_ini_file($_POST['lang'].'.lng',$external);
		write_ini_file('remote.cfg',$remote);
		if (isset($_POST['pastebin'])) {							
			$translation_text = var_ini($external);
			$api_dev_key = '0f98bdaf00df8de76452dd2164e30914';
			$api_paste_code = urlencode($translation_text);
			$api_paste_private = '2';
			$api_paste_name = $_POST['lang']._.rand(0,10000);
			$api_paste_expire_date = 'N'; 
			$api_paste_format = 'text'; 
			$api_user_key = '2203db412c54bee6df4584addb58c511';
			$api_paste_name = urlencode($api_paste_name); 
			$url = 'http://pastebin.com/api/api_post.php'; 
			$ch = curl_init($url); 
			curl_setopt($ch, CURLOPT_POST, true); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'api_option=paste&api_user_key='.$api_user_key.'&api_paste_private='.$api_paste_private.'&api_paste_name='.$api_paste_name.'&api_paste_expire_date='.$api_paste_expire_date.'&api_paste_format='.$api_paste_format.'&api_dev_key='.$api_dev_key.'&api_paste_code='.$translation_text.''); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_VERBOSE, 1); 
			curl_setopt($ch, CURLOPT_NOBODY, 0); 
			$response = curl_exec($ch); 
			$remote['options']['pastebin']="1";
			write_ini_file('remote.cfg',$remote);
		}
		else
		{
			$remote['options']['pastebin']="";
			write_ini_file('remote.cfg',$remote);
		}
		?>
		<script>window.open("index.php","_self");</script>
		<?php
	}
	?>
	<style type="text/css">
		@font-face {
			font-family: "NotoSans";
			src: url("css/NotoSans-Regular.ttf");
			font-weight: 400;
		}
		@font-face {
			font-family: "NotoSans";
			src: url("css/NotoSans-Bold.ttf");
			font-weight: 700;
		}
		* {
			font-family: 'NotoSans';
		}		
		body 
		{ 
			color: #fff; 
			background:url('img/raspberry/background.png'); 
			background-position: top center;  
			background-color:#99004c;
			min-width:300px;
		}
		*:not(input):not(textarea) {
			-webkit-touch-callout:none;
			-webkit-user-select:none;
			-moz-user-select:none;
			-ms-user-select:none;
			user-select:none;
			-ms-touch-select:   none;
			-ms-touch-action:   none;
			-webkit-touch-callout: none;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
		}    
		img {
			pointer-events:none;
		}		
		a { 
			cursor: pointer;
		}
		p {
			text-align:center; 
			padding-bottom:12px; 
			padding-top:12px; 
		}
		:focus {
			outline-color: transparent !important;
			outline-style: none !important;
		}
		#grey_layer {
			background-color:#1c1c1c;
			padding-right:4px;
			padding-left:4px; 
			border-top: 4px solid #4c9900;
			border-bottom: 4px solid #626262;	
		}
		#element_style {
			background:#1c1c1c;
			color:white;
			text-align: center; 
			padding-top: 4px;
			padding-bottom: 4px;
			border-bottom: 1px solid #505050;		
		}
		#header_style {
			max-width:300px;
			background-color: #1c1c1c;
			text-align: center; 
			color: #fafafa; 
			padding-top: 6px;
			padding-bottom: 6px;
			width:100%;
			border-bottom: 2px solid #4c9900;
			letter-spacing:1px;	
		}
		#body_style {
			max-width:500px;
		}
		#table_style {
			max-width:300px;
		}
		.submit button	{
			max-width:100%;
			height: 50px;
			text-align:center;
			background-color: #99004c;
		}
		.main_input p {				
			padding-bottom:12px; 
			padding-top:12px; 	
		}
		.main_input input {
			display: inline-block;
			margin: 0;
			width: 100%;
			height: 50px;
			font-size: 16px;
			padding: 8px;
			border:1px solid #404040;
			border-radius:2px;
			transition: border 0.3s;
			background: #1c1c1c;
			color:white;
			font-weight:400;
			text-align:center;
		}		
		.main_input input:focus,
		.main_input input.focus  {
			border: 1px solid gray;
		}
		.main_input input:hover,
		.main_input input.hover  {
			border: 1px solid gray;
		}
		.main_input input:disabled {
			border-color:#262626;
		}
		.main_input input:disabled:hover {
			border-color:#262626;
		}
		.submit-trans button{
			position: fixed;
			width: 100%;
			height: 50px;
			left:0;
			right:0;
			margin:0 auto;
			cursor:pointer;
			text-align:center;
			background-color: #4c9900;
			border:0px;
			color:white;
			font-size:18px;
			font-family:"Verdana";
			bottom:0px;
			font-weight:bold;
		}
		#save:disabled {
			display:none;
		}
		button.trans_back  {
			background:none;	
			border:0;
			cursor:pointer;
			width:60px;
			height:60px;
			font-size:14;
			text-align:center;
			transition:all linear 0.15s;
			border-radius:50px;
		}
		button.trans_back:hover  {
			background:#303030;	
		}
	</style>
</head>
<body>  
	<center>
		<br>
		<h2>
			<div id="element_style" class="settings" style="opacity: 0.9;background-color:#99004c; padding:8px; border:0;">TRANSLATION</div>
		</h2>
		<div id="grey_layer">
			<div>
				<br>
				<button onclick="window.open('index.php','_self');" class="trans_back"><img src="img/raspberry/left_arrow.png" style="height:40;width:40;"></img></button>

				<form enctype="multipart/form-data" onkeypress="return event.keyCode != 13;" method="post" action="translate.php">
				<br>					
				<table id="table_style">
						<tr>
							<td>
								<h3 style="text-align:center;">Your language prefix</h3>
								<div class="main_input" id="">
									<p>
										<input name="lang" type="text" id="lang" placeholder="ex. el" value="<?php if ((isset($external)) && (!empty($external['external']['lang']))) { echo $external['external']['lang'];}else {echo "";}?>">
									</p>
								</div>
								<h3 style="text-align:center;">Your language in english</h3>
								<div class="main_input" id="">
									<p>
										<input class="fields" name="display" type="text" placeholder="ex. greek, ελληνικά" value="<?php if ((isset($external)) && (!empty($external['external']['display']))) { echo $external['external']['display'];}else {echo "";}?>" <?php if (empty($external['external']['display']['lang'])) { echo "disabled"; }?>>
									</p>
								</div>
								<div id="element_style" style="width:98%; max-width:500px; background-color:#404040; text-align:center; padding-top:16px; padding-bottom:16px;font-style:italic;border:0;">
									<h4>Translate what you see and make your own translation file. You can save and continue back later. A copy of this file will paste to my pastebin.com account for the porpose of this project. You can stop this unchecking the following box.</h4><h4><input type="checkbox" name="pastebin" value="" id="pbin" <?php if (($remote['options']['pastebin']=="1") || (!isset($remote['options']['pastebin']))) { echo " checked"; } ?>>&nbsp<?php echo 'Paste to pastebin.com';?></h4>
								</div>
								<br>
								<?php 
								unset($lang['lang']);
								unset($point);
								foreach (array_keys($lang) as $key) {
									?>
									<h4>
										<div id="element_style" style="width:98%;"><?php echo $lang[$key]['en'];?></div>
									</h4>
									<div class="main_input" id=""  >
										<p>
											<input class="fields" id="<?php echo $key;?>" name="<?php echo $key;?>" type="text" placeholder="<?php echo $key; ?>" value="<?php if ((isset($external)) && (!empty($external[$key][$external['external']['lang']]))) { echo $external[$key][$external['external']['lang']];}else {echo "";}?>" <?php if (empty($external['external']['display']['lang'])) { echo "disabled"; }?>>					
										</p>
									</div>
									<?php
									if ((isset($external['external']['lang'])) && (empty($external[$key][$external['external']['lang']])) && (!isset($point))) {
										$point=$key;
									}
								}
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="submit-trans"><button id="save" name="submit-form" type="submit" disabled>SAVE</button></div>
	</form>
	<?php 
	unset($external);
	if (isset($point)) {
		?>
		<script>var point=<?php echo json_encode($point);?>;</script>
		<?php
	}
	?>
	<script>
		$(document).ready(function() {

			$('input').attr({
				autocomplete: 'off',
				//autocorrect: 'off',
				autocapitalize: 'off',
				//spellcheck: 'false'
			});
			if($.trim($('#lang').val()) != '') {
				$('input').not('#lang').prop('disabled', false);	
			}
			else {
				setTimeout(function() { $('#lang').focus(); }, 500);
			}
			$('input').on('change keyup paste input propertychange', function(e) {

				if($.trim($('#lang').val()) == '') {
					$('#save').prop('disabled', true);
					$('input').not('#lang').prop('disabled', true);
				}
				else {
					$('#save').prop('disabled', false);	
					$('input').not('#lang').prop('disabled', false);
					if (e.which === 13) {
						var length = $(":input[type=text]").length;
						var i=$('input').index(this);
						while (i<=length) {
							if ($('input[type=text]').eq(i).val()== '') {
								$('input[type=text]').eq(i).focus();
								break;
							}
							if (i==length) {
								$("#save").click();
								break;
							}
							else {
								i++;
							}
						}
					}	
				}
			});
			if (point!=null) {
				setTimeout(function() { $("#" + point).focus(); }, 500);
			}
		});
	</script>
</body>
</html>
