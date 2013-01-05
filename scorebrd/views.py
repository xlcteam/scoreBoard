# Create your views here.
from django.shortcuts import render_to_response, get_object_or_404, redirect
from scorebrd.models import Team, Event, Group, Competition, LoginForm, Match
from django.contrib.auth import authenticate, login, logout
from django.core.context_processors import csrf
from annoying.decorators import render_to

@render_to('login.html')
def my_login(request):
    def errorHandle(error):
        form = LoginForm()
        c = {}
        c.update(csrf(request))
        c['form'] = form
        c['error'] = error
        return c

    if request.user.is_authenticated():
        return redirect('/')

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
                    return redirect('/')
            else:
                error = u'invalid login'
                return errorHandle(error)	
        else:
            return errorHandle(u'invalid login')
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
def events(request):
    events = Event.objects.all()
    return {'events': events}

@render_to('event.html')
def event(request, event_id):
    event = get_object_or_404(Event, pk=event_id)
    competitions = Competition.objects.all()
    return {'user': request.user, 'event': event, 'competitions': competitions}

@render_to('teams.html')
def teams(request):
    teams = Team.objects.all()
    return {'teams': teams}

@render_to('team.html')
def team(request, team_id):
    team = get_object_or_404(Team, pk=team_id)
    from itertools import chain

    matches = list(chain(Match.objects.filter(teamA=team),
            Match.objects.filter(teamB=team)))
<<<<<<< HEAD
    return {'user': request.user, 'team': team, 'matches': matches,
            'event_id': event_id, 'competition_id': competition_id, 'group_id': group_id}
=======
    return {'team': team, 'matches': matches}
>>>>>>> b408b782ab29dc532fb28de678361f0a7029f275

@render_to('index.html')
def index(request):
    return {'user': request.user}

@render_to('group.html')
def group(request, group_id):
    group = get_object_or_404(Group, pk=group_id)
    teams = group.teams.all()
<<<<<<< HEAD
    return {'user': request.user, 'group': group, 'teams': teams, 
            'event_id': event_id, 'competition_id': competition_id}
=======
    return {'group': group, 'teams': teams}
>>>>>>> b408b782ab29dc532fb28de678361f0a7029f275

@render_to('groups.html')
def groups(request):
    groups = Group.objects.all()
    return {'groups': groups}

@render_to('competition.html')
def competition(request, competition_id):
    competition = get_object_or_404(Competition, pk=competition_id)
    groups = competition.groups.all()
<<<<<<< HEAD
    return {'user': request.user, 'competition': competition, 'groups': groups, 
            'event_id': event_id}
=======
    return {'competition': competition, 'groups': groups}
>>>>>>> b408b782ab29dc532fb28de678361f0a7029f275

@render_to('competitions.html')
def competitions(request):
    competitions = Competition.objects.all()
    return {'competitions': competitions}

def matches_create(request):
    pass
