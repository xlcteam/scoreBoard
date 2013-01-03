# Create your views here.
from django.shortcuts import render_to_response, get_object_or_404, redirect
from scorebrd.models import Team, Event, Group, Competition, LoginForm
from django.contrib.auth import authenticate, login
from django.core.context_processors import csrf


def login(request):
    def errorHandle(error):
        form = LoginForm()
        c = {}
        c.update(csrf(request))
        c['form'] = form
        c['error'] = error
        return render_to_response('login.html', c)

    if request.method == 'POST':
        form = LoginForm(request.POST)
        if form.is_valid(): 
            username = request.POST['username']
            password = request.POST['password']
            user = authenticate(username=username, password=password)
            if user is not None:
                if user.is_active:
                    from django.contrib.auth import login
                    # Redirect to a success page.
                    login(request, user)
                    print "worked"
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
        return render_to_response('login.html', c)

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
    if not request.user.is_authenticated():    
        return redirect('/login')
    else:
        return render_to_response('index.html')

def group(request, group_id):
    group = get_object_or_404(Group, pk=group_id)
    teams = group.teams.all()
    return render_to_response('group.html',
            {'group': group, 'teams': teams})

def groups(request):
    groups = Group.objects.all()
    return render_to_response('groups.html', {'groups': groups})

def competition(request, competition_id):
    competition = get_object_or_404(Competition, pk=competition_id)
    groups = competition.groups.all()
    return render_to_response('competition.html',
            {'competition': competition, 'groups': groups})

def competitions(request):
    competitions = Competition.objects.all()
    return render_to_response('competitions.html',
                                {'competitions': competitions})
