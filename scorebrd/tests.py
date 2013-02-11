"""
This file demonstrates writing tests using the unittest module. These will pass
when you run "manage.py test".

Replace this with more appropriate tests for your application.
"""

from django.test import TestCase
from models import (Team, Event, Group, Competition, LoginForm, Match,
        TeamResult, MatchSaveForm)
from django.test.client import Client

#   class SimpleTest(TestCase):
#       def test_basic_addition(self):
#           """
#           Tests that 1 + 1 always equals 2.
#           """
#           self.assertEqual(1 + 1, 2)

#   class ModelTest(TestCase):
#       def test_models(self):
#           self.teamA = Team.objects.create(name="testTeamA")
#           self.teamB = Team.objects.create(name="testTeamB")
#           self.resultA = TeamResult.objects.create(team=self.teamA)
#           self.resultB = TeamResult.objects.create(team=self.teamB)
#           #self.match = Match.objects.create(teamA=self.teamA, teamB=self.teamB, referee=)

#           #self.group = Group.objects.create(name="testGroup", teams=[self.teamA, self.teamB, matches=[], results=[self.resultA, self.resultB])
#           #self.competition = Competition.objects.create(name="testCompetition", groups=[self.group])
#           #self.event = Event.objects.create(name="testEvent", competitions=[self.competition])

#           self.assertEqual(self.teamA.name, "testTeamA")



class LoginTest(TestCase):
    def setUp(self):
        self.client = Client()

    def test_basic_login_page(self):
        response = self.client.get('/login/')
        
        # check if page is OK
        self.assertEqual(response.status_code, 200)
        
        # check if we have a form
        self.assertTrue('form' in response.context)
        self.assertTrue(isinstance(response.context['form'], LoginForm))

        # check if we get to index (/) after logging in
        self.assertTrue('next' in response.context)
        self.assertEqual(response.context['next'], '/')

    def test_login_page_with_forward(self):
        response = self.client.get('/login/?next=/events')

        # check if page is OK
        self.assertEqual(response.status_code, 200)

        # check if we have a form
        self.assertTrue('form' in response.context)
        self.assertTrue(isinstance(response.context['form'], LoginForm))

        # check if we get to events (/events) after logging in
        self.assertTrue('next' in response.context)
        self.assertEqual(response.context['next'], '/events')
