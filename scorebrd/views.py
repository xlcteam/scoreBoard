# Create your views here.
from django.shortcuts import render_to_response
from scorebrd.models import Team, Event, Group, Competition

def events(request):
    events = Event.objects.all()
    return render_to_response('events.html', {'events': events})

def teams(request):
    teams = Team.objects.all()
    return render_to_response('teams.html', {'teams': teams})

def index(request):
    return render_to_response('index.html')

def groups(request):
    groups = Group.objects.all()
    return render_to_response('groups.html', {'groups': groups})

def competitions(request):
    competitions = Competition.objects.all()
    return render_to_response('competitions.html',
                                {'competitions': competitions})
