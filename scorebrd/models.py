from django.db import models

class Team(models.Model):
    name = models.CharField(max_length=200)

class Group(models.Model):
    name = models.CharField(max_length=200)
    teams = models.ManyToManyField(Team)

class Competition(models.Model):
    name = models.CharField(max_length=200)
    groups = models.ManyToManyField(Group)
    teams = models.ManyToManyField(Team)

class Match(models.Model):
    teamA = models.ForeignKey(Team, related_name='homelanders')
    teamB = models.ForeignKey(Team, related_name='foreigners')
    scoreA = models.IntegerField(default=0)
    scoreB = models.IntegerField(default=0)
