# Create your views here.
from django.shortcuts import render_to_response
from scorebrd.models import Team

def events(request):
    events = Events.objects.all()
    return render_to_response('events.html', {'events': events})

def teams(request):
    teams = Team.objects.all()
    return render_to_response('teams.html', {'teams': teams})
