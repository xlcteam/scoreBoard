# Create your views here.
from django.shortcuts import render_to_response, get_object_or_404
from scorebrd.models import Team, Event, Group, Competition

def events(request):
    events = Event.objects.all()
    return render_to_response('events.html', {'events': events})

def event(request, event_id):
    event = get_object_or_404(Event, pk=event_id)
    competitions = Competition.objects.all()
    return render_to_response('event.html', 
            {'event': event, 'competitions': competitions})

def teams(request):
    teams = Team.objects.all()
    return render_to_response('teams.html', {'teams': teams})

def team(request, team_id):
    team = get_object_or_404(Team, pk=team_id)
    return render_to_response('team.html', {'team': team})

def index(request):
    return render_to_response('index.html')

def group(request, group_id):
    group = get_object_or_404(Group, pk=group_id)
    return render_to_response('group.html', {'group': group})

def groups(request):
    groups = Group.objects.all()
    return render_to_response('groups.html', {'groups': groups})

def competition(request):
    competition = get_object_or_404(Competition, pk=competition_id)
    groups = competition.groups()
    return render_to_response('competition.html',
            {'competition': competition, 'groups': groups})

def competitions(request):
    competitions = Competition.objects.all()
    return render_to_response('competitions.html',
                                {'competitions': competitions})
