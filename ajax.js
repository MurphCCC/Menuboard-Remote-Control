function ajax(act,file,func,ar,ar1) {

	if ((act!='out') && (act!='seekto')) {
		arg1='1';
		if (timer===false) {
			
			if (counter<1) {
				counter++;
				window.setInterval(function(){ajax('out');}, 1000);
			}
		}	
	}

	if ((arg1=='1') && ((act=='out') || (act=='seekto'))){
		return;
	}

	switch (act) {
		case 'play':
		switch (func) {
			case 'file':
			file=document.getElementById('selected').value;
			func="file";
			break;
			case 'url':
			file=document.getElementById('url_address').value;
			func="url";
			break;
			case 'ext':
			document.getElementById('url_address').value=file;
			func="url";
			break;
			case 'ext_':
			document.getElementById('selected').selectedIndex=0;
			func="file";
			break;
			case 'webtv':
			func="webtv";
			break;
			case 'tv':
			func="tv";
			break;
			case 'history':
			func="url";
			break;
		}
		if ((func=='file') && (subtitles==true)) {
			if (file!='') {
				document.getElementById('console').innerHTML=waiting_for_subs;
			}
			else {
				document.getElementById('console').innerHTML=waiting;
			}		
		}
		else {
			document.getElementById('console').innerHTML=waiting;		
		}
		out[8]=false;
		break;
		case 'halt': 
		if (ar1!="1") {
			ar1=document.getElementById('sys').value;
		}
		break;
		case 'passthrough':
		if ($('#passthrough').text()==index_passe) {
			
			$('#passthrough').empty();
			$('<p>',{
				text: index_passd,
			}).appendTo('#passthrough');

			$("#audio_out").attr("src", "img/" + constants['theme'] + "/hdmi.png");

			$('#audio_lay').empty();
			$('<p>',{
				text: index_lay0,
			}).appendTo('#audio_lay');
		}
		else {

			$('#passthrough').empty();
			$('<p>',{
				text: index_passe,
			}).appendTo('#passthrough');

		}
		break;
		case 'audio_lay':
		if ($('#audio_lay').text()==index_lay0) {

			$('#audio_lay').empty();
			$('<p>',{
				text: index_lay21,
			}).appendTo('#audio_lay');

			$('#passthrough').empty();
			$('<p>',{
				text: index_passe,
			}).appendTo('#passthrough');
		}
		else if ($('#audio_lay').text()==index_lay51) {

			$('#audio_lay').empty();
			$('<p>',{
				text: index_lay0,
			}).appendTo('#audio_lay');
		}
		else if ($('#audio_lay').text()==index_lay21) {

			$('#audio_lay').empty();
			$('<p>',{
				text: index_lay51,
			}).appendTo('#audio_lay');

			$("#audio_out").attr("src", "img/" + constants['theme'] + "/hdmi.png");

			$('#passthrough').empty();
			$('<p>',{
				text: index_passe,
			}).appendTo('#passthrough');
		}
		break;
		case 'source_res':
		if ($('#source_res').text()==index_arese) {
			
			$('#source_res').empty();
			$('<p>',{
				text: index_aresd,
			}).appendTo('#source_res');

		}
		else {

			$('#source_res').empty();
			$('<p>',{
				text: index_arese,
			}).appendTo('#source_res');
		}
		break;
		case 'audio_out':
		if ($("#audio_out").attr("src")=="img/" + constants['theme'] + "/both.png") {
			$("#audio_out").attr("src", "img/" + constants['theme'] + "/hdmi.png");
		}
		else if ($("#audio_out").attr("src")=="img/" + constants['theme'] + "/hdmi.png") {
			$("#audio_out").attr("src", "img/" + constants['theme'] + "/rca.png");
			$('#passthrough').empty();
			$('<p>',{
				text: index_passe,
			}).appendTo('#passthrough');
		}
		else if ($("#audio_out").attr("src")=="img/" + constants['theme'] + "/rca.png") {
			$("#audio_out").attr("src", "img/" + constants['theme'] + "/both.png");
			$('#passthrough').empty();
			$('<p>',{
				text: index_passe,
			}).appendTo('#passthrough');
		}
		break;
		case 'reallocate':
		document.getElementById('console').innerHTML=dvb_sync;		
		break;
		default:
		break;
	}

	//LOOPING VARIABLES
	
	switch (act) {
		case 'out':
		ar=arg;
		file=pos;
		func=type;
		break;
		case 'play':
		if ((arg=='idle') || (ar=='idle')){
			arg='';
			ar='';			
		}
		else {
			ar=arg;
			ar1=arg1;			
		}
		break;
		case 'halt':
		ar=arg;
		file=pos;
		func=type;
		break;
		case 'stop':
		ar1=arg1;
		ar=arg;
		file=pos;
		break;
		case 'seekto':
		break;
		case 'run':
		ar1=arg1;
		file=pos;
		func=type;
		break;		
		default:
		ar1=arg1;
		ar=arg;
		file=pos;
		func=type;
		break;
	}

	result = new Array();
	$.ajax({
		url: "ajax.php", 
		type: 'POST',
		data:{ 
			"command": act, 
			"pos": file, 
			"type": func, 
			"arg": ar, 
			"out": out,
			"arg1":ar1,
			"constants":constants,
			"sudoer":sudoer,
			"session":session
		},
    	//dataType:'json',
    	success: function(results){

    		result = JSON.parse(results);
    		command=result['command'];
    		arg=result['arg'];			
    		type=result['type'];
    		pos=result['pos'];
    		arg1=result['arg1'];

    		if (out[5]==result['out'][5]) {
    			paused=true;
    		}
    		else {
    			paused=false;
    		}

    		if (out[2]==result['out'][2]) {

    			delay[1]=true;
    		}
    		else {

    			delay[1]=false;
    			filename=result['out'][2];
    		}

        	//LOOP GLOBAL RESULT MESSAGES 2 TIMES

        	if (out[0]==result['out'][0]) {
        		if (delay[0]===true) {
        			out=result['out'];
        		}
        		else {
        			result['out'][0]='';
        			out=result['out'];
        		}
        		delay[0]=false;	
        	}
        	else {
        		out=result['out'];	
        		delay[0]=true;			     		
        	}
        	if (command!="out"){
        		file=pos;
        		func=type;
        		ar=arg;
        		ar1=arg1;
        	}

			//HISTORY

			if ((type=="url") && (command=="play") && (pos!=null)) {

				var history = $(".wrapper a[onclick*='"+pos+"']");

				if (history.length) {
					
					history.prependTo(".wrapper");
					var listed=true;

				}

				if(!listed) {

					if (out[7].length > 45 ) {
						var text = out[7].substr(0, 45)+"...";
					}
					else {
						var text = out[7];
					}

					$(".wrapper").show();
					$("a:contains('" + text + "')").closest('.history').remove();
					$(".wrapper").prepend('<a class="internal history" onclick="ajax(\'play\',\''+pos+'\',\'history\')">'+text+'&nbsp;'+'</a>');
					$("#clr").css({"opacity":"1"});	
				}

			}

			//OUTPUT MESSAGES
			//0:RESULT MESSAGE
			//1:IDLE STATE
			//2:CURRRENT PLAYING
			//3:REMAINING TIME PATTERNED
			//4:MOVIE LENGTH (SECS)
			//5:CURRENT TIME (SECS)
			//6:CURRENT TIME PATTERNED
			//7:FILENAME OR VIDEO TITLE
			//8:ALERT IF ANOTHER SESSION STARTS
			
   			//TIMELINE

   			if (document.getElementById("timeline").getAttribute("max")!=parseInt(out[4])) {

   				if (parseInt(out[4])>0) {

   					document.getElementById("timeline").setAttribute("max",parseInt(out[4]));
   					document.getElementById("timeline").value=0;

   					if(document.getElementById('timeline').disabled == true){
   						document.getElementById('timeline').disabled = false;
   					}

   				}
   				else {

   					if(document.getElementById('timeline').disabled == false){
   						document.getElementById("timeline").setAttribute("max",1);
   						document.getElementById("timeline").value=0;
   						document.getElementById("timeline").value=1;	 
   						document.getElementById('timeline').disabled = true;					
   					}

   					if (arg=='idle') {
   						document.getElementById('time').innerText='';
   					}

   				}
   			}

   			if ((document.getElementById("timeline").getAttribute("value")!=parseInt(out[5])) && (parseInt(out[4])>0)) {

   				if (parseInt(out[5]) > 0) {

   					document.getElementById("timeline").value=parseInt(out[5]);

   				}
   			}

       		//CONSOLE
       		
       		if ((arg=='idle') && (out[8]!==true)) {

				//OUTPUT DEBUG
				//console=out[0]+' '+out[1]+' '+out[2]+' '+out[3]+' '+out[4]+' '+out[5];
				
				if (out[0]=='') {

						// ABSOLUTE IDLE STATE

						console = out[1];
						if (constants['theme']=='terminal') {
							console = console + " _";
						}
						timer=false;
						counter=0;
						for (var i = 1; i < 100; i++){ 
							window.clearInterval(i);
						}
					}
					else {

						//RESULT MESSAGE IN IDLE STATE

						console = out[0];

					}
				}
				else {

				// PLAYING STATE

				if (width!=document.documentElement.clientWidth) {
					width=document.documentElement.clientWidth;
					delay[1]=false;
					filename=out[2];

				}

				// REMAINING TIME
				
				if (parseInt(out[4])==-1) {

					if ((type=='webtv') || (type=='tv')){
						document.getElementById('time').innerText="Live";
					}
					else {
						document.getElementById('time').innerText="N/A";
					}						
				}
				else if (parseInt(out[4])==0){
					document.getElementById('time').innerText="";
				}
				else if (out[5]>1) {
					if (parseInt(out[4])>0) {
						if (out[3]!='') {
							document.getElementById('time').innerText="- " + out[3];
						}
					}	
				}
				else {
	        		//delay
	        	}

	        	if (out[0]=='') {

        			//CURRENT PLAYING
        			
        			if (!delay[1]) {

        				var size = document.getElementsByTagName('body')[0].clientWidth - document.getElementById('time').offsetWidth - 65;

        				document.getElementById('console').innerHTML=out[2];

        				var console_size = document.getElementById('console').offsetWidth;

        				if (console_size > size) {

        					while (console_size > size) {
        						filename = filename.slice(0, -1);
        						document.getElementById('console').innerHTML=filename;
        						console_size = document.getElementById('console').offsetWidth;
        					}

        					filename+="...'";
        					console = filename;

        				}
        				else {

        					console = out[2];

        				}
        			}
        			else {

        				console = filename;

        			}
        		}
        		else {

        			//RESULT MESSAGE (DELAY CURRENT PLAYING INFO)

        			width=0;
        			console = out[0];

        			//MULTIPLE SESSIONS ALERT, HALT LOOP
        			
        			if (out[8]===true) {

        				window.clearInterval(loop);

        				timer=false;
        				counter=0;
        				out[6]=0;
        				document.getElementById('time').innerText="";
        				document.getElementById('playtime').textContent = "";
        				document.getElementById("timeline").setAttribute("max",1);
        				document.getElementById("timeline").value=0;
        				document.getElementById("timeline").value=1;
        				document.getElementById('timeline').disabled = true;

        			}
        		}
        	}

        	if (command=="reallocate") {
        		window.open('index.php','_self');
        	}

        	if (arg===true) {
        		window.open('index.php?update=','_self');
        	}

        	if (constants['theme']=='terminal') {
        		console="> "+console;
        	}
        	document.getElementById('console').textContent = console;
        	
        	if ((out[6]==0) || (out[6]=='undefined') || (out[6]==null)) {
        		document.getElementById('playtime').style.opacity="0";
        	}
        	else {
        		document.getElementById('playtime').style.opacity="1";
        		document.getElementById('playtime').textContent = out[6];
        	}

			//JS DEBUG		
    		//document.getElementById('console').textContent = i + " " + act + " " + func + " " + ar + " " + ar1 + " " + file;
    		//var i=i+1;
    	},
		//async: false
	});

}