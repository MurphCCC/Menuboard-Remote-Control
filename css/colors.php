<style>
	<?php 
	if ((theme!='terminal') || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)) {
		?>
		@font-face {
			font-family: "Comfortaa";
			src: url("css/Comfortaa-Regular.ttf");
			font-weight: 400;
		}
		@font-face {
			font-family: "Comfortaa";
			src: url("css/Comfortaa-Bold.ttf");
			font-weight: 700;
		}
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
		<?php
	}
	else {
		?>
		@font-face {
			font-family: "Cousine";
			src: url("css/Cousine-Regular.ttf");
			font-weight: 400;
		}
		@font-face {
			font-family: "Cousine";
			src: url("css/Cousine-Bold.ttf");
			font-weight: 700;
		}
		<?php
	}
	?>
	*:not(.main){
		<?php 
		if ((theme!='terminal') || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false))  {
			if (($remote['options']['font']=="Comfortaa") && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') === false)) {
				?>
				font-family: 'Comfortaa';
				<?php
			} 
			else {
				?>
				font-family: 'NotoSans';
				<?php
			}
		}
		else {
			?>
			font-family: 'Cousine'!important;
			<?php
		}
		?>
	}
	body {
		background-position: <?=position?>;
		background-size: <?=size?>;
		background-repeat: <?=repeat?>;
	}
	#file_select.main_select select {
		background:url('img/<?=theme?>/down.png') no-repeat;  
		background-size: 14px;
		background-position: right 14px top 20px;
	}
	#config_select.main_select select {
		background:url('img/<?=theme?>/down.png') no-repeat; 
		background-size: 14px;
		background-position: right 14px top 20px;
	}
	label.purelabel,
	label.hlslabel {
		background-image:url('img/<?=theme?>/check_box.png');
	}
	.main.play,
	.main.bb,
	.main.ff {
		background:<?=group53?>;
	}
	.main_input.url_address input {
		box-shadow:<?=group54?>;
	}
	::selection {
		color:<?=group1?>;
	}
	button.trans,
	.button button, 
	.button a,
	.round-button, 
	.main_select select,
	.links.element,
	#browse_button, 
	#don, 
	#config_input.main_input input, 
	#config_select.main_select select, 
	#black p, 
	#navi button,
	#file_select,select:focus > option:checked,
	#config_select select:focus > option:checked,
	#hls_check input:checked + span,
	.backarea,
	#header_style,
	#body_style,
	#osd
	{
		color:<?=group1?>;
	}
	::-webkit-input-placeholder {
		color:<?=group1c?>;
	}
	:-moz-placeholder { 
		color:<?=group1c?>;
		opacity:1;
	}
	::-moz-placeholder {  
		color:<?=group1c?>;
		opacity:1;
	}
	input:-ms-input-placeholder {
		color:<?=group1c?>!important; 
	}
	::-webkit-selection {
		color:<?=group1?>;
	}
	::-moz-selection {
		color:<?=group1?>;
	}
	::selection {
		color:<?=group1?>;
	}
	.main_input.url_address input {
		color:<?=group1b?>;
	}
	button.submit,
	button.remove {
		color:<?=group1d?>;
	}
	button.submit:hover,
	button.remove:hover {
		color:<?=group1e?>;
	}
	.main_input textarea,
	.history,
	.faq p,
	#element_style,
	#file_select.main_select select option,
	#inner_back,
	#time,
	.switchfont,
	.element,
	#time,
	input.purebox + label.purelabel,
	input.hlsbox + label.hlslabel
	{
		color:<?=group2?>;
	}
	.check_options input +span {
		color:<?=group2b?>;
	}
	.popup-inner::-webkit-scrollbar {
		color:<?=group2?>;
	}
	.popup-inner::-webkit-scrollbar-track {
		color:<?=group2?>;
	}
	.history:hover,
	#hls_check label {
		color:<?=group3?>;
	}
	button.wrap_button:hover:not(.remote) {
		color:<?=group3b?>;
	}
	.top:hover,
	button.wrap_button,
	option:checked,
	#hls_check {
		color:<?=group4?>;
	}
	.check_options input:checked +span {
		color:<?=group4b?>;
	}
	z-html-cellhighlighttext {
		color:<?=group4?>;
	}
	#timeline::-ms-track {
		color:<?=group4?>;
	}
	#timeline::-ms-fill-lower {
		color:<?=group4?>;
	}
	#timeline::-ms-fill-upper {
		color:<?=group4?>;
	}
	#timeline::-ms-tooltip {
		color:<?=group4?>;
	}
	#playtime,
	.wrap_button.remote,
	.email,
	#console {
		color:<?=group5?>;
	}
	#timeline::-moz-range-thumb {
		color:<?=group6?>;
	}
	#timeline::-webkit-slider-thumb {
		color:<?=group6?>;
	}
	#timeline::-ms-thumb {
		color:<?=group6?>;
	}
	#timeline::-ms-ticks {
		color:<?=group7?>;
	}
	#timeline::-ms-ticks-after {
		color:<?=group7?>;
	}
	#timeline::-ms-ticks-before {
		color:<?=group7?>;
	}
	#navi button:hover {
		color:<?=group7?>;
	}
	.dark:hover{
		color:<?=group7b?>!important;
	}
	body {
		background-color:<?=group8?>;	
	}
	.round-button-circle.halt {
		background-color:<?=group8b?>;
	}
	#don,
	#browse_button,
	button.trans,
	.radio-custom:checked + .radio-custom-label:before,
	.round-button-circle,
	.img.round-button-circle,
	.button a:hover
	{
		background-color:<?=group9?>;
	}
	.button button:hover,
	.button input:hover,
	.button a:hover {
		background-color:<?=group9b?>;
	}
	#timeline::-moz-range-thumb {
		background-color:<?=group9c?>;
	}
	#timeline::-webkit-slider-thumb{
		background-color:<?=group9c?>;
	}
	#timeline::-ms-thumb{
		background-color:<?=group9c?>;
	}
	::selection{
		background-color:<?=group9?>;
	}
	::-webkit-selection{
		background-color:<?=group9?>;
	}
	::-moz-selection {
		background-color:<?=group9?>;
	}
	button.submit,
	button.submit:disabled,
	button.remove,
	.main_input textarea,
	#check_setbtn,
	#config_input.main_input input,
	#config_select.main_select option,
	.popup-inner {
		background-color:<?=group10?>;	
	}
	#file_select.main_select select option {
		background-color:<?=group11f?>;
	}
	select:focus::-ms-value {background-color: transparent; color:<?=group1?>;}
	select::-ms-value {
		background: none!important;
	}
	button.gray {
		background-color:<?=group11e?>;
	}
	.wrap_button.remote
	{
		background-color:<?=group10b?>;
	}
	#gray_layer{
		background:<?=group11?>;
		background-position: <?=group11b?>;
		background-size: <?=group11c?>;
		background-repeat: <?=group11d?>;
		opacity:1;
	}
	#timeline::-ms-tooltip {
		background-color:<?=group11?>;
	}
	#timeline:disabled::-ms-thumb {
		background-color:<?=group11?>;
	}
	.button button,
	.button a,
	button.wrap_button,
	.wrapper_bar,
	.wrapper_bar,
	.top:hover{
		background-color:<?=group12?>;
	}
	.line-separator:hover {
		background-color:<?=group12c?>!important;
	}
	.check_options input,
	.check_options span,
	.check_options label{
		background-color:<?=group12b?>!important;
	}
	button.trans_back:hover,
	#config_select select:focus > option:checked {
		background-color:<?=group13?>;
	}
	.backarea,
	#body_style {
		background-color:<?=group14?>;
	}
	.backarea.apptitle {
		background-color:<?=group14b?>;
	}
	.backarea button:hover{
		color:<?=group3c?>!important;
	}
	.backarea a:hover{
		color:<?=group3c?>!important;
	}
	.line-separator {
		background-color:<?=group15?>!important;
		opacity:1;
	}
	#file_select select:focus > option:checked,
	.main {
		background-color:<?=group16?>;
	}
	.halt_select select option,
	.checkbox-custom + .checkbox-custom-label:before, .radio-custom + .radio-custom-label:before {
		background-color:<?=group17?>;
	}
	.marquee {
		background-color:<?=group18?>;
	}
	#osd,
	#black {
		background-color:<?=group19?>;
	}
	.dark {
		background-color:<?=group19?>;
	}
	#timeline::-ms-ticks-after {
		background-color:<?=group19?>;
	}
	#timeline::-ms-ticks-before {
		background-color:<?=group19?>;
	}
	#timeline::-ms-ticks {
		background-color:<?=group19?>;
	}
	#timeline::-moz-range-track {
		background-color:<?=group20?>;
	}
	#timeline::-webkit-slider-runnable-track {
		background-color:<?=group20?>;
	}
	#timeline::-ms-track {
		background-color:<?=group20?>;
	}
	#timeline::-ms-fill-upper {
		background-color:<?=group8?>;
	}
	#timeline:disabled::-moz-range-thumb {
		background-color:<?=group21?>;
	}
	#timeline:disabled::-webkit-slider-thumb {
		background-color:<?=group21?>;
	}
	#timeline::-ms-fill-lower {
		background-color:<?=group21?>;
	}
	.wrapper:hover::-webkit-scrollbar-thumb {
		background-color:<?=group22?>;
	}
	.wrapper_bar:hover::-webkit-scrollbar-thumb {
		background-color:<?=group22?>;
	}
	.popup-inner::-webkit-scrollbar {
		background-color:<?=group23?>!important;
	}
	.popup-inner::-webkit-scrollbar-thumb {
		background-color:<?=group23b?>!important;
	}
	.kodi {
		background-color:<?=group19b?>;
	}
	.vnc {
		background-color:<?=group19c?>;
	}
	.custom {
	}
	#file_select.main_select select:hover {
		background-color:<?=group24?>;
		-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=<?=group25?>00, endColorstr=<?=group25?>00)";
		filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=<?=group26?>00, endColorstr=<?=group26?>00);
	}
	#file_select.main_select select{
		background-color:<?=group28?>;
		-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=<?=group29?>00, endColorstr=<?=group29?>00)";
		filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=<?=group30?>00, endColorstr=<?=group30?>00);
	}
	.main_input.url_address input {
		background:	<?=group31?>;
		background: -webkit-linear-gradient(-0deg, <?=group31?>, <?=group32?>);
		background: -moz-linear-gradient(-0deg, <?=group31?>, <?=group32?>);
		-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=<?=group31?>00, endColorstr=<?=group32?>00)";
		filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=<?=group31?>00, endColorstr=<?=group32?>00);
	}
	.wrapper_bar-nav:hover::-webkit-scrollbar-thumb {
		background-image: -webkit-linear-gradient(left, <?=group33?>, <?=group34?>, <?=group33?>);
	}	
	#config_input.main_input input,
	#config_select.main_select select {
		border-color:<?=group38?>;
	}
	#don,
	button.trans,
	#header_style,
	#browse_button {
		border-color:<?=group39?>;
	}
	#element_style,
	button.remove,
	#gray_layer {
		border-color:<?=group40?>!important;
	}
	button.submit,
	button.remove {
		border-color:<?=group40b?>!important;
	}
	button.submit:hover,
	button.remove:hover{
		border-color:<?=group40c?>!important;
	}
	.main {
		border-color:<?=group41?>;
	}
	#general_set,
	#header_style.faq,
	.round-button-circle,
	.round-button-circle.halt,
	.img.round-button-circle,
	#header_style.faq_header {
		border-color:<?=group42?>;
	}
	.main_input input,
	.main_input textarea {
		border-color:<?=group43?>;
	}
	.main_input.url_address input {
		border-color:<?=group43?>;
	}
	#config_select.main_select select:hover:not(disabled),
	#config_input.main_input input:focus,
	#config_input.main_input input.focus,  
	#config_input.main_input input:hover,
	#config_input.main_input input.hover  {
		border-color:<?=group44?>;
	}
	.checkbox-custom + .checkbox-custom-label:before, .radio-custom + .radio-custom-label:before,
	.checkbox-custom:focus + .checkbox-custom-label, .radio-custom:focus + .radio-custom-label {
		border-color:<?=group45?>;
	}
	.subtitle_line{
		border-color:<?=group46?>!important;
	}
	.main_select select {
		outline-color:<?=group47?>;
	}
	.checkbox-custom:focus + .checkbox-custom-label, .radio-custom:focus + .radio-custom-label {
		outline-color:<?=group48?>;
	}
	.round-button-circle.halt,
	.round-button-circle.vol,
	.img.round-button-circle
	{
		color:<?=group49?>;
	}
	button.wrap_button:hover:not(.remote) {
		background-color:<?=group55?>!important;
	}
	.top,
	.history,
	.history:hover {
		background-color:transparent;
	}
	<?php 
	if (theme=='terminal') {
		?>
		#file_select select{
			background:0!important;
			border:0;
			width:100%!important;
			margin-left:-15!important;
		}
		.main_input.url_address input{
			margin-top:-20!important;
			width:98%!important;
		}
		#timeline:disabled::-webkit-slider-runnable-track {
			border-style:solid!important;
			background:transparent;
			color:#4c9900;
		}	
		#timeline::-webkit-slider-runnable-track {
			border-style:dashed!important;
			background:transparent;
			color:#4c9900;
		}
		#timeline::-webkit-slider-thumb {
			border-radius:0;
			margin:0;
			margin-top:-12;
			width:15;
		}
		#timeline::-moz-range-track {
			border-style:solid!important;
			background:transparent;
			color:#4c9900;		
			height:2;
		}
		#timeline:disabled::-moz-range-track {
			border-style:solid!important;
			background:transparent;
			color:#4c9900;
			height:2;
		}	
		#timeline::-moz-range-thumb {
			border-radius:0;
			width:15;	
		}
		#console,
		#time {
			margin-top:8;
		}
		.backarea:not(.apptitle) {
			border:2px solid #4c9900;
		}
		.line-separator {	
			border:1px solid #4c9900;
			background-color:#000!important;
			width:299px;
		}
		.line-separator:hover {	
			background-color:#4c9900!important;
		}
		*:not(.main):not(.round-button-circle):not(#navi) {
			border-radius:0px!important;
		}
		#config_input input,
		#config_select  {
			border:1!important;
		}
		.popup-inner {
			border-radius:0px!important;
		}
		.popup-inner::-webkit-scrollbar {
			border:0;
			border-right:initial;
			border-radius: 0px!important;
			height:3px!important;
			background-color:#000!important;
		}
		.popup-inner::-webkit-scrollbar-thumb {
			border:1px solid #4c9900;
			background-color:#000!important;
			border-radius: 0px!important;
		}
		button.wrap_button  {
			transition:none;
		}
		<?php
	}
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) {
		?>
		#timeline {
			margin-top:12; 
			margin-left:1.2%;		
		}
		#console {
			margin-top:-30;
			margin-left:1.2%;
		}
		#time{
			margin-top:-30;
			margin-right:0.8%;
		}
		<?php 
	}
	?>
</style>
