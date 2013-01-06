# Create your views here.
from django.shortcuts import render_to_response, get_object_or_404, redirect
from scorebrd.models import (Team, Event, Group, Competition, LoginForm, Match,
    MatchesCreateForm)
from django.contrib.auth import authenticate, login, logout
from django.core.context_processors import csrf
from annoying.decorators import render_to
from django.contrib.auth.decorators import login_required


@render_to('login.html')
def my_login(request, url='/'):
    
    if 'next' in request.POST:
        url = request.POST['next']
    
    def errorHandle(error):
        form = LoginForm()
        c = {}
        c.update(csrf(request))
        c['form'] = form
        c['error'] = error
        return c

    if request.user.is_authenticated():
        return redirect(url)

    if request.method == 'POST':
        form = LoginForm(request.POST)
        if form.is_valid(): 
            username = request.POST['username']
            password = request.POST['password']
            user = authenticate(username=username, password=password)
            if user is not None:
                if user.is_active:
                    # Redirect to a success page.
                    login(request, user)
                    return redirect(url)
            else:
                error = u'Invalid login'
                return errorHandle(error)	
        else:
            return errorHandle(u'Invalid login')
    else:
        form = LoginForm()
        c = {}
        c.update(csrf(request))
        c['form'] = form
        return c

def my_logout(request):
    if request.user.is_authenticated():
        logout(request)       
        
    return redirect('/')

@render_to('results_live.html')
def results_live(request):
    groups = Group.objects.all()
    events = Event.objects.all()
    return {'groups': groups, 'event': events}

@render_to('events.html')
@login_required(login_url='/login/')
def events(request):
    events = Event.objects.all()
    return {'events': events}

@render_to('event.html')
@login_required(login_url='/login/')
def event(request, event_id):
    event = get_object_or_404(Event, pk=event_id)
    competitions = Competition.objects.all()
    return {'event': event, 'competitions': competitions}

@render_to('teams.html')
@login_required(login_url='/login/')
def teams(request):
    teams = Team.objects.all()
    return {'teams': teams}

@render_to('team.html')
@login_required(login_url='/login/')
def team(request, team_id):
    team = get_object_or_404(Team, pk=team_id)
    from itertools import chain

    matches = list(chain(Match.objects.filter(teamA=team),
            Match.objects.filter(teamB=team)))
    return {'team': team, 'matches': matches}

@render_to('index.html')
def index(request):
    events = Event.objects.all()
    return {'user': request.user, 'events': events}

@render_to('group.html')
@login_required(login_url='/login/')
def group(request, group_id):
    group = get_object_or_404(Group, pk=group_id)
    teams = group.teams.all()
    return {'group': group, 'teams': teams}

@render_to('groups.html')
@login_required(login_url='/login/')
def groups(request):
    groups = Group.objects.all()
    return {'groups': groups}

@render_to('competition.html')
@login_required(login_url='/login/')
def competition(request, competition_id):
    competition = get_object_or_404(Competition, pk=competition_id)
    groups = competition.groups.all()
    return {'competition': competition, 'groups': groups}

@render_to('competitions.html')
@login_required(login_url='/login/')
def competitions(request):
    competitions = Competition.objects.all()
    return {'competitions': competitions}

@render_to('matches/generate.html')
@login_required(login_url='/login/')
def matches_generate(request):
    if request.method == 'POST':
        form = MatchesCreateForm(request.POST)
    else:
        form = MatchesCreateForm()
     
    return {'form': form}
