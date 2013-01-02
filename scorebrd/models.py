from django.db import models

class Team(models.Model):
    name = models.CharField(max_length=200)


class Match(models.Model):
    teamA = models.ForeignKey(Team, related_name='homelanders')
    teamB = models.ForeignKey(Team, related_name='foreigners')
    scoreA = models.IntegerField(default=0)
    scoreB = models.IntegerField(default=0)


class Group(models.Model):
    name = models.CharField(max_length=200)
    teams = models.ManyToManyField(Team)


class Competition(models.Model):
    name = models.CharField(max_length=200)
    groups = models.ManyToManyField(Group)
    teams = models.ManyToManyField(Team)


class Event(models.Model):
    name = models.CharField(max_length=200)
    competitions = models.ManyToManyField(Competition)


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
    playing = models.CharField(max_length=1, choices=PLAYING_CHOICES)

    class Meta:
        verbose_name_plural = 'matches'
