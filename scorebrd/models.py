from django.db import models

class Group(models.Model):
    name = models.CharField(max_length=200)

class Competition(models.Model):
    name = models.CharField(max_length=200)
    groups = models.ManyToManyField(Group)

class Team(models.Model):
    name = models.CharField(max_length=200)
    competition = models.ForeignKey(Competition)

class Match(models.Model):
    teamA = models.ForeignKey(Team)
    teamB = models.ForeignKey(Team)
    scoreA = models.IntegerField(default=0)
    scoreB = models.IntegerField(default=0)
