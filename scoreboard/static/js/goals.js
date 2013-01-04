var goal1 = 0;
var goal2 = 0;

window.scaling1 = 0;
window.scaling2 = 0;

function toggleOnGoal()
{
	btnStart = document.getElementById("btnStart");
	if (btnStart.innerHTML == "Pause"){
	    $("#time").stopwatch().stopwatch('stop');
		btnStart.innerHTML = "Resume";
	}
}

function team1Goal()
{
	if (window.scaling1 == 1){ return false;}
  else {
		goal1++;
        $.post(window.update_url, {action: 'team1_increase'},
                function(data) {
                    return;
                }
        );
		//toggleOnGoal();
		$("#team1").html(goal1);
		if(document.forms['effects'][0].checked) {
			window.scaling1 = 1;
			$("#team1").effect("scale", { percent: 150}, 500)
				           .effect("scale", { percent: (100 / (150 / 100))}, 1000, function(){
		                      window.scaling1 = 0;
								});
		}
	}
}

function team2Goal()
{
	if (window.scaling2 == 1){ return false;}
	else {
		goal2++;
        $.post(window.update_url, {action: 'team2_increase'},
                function(data) {
                    return;
                }
        );
		//toggleOnGoal();
		$("#team2").html(goal2);
		if(document.forms['effects'][0].checked) {
			window.scaling2 = 1;
			$("#team2").effect("scale", { percent: 150}, 500)
				         .effect("scale", { percent: 100 / (150 / 100)}, 1000, function(){
												window.scaling2 = 0;
									});
		}
	}
}

function team1Down()
{
	if (goal1 <= 0){
		goal1 = 0;
		return false;	
	}else{
		if (window.scaling1 == 1){ return false;}
		else {		
			goal1--;
            $.post(window.update_url, {action: 'team1_decrease'},
                    function(data) {
                        return;
                    }
            );
			$("#team1").html(goal1);
			if(document.forms['effects'][0].checked) {
				window.scaling1 = 1;	
                soundPlay("whistle");
				$("#team1").effect("scale", { percent: 150}, 500)
		             	.effect("scale", { percent: Math.ceil(100 / (150 / 100))}, 1000, function(){
                        	window.scaling1 = 0;
									});
			}
		}
	}
}


//======================================================================
var soundEmbed = null;
//======================================================================
function soundPlay(which)
    {
    if (!soundEmbed)
        {
        soundEmbed = document.createElement("embed");
        soundEmbed.setAttribute("src", "/mp3/"+which+".wav");
        soundEmbed.setAttribute("hidden", true);
        soundEmbed.setAttribute("autostart", true);
        }
    else
        {
        document.body.removeChild(soundEmbed);
        soundEmbed.removed = true;
        soundEmbed = null;
        soundEmbed = document.createElement("embed");
        soundEmbed.setAttribute("src", "/mp3/"+which+".wav");
        soundEmbed.setAttribute("hidden", true);
        soundEmbed.setAttribute("autostart", true);
        }
    soundEmbed.removed = false;
    document.body.appendChild(soundEmbed);
    }



function team2Down()
{
	if (goal2 <= 0){
		goal2 = 0;
		return false;	
	}else{
		if (window.scaling2 == 1){ return false;}
		else {
			goal2--;
            $.post(window.update_url, {action: 'team2_decrease'},
                    function(data) {
                        return;
                    }
            );
			$("#team2").html(goal2);
			if(document.forms['effects'][0].checked) {
				window.scaling2 = 1;
                soundPlay("whistle");
				$("#team2").effect("scale", { percent: 150}, 500)
				           .effect("scale", { percent: Math.ceil(100 / (150 / 100))}, 1000, function(){
													window.scaling2 = 0
									});
			}
		}
	}
}

function resetScore()
{
	goal1 = 0;
	goal2 = 0;
	$('#team1').html(goal1);
	$('#team2').html(goal2);
}
