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
    PLAYING_CHOICES = (
        ('N', 'Not played yet (To be played)'),
        ('P', 'Playing at the moment (Match in progress)'),
        ('D', 'Already played (Done)'),
    )
    playing = models.CharField(max_length=1, choices=PLAYING_CHOICES,
            default='N')
    referee = models.ForeignKey('auth.User')

    class Meta:
        verbose_name_plural = 'matches'

    def __unicode__(self):
        return "%s vs. %s" % (self.teamA.name, self.teamB.name)

class Group(models.Model):
    name = models.CharField(max_length=200)
    teams = models.ManyToManyField(Team)
    matches = models.ManyToManyField(Match)

    def __unicode__(self):
        return self.name

class TeamResult(models.Model):
    team = models.ForeignKey(Team)
    group = models.ForeignKey(Group)

    wins = models.IntegerField(default=0)
    draws = models.IntegerField(default=0)
    loses = models.IntegerField(default=0)

    goal_shot = models.IntegerField(default=0)
    goal_diff = models.IntegerField(default=0)
    matches_played = models.IntegerField(default=0)
    points = models.IntegerField(default=0)

    def __unicode__(self):
        return "{0} - {1} - {2} -> {3} in {4}".format(self.wins, self.draws,
                self.loses, self.team, self.group)


class Competition(models.Model):
    name = models.CharField(max_length=200)
    groups = models.ManyToManyField(Group)

    def __unicode__(self):
        return self.name


class Event(models.Model):
    name = models.CharField(max_length=200)
    competitions = models.ManyToManyField(Competition)

    def __unicode__(self):
        return self.name
