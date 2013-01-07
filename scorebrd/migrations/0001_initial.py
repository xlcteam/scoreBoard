# -*- coding: utf-8 -*-
import datetime
from south.db import db
from south.v2 import SchemaMigration
from django.db import models


class Migration(SchemaMigration):

    def forwards(self, orm):
        # Adding model 'Team'
        db.create_table('scorebrd_team', (
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('name', self.gf('django.db.models.fields.CharField')(max_length=200)),
        ))
        db.send_create_signal('scorebrd', ['Team'])

        # Adding model 'Match'
        db.create_table('scorebrd_match', (
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('teamA', self.gf('django.db.models.fields.related.ForeignKey')(related_name='homelanders', to=orm['scorebrd.Team'])),
            ('teamB', self.gf('django.db.models.fields.related.ForeignKey')(related_name='foreigners', to=orm['scorebrd.Team'])),
            ('scoreA', self.gf('django.db.models.fields.IntegerField')(default=0)),
            ('scoreB', self.gf('django.db.models.fields.IntegerField')(default=0)),
        ))
        db.send_create_signal('scorebrd', ['Match'])

        # Adding model 'Group'
        db.create_table('scorebrd_group', (
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('name', self.gf('django.db.models.fields.CharField')(max_length=200)),
        ))
        db.send_create_signal('scorebrd', ['Group'])

        # Adding M2M table for field teams on 'Group'
        db.create_table('scorebrd_group_teams', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('group', models.ForeignKey(orm['scorebrd.group'], null=False)),
            ('team', models.ForeignKey(orm['scorebrd.team'], null=False))
        ))
        db.create_unique('scorebrd_group_teams', ['group_id', 'team_id'])

        # Adding model 'Competition'
        db.create_table('scorebrd_competition', (
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('name', self.gf('django.db.models.fields.CharField')(max_length=200)),
        ))
        db.send_create_signal('scorebrd', ['Competition'])

        # Adding M2M table for field groups on 'Competition'
        db.create_table('scorebrd_competition_groups', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('competition', models.ForeignKey(orm['scorebrd.competition'], null=False)),
            ('group', models.ForeignKey(orm['scorebrd.group'], null=False))
        ))
        db.create_unique('scorebrd_competition_groups', ['competition_id', 'group_id'])

        # Adding M2M table for field teams on 'Competition'
        db.create_table('scorebrd_competition_teams', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('competition', models.ForeignKey(orm['scorebrd.competition'], null=False)),
            ('team', models.ForeignKey(orm['scorebrd.team'], null=False))
        ))
        db.create_unique('scorebrd_competition_teams', ['competition_id', 'team_id'])

        # Adding model 'Event'
        db.create_table('scorebrd_event', (
            ('id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('name', self.gf('django.db.models.fields.CharField')(max_length=200)),
        ))
        db.send_create_signal('scorebrd', ['Event'])

        # Adding M2M table for field competitions on 'Event'
        db.create_table('scorebrd_event_competitions', (
            ('id', models.AutoField(verbose_name='ID', primary_key=True, auto_created=True)),
            ('event', models.ForeignKey(orm['scorebrd.event'], null=False)),
            ('competition', models.ForeignKey(orm['scorebrd.competition'], null=False))
        ))
        db.create_unique('scorebrd_event_competitions', ['event_id', 'competition_id'])


    def backwards(self, orm):
        # Deleting model 'Team'
        db.delete_table('scorebrd_team')

        # Deleting model 'Match'
        db.delete_table('scorebrd_match')

        # Deleting model 'Group'
        db.delete_table('scorebrd_group')

        # Removing M2M table for field teams on 'Group'
        db.delete_table('scorebrd_group_teams')

        # Deleting model 'Competition'
        db.delete_table('scorebrd_competition')

        # Removing M2M table for field groups on 'Competition'
        db.delete_table('scorebrd_competition_groups')

        # Removing M2M table for field teams on 'Competition'
        db.delete_table('scorebrd_competition_teams')

        # Deleting model 'Event'
        db.delete_table('scorebrd_event')

        # Removing M2M table for field competitions on 'Event'
        db.delete_table('scorebrd_event_competitions')


    models = {
        'scorebrd.competition': {
            'Meta': {'object_name': 'Competition'},
            'groups': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['scorebrd.Group']", 'symmetrical': 'False'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '200'}),
            'teams': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['scorebrd.Team']", 'symmetrical': 'False'})
        },
        'scorebrd.event': {
            'Meta': {'object_name': 'Event'},
            'competitions': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['scorebrd.Competition']", 'symmetrical': 'False'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '200'})
        },
        'scorebrd.group': {
            'Meta': {'object_name': 'Group'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '200'}),
            'teams': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['scorebrd.Team']", 'symmetrical': 'False'})
        },
        'scorebrd.match': {
            'Meta': {'object_name': 'Match'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'scoreA': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'scoreB': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'teamA': ('django.db.models.fields.related.ForeignKey', [], {'related_name': "'homelanders'", 'to': "orm['scorebrd.Team']"}),
            'teamB': ('django.db.models.fields.related.ForeignKey', [], {'related_name': "'foreigners'", 'to': "orm['scorebrd.Team']"})
        },
        'scorebrd.team': {
            'Meta': {'object_name': 'Team'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '200'})
        }
    }

    complete_apps = ['scorebrd']