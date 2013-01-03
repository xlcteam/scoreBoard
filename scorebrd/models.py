from django.db import models
from django import forms

class LoginForm(forms.Form):
	username = forms.CharField(max_length=30)
	password = forms.CharField(widget=forms.PasswordInput(render_value=False),max_length=100)


class Team(models.Model):
    name = models.CharField(max_length=200)

    def __unicode__(self):
        return self.name


class Match(models.Model):
    teamA = models.ForeignKey(Team, related_name='homelanders')
    teamB = models.ForeignKey(Team, related_name='foreigners')
    scoreA = models.IntegerField(default=0)
    scoreB = models.IntegerField(default=0)

    def __unicode__(self):
        return self.name


class Group(models.Model):
    name = models.CharField(max_length=200)
    teams = models.ManyToManyField(Team)

    def __unicode__(self):
        return self.name


class Competition(models.Model):
    name = models.CharField(max_length=200)
    groups = models.ManyToManyField(Group)
    teams = models.ManyToManyField(Team)

    def __unicode__(self):
        return self.name


class Event(models.Model):
    name = models.CharField(max_length=200)
    competitions = models.ManyToManyField(Competition)

    def __unicode__(self):
        return self.name


class Match(models.Model):
    teamA = models.ForeignKey(Team, related_name='homelanders')
    teamB = models.ForeignKey(Team, related_name='foreigners')
    scoreA = models.IntegerField(default=0)
    scoreB = models.IntegerField(default=0)
    PLAYING_CHOICES = (
        ('N', 'Not played yet'),
        ('P', 'Playing at the moment'),
        ('D', 'Done playing'),
    )
    playing = models.CharField(max_length=1, choices=PLAYING_CHOICES,
            default='N')

    class Meta:
        verbose_name_plural = 'matches'

    def __unicode__(self):
        return self.name
