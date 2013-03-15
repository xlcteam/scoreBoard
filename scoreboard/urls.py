from django.conf.urls import patterns, include, url
from django.views.generic import TemplateView, RedirectView

urlpatterns = patterns('scorebrd.views',
    url(r'^$', 'index', name='index'),
    # url(r'^login/', 'my_login', name='login'),
    # url(r'^logout/', 'my_logout', name='logout'),
    url(r'^results/live', 'results_live'),

    url(r'^events/?$', 'events'),
    url(r'^event/(?P<event_id>\d+)/?$', 'event', name="event"),
    url(r'^event/new/?$', 'new_event', name='new_event'),

    url(r'^competitions/?$', 'competitions'),
    url(r'^competition/(?P<competition_id>\d+)/?$', 'competition', name="competition"),
    url(r'^competition/new/?$', 'new_competition', name="new_competition"),

    url(r'^groups/?$', 'groups'),
    url(r'^group/(?P<group_id>\d+)/?$', 'group', name="group"),
    url(r'^group/new/?$', 'new_group', name='new_group'),

    url(r'^teams/?$', 'teams'),
    url(r'^team/(?P<team_id>\d+)/?$', 'team', name="team"),
    url(r'^team/new/?$', 'new_team', name='new_team'),

    url(r'^matches/generate/?$', 'matches_generate_listing'),
    url(r'^matches/generate/(?P<group_id>\d+)/?$', 'matches_generate'),

    url(r'^match/play/(?P<match_id>\d+)/?$', 'match_play',
        name='match_play'),
    url(r'^match/save/(?P<match_id>\d+)/?$', 'match_save',
        name='match_save'),

    url(r'^results/?$', 'results'),
    url(r'^results/live/?$', 'results_live', name="results_live"),
    url(r'^results/group/(?P<group_id>\d+)/?$', 'results_group_view'),
    url(r'^results/group/(?P<group_id>\d+)\.pdf/?$', 'results_group_pdf'),
    url(r'^results/competition/(?P<competition_id>\d+)\.pdf/?$', 'results_competition_pdf'),
    url(r'^results/event/(?P<event_id>\d+)\.pdf/?$', 'results_event_pdf'),
    url(r'^results/team/(?P<team_id>\d+)/?$', 'results_team_view'),
    url(r'^results/match/(?P<match_id>\d+)/?$', 'results_match_view'),
)
