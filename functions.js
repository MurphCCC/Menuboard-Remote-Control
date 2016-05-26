/* INDEX */

$(function() {
	
	$('[data-popup-open]').on('click', function(e)  {
		var targeted_popup_class = jQuery(this).attr('data-popup-open');
		$('[data-popup="' + targeted_popup_class + '"]').fadeIn(0);
		e.preventDefault();
		$('body').not('.popup-inner').css({
			"overflow":"hidden",
			"position": "fixed"
		});
	});
	
	
	$('[data-popup-close]').on('click', function(e)  {
		$('body').not('.popup-inner').css({
			"overflow":"auto",
			"position": "relative"
		});
		var targeted_popup_class = jQuery(this).attr('data-popup-close');
		$('[data-popup="' + targeted_popup_class + '"]').fadeOut(0);
		e.preventDefault();
	});
});

function drop(area) {

	if ((!area.style.opacity) || (area.style.opacity=='0')){
		slideDown(area);
		if (area==general_set) {
			$('#gray_layer').css({"border-top":"0"});
			$("#menu").html("-");
		}
	}
	else {
		slideUp(area);
		if (area==general_set) {
			$('#gray_layer').css({
				"border-top":"2px solid",
				"border-color":constants['group40']
			});
			$("#menu").html("+");
		}
	}

}


function slideDown( elem ) {
	$(elem).css({'opacity': '1'});
	$(elem).css({'max-height': '100000px'});
	
}

function slideUp( elem )
{
	$(elem).css({'max-height': '0'});
	once( 1, function ()
	{
		$(elem).css({'opacity': '0'});
	} );    
}

function once( seconds, callback ) {
	var counter = 0;
	var time = window.setInterval( function () {
		counter++;
		if ( counter >= seconds )
		{
			callback();
			window.clearInterval( time );
		}
	}, 1000 );
}

$(document).ready(function() {

	if ((navigator.userAgent.indexOf("Firefox") != -1 ) || ((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true ))) {
      $('.wrapper_bar,.wrapper_bar-nav,.wrapper').addClass('hidescrollbars');
    }  


	$('#hidescrollbars').click(function(){
		if ($('.wrapper_bar,.wrapper_bar-nav,.wrapper').hasClass('showscrollbars')) {
			$('.wrapper_bar,.wrapper_bar-nav,.wrapper').removeClass('showscrollbars').addClass('hidescrollbars');
		}
		else {
			$('.wrapper_bar,.wrapper_bar-nav,.wrapper').removeClass('hidescrollbars').addClass('showscrollbars');
		}
	});

	$('.purelabel').on("mouseenter",function(){
			$('.purebox + .purelabel').css({
				"background-position": "0 -24px"
			});
	});
	$('.purelabel').on("mouseleave",function(){
		if ($('#pure').is(':not(:checked)')) {
			$('.purebox + .purelabel').css({
				"background-position": "0 0px"
			});
		}
	});

	$('#pure').on("change", function() {

		if ($('#pure').is(':not(:checked)')) {
			if (isTouchSupported) {
				$('.purebox + .purelabel').css({
					"background-position": "0 0px"
				});
			}
		}
		else {
			if (isTouchSupported) {
				$('.purebox + .purelabel').css({
					"background-position": "0 -24px"
				});
			}			
		}
	});

	$('#timeline').on("mousemove", function(e) {
		if ($('#timeline').is(':enabled')) {
			if(e.which==1)
			{
		 	    var sec_num = parseInt($(this).val(), 10); // stackoverflow.com/questions/6312993
		 	    var hours   = Math.floor(sec_num / 3600);
		 	    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
		 	    var seconds = sec_num - (hours * 3600) - (minutes * 60);

		 	    if (hours   < 10) {hours   = "0"+hours;}
		 	    if (minutes < 10) {minutes = "0"+minutes;}
		 	    if (seconds < 10) {seconds = "0"+seconds;}
		 	    var time  = hours+':'+minutes+':'+seconds;

		 	    $('#console').html(seek + time);
		 	}
		 }
		});
	

	$('#timeline').on("change", function() {
			ajax('seekto',pos,type,$(this).val());
	});

	/* THEME */

	$('#themes, #pure').change(function(){
		$('#submit-general').prop('disabled', false);
	});

	/* FILE SELECTION */

	$('#selected').change(function(){
		stop=true;
	});
	
	/* SCRIPTS */

	$('#script_edit').on('change keyup paste input propertychange', function() {

		$('#remove-script').prop('disabled', false);
		$('#submit-scripts').prop('disabled', true);

		var script = $('#script_edit').val().split("|||");

		$('#script_pre').val(script[0]);
		$('#script_sh').val(script[2]);
		$('#colors').val(script[1]);

	});

	$('#script_pre,#script_sh').on('change keyup paste input propertychange', function() {

		$('#remove-script').prop('disabled', true);

		if(($.trim($('#script_pre').val()) == '') ||
			($.trim($('#script_sh').val()) == '')) {

			$('#submit-scripts').prop('disabled', true);

	}
	else {

		$('#submit-scripts').prop('disabled', false);

	}

});

	$('#colors').change(function(){
		
		$('#remove-script').prop('disabled', true);
		
		if(($.trim($('#script_pre').val()) == '') ||
			($.trim($('#script_sh').val()) == '')) {

			$('#submit-scripts').prop('disabled', true);

	}
	else {

		$('#submit-scripts').prop('disabled', false);

	}

});

	/* EXECUTION */

	$('#shell_run').on('change keyup paste input propertychange', function() {
		if($.trim($('#shell_run').val()) == '') {
			$('#submit-run').prop('disabled', true);
		}
		else {
			$('#submit-run').prop('disabled', false);
		}
	});

	var exec = function() {

		if ($('#sh_users').val() == 'default') {
			var cmd = $('#shell_run').val();
		}
		else if ($('#sh_users').val() == 'root') {
			var cmd = 'sudo '+$('#shell_run').val();
		}
		else {
			var cmd = 'sudo -u '+$('#sh_users').val()+' '+$('#shell_run').val();
		}
		ajax('run','','',cmd);

	};

	$('#submit-run').click(exec);
	
	$("#shell_run").keypress(function(event) {
		if (event.which == 13) {
			$('#shell_run').blur();
			$('[data-popup="run"]').fadeOut(350);
			exec();
		}
	});
	
	/* NEW LOGO */

	$('#channel_list').change(function(){

		$("#input_dimension").css({"display":"initial"});
		$('#dimension_width,#dimension_height,#submit-logo').prop('disabled', false);
		$('#dimension_width').val("50");
		$('#dimension_height').val("30");

	});
	
	/* LOGO UPLOAD BUTTON */

	$('#browse').change(function(){

		$('#dimension').empty().prop('disabled',true);
		$('#channel_list').prop('disabled',false);
		$("#select_dim,#input_name").css({"display":"none"});
		$("#select_service,#input_dimension").css({"display":"initial"});

	});

	/* SELECT LOGO DIMENSION */

	$('#dimension').change(function(){

		$('#browse_button,#submit-logo').prop('disabled',true);
		$('#remove-logos,#dimension_width,#dimension_height,#dimension_name').prop('disabled',false);
		$("#browse,#browse_button,#submit-logo").css({"display":"none"});
		$("#input_name,#input_dimension,#remove-logos").css({"display":"initial"});

		var elem_dim = $('#dimension').val().split("|");

		$('#dimension_width').val(elem_dim[1]);
		$('#dimension_height').val(elem_dim[2]);
		$('#dimension_name').val(elem_dim[0]);

	});

	$('#dimension_width,#dimension_height,#dimension_name').on('change keyup paste input propertychange', function() {

		$('#remove-logos').prop('disabled', true);
		$("#remove-logos").css({"display":"none"});

		if 	(($.trim($('#dimension_width').val()) == '') ||
			($.trim($('#dimension_height').val()) == '')) {

			$('#submit-logo').prop('disabled', true);
		$("#submit-logo").css({"display":"none"});

	}
	else {
		$('#submit-logo').prop('disabled', false);
		$("#submit-logo").css({"display":"initial"});
	}
});

	/* WEBTV SELECTION */

	$('#webtv').change(function(){

		$('#remove-webtv').prop('disabled',false).css({"display":"initial"});
		$('#submit-webtv').prop('disabled',true).css({"display":"none"});
		$('#hls').prop('disabled',false);

		var elem_web = $('#webtv').val().split("||");

		$('#webtv_name').val(elem_web[1]);
		$('#webtv_url').val(elem_web[2]);

		if (elem_web[3]!=null) {
			$('#hls').prop('checked', true);
			$('.hlsbox + .hlslabel').css({
				"background-position": "0 -24px"
			});
		}
		else {
			$('#hls').prop('checked', false);
			if (isTouchSupported) {
				$('.hlsbox + .hlslabel').css({
					"background-position": "0 0px"
				});
			}
		}
	});

	$('#webtv_name,#webtv_url,#hls').on('change keyup paste input propertychange', function() {

		$('#remove-webtv').prop('disabled', true);
		$('#hls').prop('disabled',false);

		if ($('#hls').is(':not(:checked)')) {
			if (isTouchSupported) {
				$('.hlsbox + .hlslabel').css({
					"background-position": "0 0px"
				});
			}
		}
		else {
			if (isTouchSupported) {
				$('.hlsbox + .hlslabel').css({
					"background-position": "0 -24px"
				});
			}			
		}

		$("#remove-webtv").css({"display":"none"});

		if 	(($.trim($('#webtv_name').val()) == '') ||
			($.trim($('#webtv_url').val()) == '')) {

			$('#submit-webtv').prop('disabled', true);
		$("#submit-webtv").css({"display":"none"});

		}
		else {
			$('#submit-webtv').prop('disabled', false);
			$("#submit-webtv").css({"display":"initial"});
		}
	});
	$('.hlslabel').on("mouseenter",function(){
		if ($('#hls').is( ":enabled" )) {
			$('.hlsbox + .hlslabel').css({
				"background-position": "0 -24px"
			});
		}
	});
	$('.hlslabel').on("mouseleave",function(){
		if (($('#hls').is(":enabled")) && ($('#hls').is(':not(:checked)'))) {
			$('.hlsbox + .hlslabel').css({
				"background-position": "0 0px"
			});
		}
	});


	$('#addnewlink').click(function(){
		setTimeout(function() { $('#webtv_url').focus(); }, 500);
	});

	$('#reallocate').click(function(event){
		event.preventDefault();
		if (confirm(dvb_sync_alert)) {
			ajax('reallocate');
		} else {
		    //
		}
		$('[data-popup="tv"]').fadeOut(350);
		
	});

	/* DVB CHANNELS TVHEADEND */

	$('#dvbtv').change(function(){

		$('#remove-tv,#dvb_name').prop('disabled', false).css({"display":"initial"});
		$('#submit-tv').prop('disabled', true).css({"display":"none"});

		var elem_dvb = $('#dvbtv').val().split("||");

		$('#dvb_name').val(elem_dvb[1]);

	});

	$('#dvb_name').on('change keyup paste input propertychange', function() {

		$('#remove-tv').prop('disabled', true).css({"display":"none"});

		if 	($.trim($('#dvb_name').val()) == '') {

			$('#submit-tv').prop('disabled', true).css({"display":"none"});

		}
		else {
			$('#submit-tv').prop('disabled', false).css({"display":"initial"});

		}
	});

	/* CLEAR HISTORY LINK */

	$('#clr').click(function(){
		$(".wrapper a").not("#clr").remove();
		$("#clr").css({"opacity":"0"});
		$(".wrapper").hide();
	});

	/* SELECT USER */

	$('#users,#mdns').on('change keyup paste input propertychange', function() {
		$('#submit-general').prop('disabled', false);
	});

	/* SELECT LOCALIZATION */

	$('#enc,#lang,#font-size,#fonts,#local,#internet').change(function(){
		$('#submit-localization').prop('disabled', false);
	});

	/* MEDIA PATHS TEXT INPUT */

	$('#text').on('change keyup paste input propertychange', function() {
		$('#submit-paths').prop('disabled', false);
	});

	$('#mediapaths').click(function(){

		var eol = $("#text").val();
		setTimeout(function() { $('#text').focus().val("").val(eol); }, 500);
		
	});
	$('#file_select').on('change', function(){
		
	});
	/* TVHEADEND CONFIGURATION */

	$('#username_tvheadend,#password_tvheadend,#port_tvheadend,#path').on('change keyup paste input propertychange', function() {

		$('#submit-tvheadend').prop('disabled', false).css({"display":"initial"});

		if 	(($.trim($('#port_tvheadend').val()) == '') ||
			($.trim($('#path').val()) == '')) {

			$('#submit-tvheadend').prop('disabled', true).css({"display":"none"});

	}
	else {

		$('#submit-tvheadend').prop('disabled', false).css({"display":"initial"});

	}

});

	/* KODI VNC CONFIGURATION */

	$('#scheme_kodi,#username_kodi,#password_kodi,#port_kodi,#port_kodi,#port_kodi,#port_kodi,#port_kodi,#scheme_vnc,#username_vnc,#password_vnc,#port_vnc').on('change keyup paste input propertychange', function() {

		$('#submit-link').prop('disabled', false).css({"display":"initial"});

		if 	(($.trim($('#scheme_kodi').val()) == '') &&
			($.trim($('#scheme_vnc').val()) == '')) {

			$('#submit-link').prop('disabled', true).css({"display":"none"});

	}
	else {

		$('#submit-link').prop('disabled', false).css({"display":"initial"});

	}

});

	/* ON PAGE LOAD */

	// FLIP

	/*$(window).on("orientationchange scroll",function(){
	  if(window.orientation == 0) // Portrait
	  {
	    $("#timeline").css({
	    	"width":"97%",
	    	"margin":"28 0 0 0"
	    });
	    $(".navigate").css({
	    	"display":"none"
	    });
	    $(".marquee").css({
	    	//"width": "98%",
	    	"height" : "65px",
	    	"bottom": "7",
	    	"background-color": "#101010",
	    	"right":"1%",
	    	"opacity":"0.9"
	    });
	  }
	  else // Landscape
	  {
	    $("#timeline").css({
	    	//"margin":"0",
	    	"margin-top":$(".marquee").width()*(15/100),
	    	//"margin-bottom":"auto"
	    	//"width":"100%",
	    	//"margin":"0"

	    });
	    $(".navigate").css({
	    	"display":"initial"
	    });
	    $("#console").css({
	    	"font-size":"20px",
	    	//"margin":"0"
	    });
	    $(".marquee").css({
	    	//"bottom": "auto",
	    	//"display":"block",
	    	//"right":"auto",
	    	//"left":"auto",
	    	//"width": "90%",
	    	"height" : "65%",
	    	"opacity":"1",
	    	"bottom": "15%",
	    	//"margin":"auto",
	    	//"margin": "auto",
	    	"background-color": "#000",
	    	"box-shadow":"0px 0px 1000px 350px #000"

	    });
	  }

	});*/



	$(window).bind('resize load', function() {
		if (!pure) {
			dimensions = document.getElementsByTagName('body')[0].clientWidth;
				if (dimensions<1024) {
					$('body').css('background-image','url(img/'+constants['theme']+'/background.png)');
				}
				else {
					$('body').css('background-image','url(img/'+constants['theme']+'/background-wide.png)');
				}
		}
	});

	$("#select_service,#input_name,#input_dimension").css({"display":"none"});

	$('input').attr({
		autocomplete: 'off',
		autocorrect: 'off',
		autocapitalize: 'off',
		spellcheck: 'false'
	});

	/* CSS HOVER EFFECTS & TOUCH DEVICES */

	// NAVIGATOR

	$('.volup').on('touchend mouseup', function(e){

		if (isTouchSupported) {
			e.preventDefault();
		}

		clearInterval(timeout);
	    if(scroll != true){
	        ajax('vol');
	    }
	    scroll=false;

	}).on('touchmove', function(){

	    clearInterval(timeout);
	    scroll = true;	

	}).on('touchstart mousedown', function(e){
		
		if (isTouchSupported) {
			e.preventDefault();
		}

		timeout = setInterval(function(){
			ajax('vol'); 
		}, 150);
	    scroll = false;

	});

	$('.voldown').on('touchend mouseup', function(e){

		if (isTouchSupported) {
			e.preventDefault();
		}

		clearInterval(timeout);
	    if(scroll != true){
	        ajax('vol_');
	    }
	    scroll=false;

	}).on('touchmove', function(){

	    clearInterval(timeout);
	    scroll = true;	

	}).on('touchstart mousedown', function(e){
		
		if (isTouchSupported) {
			e.preventDefault();
		}

		timeout = setInterval(function(){
			ajax('vol_'); 
		}, 150);
	    scroll = false;

	});

	$(".round-button-circle").on("touchstart mousedown",function(){
		$(this).not('.halt').css({
			"background":constants['group8c']
		});
		$(this).css({
			"border-color":constants['group42b']
		});
	});
	$(".round-button-circle").on("touchend mouseup",function(){
		$(this).not('.halt').css({
			"background":constants['group9']
		});
		$(this).css({
			"border-color":constants['group42']
		});
	});

	$(".round-button-circle").on("mouseenter",function(){
		$(this).not('.halt').css({
			"background":constants['group8c']
		});
		$(this).css({
			"border-color":constants['group42b']
		});
	});
	$(".round-button-circle").on("mouseleave",function(){
		$(this).not('.halt').css({
			"background":constants['group9']
		});
		$(this).css({
			"border-color":constants['group42']
		});
	});

	$(".halt").on("touchstart mousedown",function(){
		$(this).css({
			"background":constants['group52']
		});
	});
	$(".halt").on("touchend mouseup",function(){
		$(this).css({"background":constants['group8']});
	});
	$(".halt").on("mouseenter",function(){
		$(this).css({
			"background":constants['group52']
		});
	});
	$(".halt").on("mouseleave",function(){
		$(this).css({"background":constants['group8b']});
	});

	// BASIC NAVIGATOR

	var webtv = $(".webtv_channels button");
	var dvb = $(".dvb button");
	
	$('.ff').on('touchend mouseup', function(e){

		if (isTouchSupported) {
			e.preventDefault();
		}

		window.clearInterval(timeout);
		window.clearInterval(seek30);

		if(scroll != true){
			var end = new Date();
			end = end - longpress;	

			if ((arg!='idle') && (end<500)) {
					window.clearInterval(zap);
					zap = setTimeout(function(){
						if ((type=='webtv') || (type=='tv')) {
							if (type=='webtv'){
								setTimeout(function(){
									document.getElementById('osd').style.display='none';
								}, 600);								
								ajax('play',channel[0],'webtv');
							}
							else if (type=='tv') {
								setTimeout(function(){
									document.getElementById('osd').style.display='none';
								}, 600);
								ajax('play',channel[0],'tv');
							}
						}
						else if (type=='file') {
							ajax('play','','file');
						}
						i=0;
							
					}, 1000);
					
					if ((type=='webtv') || (type=='tv')) {
						if (type=='webtv'){
							var funct=webtv;
						}
						else if (type=='tv') {
							var funct=dvb;
						}

						for (var z=0;z<funct.length;z++) {
							var channel=funct[z].value.split("||");

							if (channel[0]==pos) {

								if (i==0) {
									i=z+1;
								}
								else {
									i++;
								}
								if (i==funct.length) {
									i=0;
								}
								channel=funct[i].value.split("||");
								document.getElementById('osd').style.display='initial';
								document.getElementById('osd').textContent=channel[1];
								if (i==0) {
									i=1;
								}
								break;
							}
						}	
					}
					else if (type=='file') {

						if ($("#selected option").length-2==$("#selected")[0].selectedIndex) {
							var selection=0;
						}
						else if ($("#selected option").length-1==$("#selected")[0].selectedIndex) {
							var selection=0;
						}
						else {
							var selection=($("#selected")[0].selectedIndex)+1;
						}
						$('#selected option')[selection].selected = true;

					}
			}
		}

		setTimeout(function(){

			$(".play").css({
				"margin-right":"0",
				"border-color":constants['group41'],
				"border-right":"0"
			});
			$(".ff").css({
				"border-color":constants['group41'],
				"border-left":"0"
			});
			$(".bb").css({
				"border-color":constants['group41']
			});
			$("#ff_img").attr("src","img/" + constants['theme'] + "/left_arrow.png").css({
				"width":"38",
				"height":"30"
			});	

		}, 100);

		scroll=false;

	}).on('touchmove', function(){

		clearInterval(act);
	    clearInterval(timeout);
	    clearInterval(seek30);
	    scroll = true;	

	}).on('touchstart mousedown', function(e){
		
		if (isTouchSupported) {
			e.preventDefault();
		}

		longpress= new Date(); 

		act = setTimeout(function(){

			$("#ff_img").attr("src","img/" + constants['theme'] + "/left_arrow_hovered.png");

			$(".ff").css({
				"border-color":constants['group44']
			});

			if (arg=='idle') {

					if ($.trim($('#url_address').val()) == '') {	
						if ($("#selected option").length-2==$("#selected")[0].selectedIndex) {
							var selection=0;
						}
						else if ($("#selected option").length-1==$("#selected")[0].selectedIndex) {
							var selection=0;
						}
						else {
							var selection=($("#selected")[0].selectedIndex)+1;
						}
						$('#selected option')[selection].selected = true;
					}
			}		

		}, 100);

		timeout = setTimeout(function(){

			if ((out[4]>0) && (arg!='idle')) {

				$("#ff_img").attr("src","img/" + constants['theme'] + "/seek30.png").css({
					"width":"33",
					"height":"25"
				});
				$(".play").css({
					"margin-right":"5",
					"border": "3px solid",
					"border-color":constants['group44'],
					"border-left":"0"
				});
				$(".ff").css({
					"border": "3px solid",
					"border-color":constants['group44']
					
				});
				$(".bb").css({
					"border-color":constants['group44'],
					"border-right":"0"
				});			
				webseek = out[4]-out[5];
				seek30 = setInterval(function(){
					if (webseek>40) {
						ajax('seek30');
						webseek=webseek-30;
					}
					else {
						webseek=0;
						window.clearInterval(seek30);
					}
					
				}, 500);	    		
			}

			else if (arg=='idle') {

				seek30 = setInterval(function(){

					if ($.trim($('#url_address').val()) == '') {

						if ($("#selected option").length-2==$("#selected")[0].selectedIndex) {
							var selection=0;
						}
						else if ($("#selected option").length-1==$("#selected")[0].selectedIndex) {
							var selection=0;
						}
						else {
							var selection=($("#selected")[0].selectedIndex)+1;
						}
						$('#selected option')[selection].selected = true;
					}
					
				}, 100);
			}
			
		}, 500);

		scroll = false;

	});

	$('.bb').on('touchend mouseup', function(e){

		if (isTouchSupported) {
			e.preventDefault();
		}

		window.clearInterval(timeout);
		window.clearInterval(seek30);

		if(scroll != true){

			var end = new Date();
			end = end - longpress;

			if ((arg!='idle') && (end<500)) {
				window.clearInterval(zap);
				zap = setTimeout(function(){
					if ((type=='webtv') || (type=='tv')) {
						if (type=='webtv'){
							setTimeout(function(){
								document.getElementById('osd').style.display='none';
							}, 600);
							
							ajax('play',channel[0],'webtv');
						}
						else if (type=='tv') {
							setTimeout(function(){
								document.getElementById('osd').style.display='none';
							}, 600);
							ajax('play',channel[0],'tv');
						}
					}
					else if (type=='file') {
						ajax('play','','file');
					}
					i=0;
					
				}, 1000);
				
				if ((type=='webtv') || (type=='tv')) {
					if (type=='webtv'){
						var funct=webtv;
					}
					else if (type=='tv') {
						var funct=dvb;
					}

					for (var z=0;z<funct.length;z++) {
						var channel=funct[z].value.split("||");
						if (channel[0]==pos) {
							if (i==0) {
								if (z!=0) {
									i=z-1;
								}
								else {
									i=funct.length-1;
								}
							}
							else {
								i--;
							}
							channel=funct[i].value.split("||");
							document.getElementById('osd').style.display='initial';
							document.getElementById('osd').textContent=channel[1];
							if (i==0) {
								i=funct.length;
							}
							break;
						}
					}	
				}
				else if(type=='file'){
					if ($("#selected")[0].selectedIndex==0) {
						var selection=$("#selected option").length-2;
					}
					else {
						var selection=($("#selected")[0].selectedIndex)-1;
					}
					$('#selected option')[selection].selected = true;
				}		
			}
			
		}

		setTimeout(function(){

			$(".play").css({
				"margin-left":"0",
				"border-color":constants['group41'],
				"border-left":"0"
			});
			$(".ff").css({
				"border-color":constants['group41'],
				"border-left":"0"				
			});
			$(".bb").css({
				"border-color":constants['group41'],
				"border-right":"0"	
			});		
			$("#bb_img").attr("src","img/" + constants['theme'] + "/left_arrow.png").css({
				"width":"38",
				"height":"30"
			});

		}, 100);

		scroll=false;

	}).on('touchmove', function(){

		clearInterval(act);
	    clearInterval(timeout);
	    clearInterval(seek30);
	    scroll = true;	

	}).on('touchstart mousedown', function(e){
		
		if (isTouchSupported) {
			e.preventDefault();
		}

		longpress= new Date(); 

		act = setTimeout(function(){

			if (arg=='idle') {
				if ($.trim($('#url_address').val()) == '') {	
					if ($("#selected")[0].selectedIndex==0) {
						var selection=$("#selected option").length-2;
					}
					else {
						var selection=($("#selected")[0].selectedIndex)-1;
					}
					$('#selected option')[selection].selected = true;
				}
			}

			$("#bb_img").attr("src","img/" + constants['theme'] + "/left_arrow_hovered.png");

			$(".bb").css({
				"border-color":constants['group44']
				
			});

		}, 100);

		timeout = setTimeout(function(){

			if ((out[4]>0) && (arg!='idle')) {
				$("#bb_img").attr("src","img/" + constants['theme'] + "/seek30.png").css({
					"width":"33",
					"height":"25"
				});				
				$(".play").css({
					"margin-left":"5",
					"border": "3px solid",
					"border-color":constants['group44'],
					"border-right":"0"
				});
				$(".bb").css({
					"border": "3px solid",
					"border-color":constants['group44']
					
				});
				$(".ff").css({
					"border-color":constants['group44'],
					"border-left":"0"
				});				
				seek30 = setInterval(function(){
					ajax('seek30m');
				}, 500);
				//longpress=true;
			}
			else if (arg=='idle'){
				seek30 = setInterval(function(){
					if ($.trim($('#url_address').val()) == '') {	
						if ($("#selected")[0].selectedIndex==0) {
							var selection=$("#selected option").length-2;
						}
						else {
							var selection=($("#selected")[0].selectedIndex)-1;
						}
						$('#selected option')[selection].selected = true;
					}
				}, 100);
			}
		}, 500);

		scroll = false;

	});

	$('.bb').on('mouseenter', function(){
		$("#bb_img").attr("src","img/" + constants['theme'] + "/left_arrow_hovered.png");
		$(".bb").css({
			"border-color":constants['group44']
			
		});

	});
	$('.bb').on('mouseleave', function(){
		$("#bb_img").attr("src","img/" + constants['theme'] + "/left_arrow.png");
		$(".bb").css({
			"border-color":constants['group41']
		});		

	});
	$('.ff').on('mouseenter', function(){
		$("#ff_img").attr("src","img/" + constants['theme'] + "/left_arrow_hovered.png");
		$(".ff").css({
			"border-color":constants['group44']
		});

	});
	$('.ff').on('mouseleave', function(){
		$("#ff_img").attr("src","img/" + constants['theme'] + "/left_arrow.png");
		$(".ff").css({
			"border-color":constants['group41']
		});		
		
	});

	$('.play').on('touchend mouseup', function(e){

		if (isTouchSupported) {
			e.preventDefault();
		}

		clearInterval(timeout);

	    if(scroll != true){
	        
	        setTimeout(function(){
		        $("#play_img").attr("src","img/" + constants['theme'] + "/play.png").css({
		        	"margin-left":"10",
		        	"height":"22px",
		        	"width":"26px"
		        });

		        $(".play").css({
		        	"margin":"0",
		        	"border-color":constants['group41'],
		        	"border-left":"0",
		        	"border-right":"0"	
		        });
		        $(".ff").css({
		        	"border-color":constants['group41'],
		        	"border-left":"0"	
		        	
		        });
		        $(".bb").css({
		        	"border-color":constants['group41'],
		        	"border-right":"0"	
		        });		
		 }, 100);
	    }

	    scroll=false;

	}).on('touchmove', function(){

	    clearInterval(timeout);
	    scroll = true;	

	}).on('touchstart mousedown', function(e){
		
		if (isTouchSupported) {
			e.preventDefault();
		}

		timeout = setTimeout(function(){
				$("#play_img").attr("src","img/" + constants['theme'] + "/play_hovered.png").css({
					"margin-left":"10",
					"height":"22px",
					"width":"26px"
				});		

				$(".play").css({
					"border-color":constants['group44']
				});

				if (parseInt(out[4])>0) {
					if (!paused) {
						$("#play_img").attr("src","img/" + constants['theme'] + "/pause_hovered.png").css({
							"margin-left":"8",
							"height":"22px",
							"width":"26px"				
						});					
					}
				}
				
				$(".play").css({
					"border": "3px solid",
					"border-color":constants['group44'],
					"margin":"0 5 0 5",
				});
				$(".ff").css({
					"border": "3px solid",
					"border-color":constants['group44']
					
				});
				$(".bb").css({
					"border": "3px solid",
					"border-color":constants['group44']
				});

				if ((arg=='idle') && (parseInt(out[4]))==0) {
					if ($.trim($('#url_address').val()) == '') {
						ajax('play','','file');
					} 
					else {
						ajax('play','','url');
					}
				}
				else { 
					if (parseInt(out[4])>0) {
						paused=true;
						ajax('pause');
					}
				}

		}, 60);
	    scroll = false;

	});

	$('.play').on('mouseenter', function(){
		$("#play_img").attr("src","img/" + constants['theme'] + "/play_hovered.png").css({
			"margin-left":"10",
			"height":"22px",
			"width":"26px"
		});		

		$(".play").css({
			"border-color":constants['group44']
		});

		if (parseInt(out[4])>0) {
			timeout = setTimeout(function(){
				if (!paused) {
					$("#play_img").attr("src","img/" + constants['theme'] + "/pause_hovered.png").css({
						"margin-left":"8",
						"height":"22px",
						"width":"26px"				
					});					
				}
			}, 200);
		}

	});

	$('.play').on('mouseleave', function(){
		clearInterval(timeout);
		$("#play_img").attr("src","img/" + constants['theme'] + "/play.png").css({
			"margin-left":"10",
			"height":"22px",
			"width":"26px"
		});
		$(".play").css({
			"border-color":constants['group41']
		});

	});

	$("#play_img").attr("src","img/" + constants['theme'] + "/play.png").css({
		"margin-left":"10",
		"height":"22px",
		"width":"26px"
	});

	/* URL LINK ADDRESS */

	$("#url_address").keypress(function(event) {
		if (event.which == 13) {
			$('#url_address').blur();
			ajax('play','','url');
		}
	});
	$('#url_address').on('mouseenter', function(){
		if (!$("#url_address").is(":focus")){
			$(this).css({
				"border":"4px solid",
				"border-color":constants['group50']
		});
		}
	});
	$('#url_address').on('mouseleave', function(){
		if (!$("#url_address").is(":focus")){
			$(this).css({
				"border":"4px solid",
				"border-color":constants['group43']
			});
		}
	});
	$('#url_address').on('blur', function() {
		if (!$("#url_address").is(":focus")){
			$("#url_address").css({
				"border":"4px solid",
				"border-color":constants['group43']
			});
		}
	});
	$('#url_address').on('change keyup paste input propertychange', function() {

		if 	($.trim($('#url_address').val()) == '') {
			$("#url_address").css({
				"border":"4px solid",
				"border-color":constants['group43']
			});
		}
		else {
			$('#url_address').css({
				'border-right':'6px solid',
				'border-right-color':constants['group51']
			});
			}
		
	});
	$('#clear').on('touchstart mouseenter', function(){
		if 	($.trim($('#url_address').val()) != '') {
			$("#url_address").css({
				"border":"4px dotted",
				"border-color":constants['group51']
			});
			$("#clear").css('cursor','pointer');
		}
		else {
			$("#clear").css('cursor','initial');
		}
	});
	$('#clear').on('touchend mouseleave', function(){

		if 	($.trim($('#url_address').val()) != '') {
			$("#url_address").css({
				"border":"4px solid",
				"border-color":constants['group43']
			});
			$('#url_address').css({
				'border-right':'6px solid',
				'border-color':constants['group51']

			});
		}
	}); 		  

	$('#clear').click(function(){
	$('#url_address').val('');
		setTimeout(function() { $('#url_address').focus(); }, 500);
	});
	
	// PAGE NAVIGATORS
	
	$('.top.up_tv').on('touchstart mousedown', function(e){
	
		$("#up_tv").attr("src","img/" + constants['theme'] + "/up_hovered.png");

	});
	$('.top.up_tv').on('touchend mouseup', function(e){
	
		$("#up_tv").attr("src","img/" + constants['theme'] + "/up.png");

	});  
	$('.top.up_tv').on('mouseenter', function(){
		$("#up_tv").attr("src","img/" + constants['theme'] + "/up_hovered.png");

	});
	$('.top.up_tv').on('mouseleave', function(){
		$("#up_tv").attr("src","img/" + constants['theme'] + "/up.png");

	});  
	$('.top.up_webtv').on('touchstart mousedown', function(e){

		$("#up_webtv").attr("src","img/" + constants['theme'] + "/up_hovered.png");

	});
	$('.top.up_webtv').on('touchend mouseup', function(e){

		$("#up_webtv").attr("src","img/" + constants['theme'] + "/up.png");

	});  
	$('.top.up_webtv').on('mouseenter', function(){
		$("#up_webtv").attr("src","img/" + constants['theme'] + "/up_hovered.png");

	});
	$('.top.up_webtv').on('mouseleave', function(){
		$("#up_webtv").attr("src","img/" + constants['theme'] + "/up.png");
	});  
	$('.top.up_cc').on('touchstart mousedown', function(e){

		$("#up_cc").attr("src","img/" + constants['theme'] + "/up_hovered.png");

	});
	$('.top.up_cc').on('touchend mouseup', function(e){

		$("#up_cc").attr("src","img/" + constants['theme'] + "/up.png");

	});  
	$('.top.up_cc').on('mouseenter', function(){
		$("#up_cc").attr("src","img/" + constants['theme'] + "/up_hovered.png");

	});
	$('.top.up_cc').on('mouseleave', function(){
		$("#up_cc").attr("src","img/" + constants['theme'] + "/up.png");
	});  

	// SHELL SCRIPT TAGS
	
	$(".dark").on("touchstart mousedown",function(){
		$(this).css({
			"color":constants['group5']
		});
	});
	$(".dark").on("touchend mouseup",function(){
		$(this).css({
			"color":constants['group1']
		});
	});

	$(".dark").on("mouseenter",function(){
		$(this).css({
			"color":constants['group5']
		});
	});
	$(".dark").on("mouseleave",function(){
		$(this).css({
			"color":constants['group1']
		});
	});

});

