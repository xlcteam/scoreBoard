from django.conf.urls import patterns, include, url
from django.views.generic import TemplateView, RedirectView

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
    url(r'^$', 'scorebrd.views.index'),
    url(r'^login/', 'scorebrd.views.my_login'),
    url(r'^logout/', 'scorebrd.views.my_logout'),
    url(r'^results/live', 'scorebrd.views.results_live'),
    url(r'^grappelli/', include('grappelli.urls')),
    url(r'^admin/', include(admin.site.urls)),

    url(r'^events/?$', 'scorebrd.views.events'),
    url(r'^event/(?P<event_id>\d+)/?$', 'scorebrd.views.event'),
    url(r'^competitions/?$', 'scorebrd.views.competitions'),
    url(r'^competition/(?P<competition_id>\d+)/?$', 'scorebrd.views.competition'),
    url(r'^groups/?$', 'scorebrd.views.groups'),
    url(r'^group/(?P<group_id>\d+)/?$', 'scorebrd.views.group'),
    url(r'^teams/?$', 'scorebrd.views.teams'),
    url(r'^team/(?P<team_id>\d+)/?$', 'scorebrd.views.team'),
    url(r'^matches/generate/?$', 'scorebrd.views.matches_generate_listing'),
    url(r'^matches/generate/(?P<group_id>\d+)/?$', 'scorebrd.views.matches_generate'),

    url(r'^match/(?P<match_id>\d+)/?$', 'scorebrd.views.match_view'),
    url(r'^results/?$', 'scorebrd.views.results'),
    url(r'^results/group/(?P<group_id>\d+)/?$', 'scorebrd.views.results_group_view'),
    url(r'^results/team/(?P<team_id>\d+)/?$', 'scorebrd.views.results_team_view'),
)
