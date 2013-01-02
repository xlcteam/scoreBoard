from django.conf.urls import patterns, include, url
from django.views.generic import TemplateView, RedirectView

# Uncomment the next two lines to enable the admin:
from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
    url(r'^$', 'scorebrd.views.index'),
    url(r'^teams/?$', 'scorebrd.views.teams'),
    url(r'^team/(?P<team_id>\d+)/?$', 'scorebrd.views.team'),
    url(r'^events/?$', 'scorebrd.views.events'),
    url(r'^event/(?P<event_id>\d+)/?$', 'scorebrd.views.event'),
    url(r'^grappelli/', include('grappelli.urls')),
    url(r'^admin/', include(admin.site.urls)),
)
