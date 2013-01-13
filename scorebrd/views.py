# Create your views here.
from django.shortcuts import render_to_response, get_object_or_404, redirect
from scorebrd.models import (Team, Event, Group, Competition, LoginForm, Match, TeamResult)
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

# event/s
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

# competition/s
@render_to('competition.html')
@login_required(login_url='/login/')
def competition(request, competition_id):
    competition = get_object_or_404(Competition, pk=competition_id)
    groups = competition.groups.all()
    event = competition.event_set.all()[0]
    return {'event': event, 'competition': competition, 'groups': groups}

@render_to('competitions.html')
@login_required(login_url='/login/')
def competitions(request):
    competitions = Competition.objects.all()
    return {'competitions': competitions}

# group/s
@render_to('group.html')
@login_required(login_url='/login/')
def group(request, group_id):
    group = get_object_or_404(Group, pk=group_id)
    teams = group.teams.all()
    competition = group.competition_set.all()[0]
    event = competition.event_set.all()[0]
    return {'group': group, 'teams': teams,
            'competition': competition, 'event': event}

@render_to('groups.html')
@login_required(login_url='/login/')
def groups(request):
    groups = Group.objects.all()
    return {'groups': groups}

# team/s
@render_to('teams.html')
@login_required(login_url='/login/')
def teams(request):
    teams = Team.objects.all()
    return {'teams': teams}

@render_to('team.html')
@login_required(login_url='/login/')
def team(request, team_id):
    team = get_object_or_404(Team, pk=team_id)
    group = team.group_set.all()[0]
    competition = group.competition_set.all()[0]
    event = competition.event_set.all()[0]
    from itertools import chain

    matches = list(chain(Match.objects.filter(teamA=team),
            Match.objects.filter(teamB=team)))
    return {'group': group, 'competition': competition, 'event': event,
            'team': team, 'matches': matches}

@render_to('index.html')
def index(request):
    events = Event.objects.all()
    return {'user': request.user, 'events': events}

@render_to('matches/generate.html')
@login_required(login_url='/login/')
def matches_generate(request, group_id=None):
    if group_id is None:
        if request.GET:
            return {'groups': Group.objects.all()}
        elif request.POST:
            group = get_object_or_404(Group, pk=group_id)
            teams = group.teams.all()
    
    group = get_object_or_404(Group, pk=group_id)
    competition = group.competition_set.all()[0]
    return {'group': group, 'competition': competition}

def matches_generate_listing(request):
    pass

def match_view(request, match_id):
    pass

def results(request):
    pass

def results_team_view(request, team_id):
    pass

def results_group_view(request, group_id):
    pass
