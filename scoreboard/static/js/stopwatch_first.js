function toggle()
{
  if ((document.getElementById("btnStart").innerHTML == "Start") || 
					(document.getElementById("btnStart").innerHTML == "Resume")){
    document.getElementById("btnStart").innerHTML = "Pause";
    return;
  }
  if (document.getElementById("btnStart").innerHTML == "Pause"){
    document.getElementById("btnStart").innerHTML = "Resume";
    return;
  }
}
      
var delay = 10;
var milisecs = 0;
var secs = 0;
var mins = 0;
function timer()
{
  if (document.getElementById("btnStart").innerHTML == "Pause"){
    milisecs += 1;
    if (milisecs == 100){
      secs += 1;
      milisecs = 0;
    }
    if (secs == 60){
      mins += 1;
        secs = 0;
    }     
  }
}
setInterval(timer, delay);
      
function view() 
{
  if (mins < 10){
    $("#mins").html("0"+mins);
  } else {
      $("#mins").html(mins);
  }
        
  if (secs < 10){
    $("#secs").html("0"+secs);
  } else {
      $("#secs").html(secs);
  }
        
  if (milisecs < 10){
    $("#milis").html("0"+milisecs);
  } else {
      $("#milis").html(milisecs);
  }  
}
setInterval(view, 10);
