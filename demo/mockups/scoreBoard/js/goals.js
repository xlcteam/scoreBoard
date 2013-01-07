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
		//toggleOnGoal();
		$("#team1").html(goal1);
		if(document.forms['effects'][0].checked) {
			window.scaling1 = 1;
			$("#team1").effect("scale", { percent: 150}, 500)
				           .effect("scale", { percent: Math.ceil(100 / (150 / 100))}, 1000, function(){
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
		//toggleOnGoal();
		$("#team2").html(goal2);
		if(document.forms['effects'][0].checked) {
			window.scaling2 = 1;
			$("#team2").effect("scale", { percent: 150}, 500)
				         .effect("scale", { percent: Math.ceil(100 / (150 / 100))}, 1000, function(){
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
			$("#team1").html(goal1);
			if(document.forms['effects'][0].checked) {
				window.scaling1 = 1;	
				$("#team1").effect("scale", { percent: 150}, 500)
		             	.effect("scale", { percent: Math.ceil(100 / (150 / 100))}, 1000, function(){
                        	window.scaling1 = 0;
									});
			}
		}
	}
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
			$("#team2").html(goal2);
			if(document.forms['effects'][0].checked) {
				window.scaling2 = 1;
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
