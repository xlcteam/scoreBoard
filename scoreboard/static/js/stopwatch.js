window.mins = 10;
window.secs = 0;

window.halftime = 1;

function showD() {
    window.finished = true;
	$('#dialogMain').show();    
	$("#dialog").dialog({ buttons: {
				 "Send results": function() { 
                        var df = confirm("Are you sure to send results?");
                          if(df){
                            $.post(window.update_url, 
                                {action: 'finish', 
                                    team1goals: $('#team1').text(),
                                    team2goals: $('#team2').text(),},
                                function(data) {
                                    $(this).dialog("close"); 
				                    $('#dialogMain').hide();
                                    window.location = window.back_url; 
                                }
                            );
                          }else{
                            return;
                          }
                              
                        
					
				}	
			}
	});
	$('#dname').html($('#name1').text());
	$('#d2name').html($('#name2').text());
	$('#dgoals').val($('#team1').text());
	$('#d2goals').val($('#team2').text());
}

function startMatch (){
  $('#startAll').hide();
  toggle();
  
}

function toggleHalf()
{
	halftimeNumber = document.getElementById("halftime");  
  halftimeNumber.innerHTML = "2.";

  /*$('.leftBckg').css("float", "right");
  $('.rightBckg').css("float", "left");*/
	toggle();

}


function pad2(number) {                                                
     return (number < 10 ? '0' : '') + number;                         
}

function format(millis) {                                              
    var seconds, minutes;                                              
    minutes = Math.floor(millis / 60000);                              
    millis %= 60000;                                                   
    seconds = Math.floor(millis / 1000);                               
    millis = Math.floor(millis % 1000);                                
    millis = Math.floor(millis / 10);
		
		if (window.halftime == 1 && minutes >= window.mins / 2){
			if (seconds >= window.secs / 2){
				$("#time").stopwatch().stopwatch('stop');
				toggleHalf();
				window.halftime = 2;
        $('#startText').html("Start 2nd half");
        $('#startAll').show();			
			}				
		}else if (window.halftime == 2 && minutes >= window.mins){
			if (seconds >= window.secs){
        $("#time").stopwatch().stopwatch('stop');        
        $.idleTimer('destroy');

        //spaghetti unbind code
        $(".element1").unbind("mouseover", fill1);
				$(".element1").unbind("mouseout", unfill1);
				$(".element2").unbind("mouseover", fill2);
				$(".element2").unbind("mouseout", unfill2);
				$(".element3").unbind("mouseover", fill3);
				$(".element3").unbind("mouseout", unfill3);
        $(".leftBckg").unbind("mouseover", fill1);
				$(".leftBckg").unbind("mouseout", unfill1);
				$(".rightBckg").unbind("mouseover", fill2);
				$(".rightBckg").unbind("mouseout", unfill2);
				$(".startBckg").unbind("mouseover", fill3);
				$(".startBckg").unbind("mouseout", unfill3);
        

        $(".startBckg, .leftBckg, .rightBckg").fadeIn("fast");
        $(".startBckg, .leftBckg, .rightBckg").css('opacity', '0.7');
        $(".startBckg, .leftBckg, .rightBckg").css('background', '#0042AB');
        showD();
      }
		}
		return [pad2(minutes), pad2(seconds)].join(':') + ',' + pad2(millis);
		
}   

function toggle ()
{
  if ($("#startAll").is(':visible')){
    $('#startAll').hide();
  }  
  
  btnStart = document.getElementById("btnStart");  
	if (btnStart.innerHTML == "Start" || btnStart.innerHTML == "Resume"){
    btnStart.innerHTML = "Pause";
		$("#time").stopwatch({formatter: format, updateInterval: 50}).stopwatch('start');
    return;
  }
  else if (btnStart.innerHTML == "Pause"){
    btnStart.innerHTML = "Resume";
		$("#time").stopwatch().stopwatch('stop');
		return;
  }
}
                                                                      
function resetTime()
{
  btnStart = document.getElementById("btnStart");	
	if (btnStart.innerHTML == "Resume" || btnStart.innerHTML == "Pause") {
    $("#time").stopwatch().stopwatch('stop');		
    $("#time").stopwatch().stopwatch('reset');
    $("#time").html("00:00,00");
    btnStart.innerHTML = "Start";
  }
  if ($("#startAll").is(':hidden')){
    $('#startSpan').html("Start match");
    $('#startAll').show();
  }else {
    $('#startSpan').html("Start match");
  }
	halftimeNumber = document.getElementById("halftime"); 
	if (halftimeNumber.innerHTML == "2."){
		halftimeNumber.innerHTML = "1."
	} 
  window.halftime = 1;
	
  return;
}

function newTime()
{
	var inpMins = $('#fmins').val();
	var inpSecs = $('#fsecs').val();
	
	window.mins = inpMins;
	window.secs = inpSecs;
  $('.saved').fadeIn(200).delay(500).fadeOut(200);


	return false;
}


