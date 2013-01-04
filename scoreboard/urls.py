from django.conf.urls import patterns, include, url
from django.views.generic import TemplateView, RedirectView

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
    url(r'^$', 'scorebrd.views.index'),
    url(r'^competition/(?P<competition_id>\d+)/?$', 'scorebrd.views.competition'),
    url(r'^group/(?P<group_id>\d+)/?$', 'scorebrd.views.group'),
    url(r'^teams/?$', 'scorebrd.views.teams'),
    url(r'^team/(?P<team_id>\d+)/?$', 'scorebrd.views.team'),
    url(r'^events/?$', 'scorebrd.views.events'),
    url(r'^event/(?P<event_id>\d+)/?$', 'scorebrd.views.event'),
    url(r'^matches/create/?$', 'scorebrd.views.create_matches'),
    url(r'^login/', 'scorebrd.views.my_login'),
    url(r'^logout/', 'scorebrd.views.my_logout'),
    url(r'^results/live', 'scorebrd.views.results_live'),
    url(r'^grappelli/', include('grappelli.urls')),
    url(r'^admin/', include(admin.site.urls)),
)
