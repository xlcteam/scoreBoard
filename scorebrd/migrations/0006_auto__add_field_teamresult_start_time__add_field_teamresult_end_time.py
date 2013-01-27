# -*- coding: utf-8 -*-
import datetime
from south.db import db
from south.v2 import SchemaMigration
from django.db import models


class Migration(SchemaMigration):

    def forwards(self, orm):
        # Adding field 'TeamResult.start_time'
        db.add_column('scorebrd_teamresult', 'start_time',
                      self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime(2013, 1, 26, 0, 0)),
                      keep_default=False)

        # Adding field 'TeamResult.end_time'
        db.add_column('scorebrd_teamresult', 'end_time',
                      self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime(2013, 1, 26, 0, 0)),
                      keep_default=False)


    def backwards(self, orm):
        # Deleting field 'TeamResult.start_time'
        db.delete_column('scorebrd_teamresult', 'start_time')

        # Deleting field 'TeamResult.end_time'
        db.delete_column('scorebrd_teamresult', 'end_time')


    models = {
        'auth.group': {
            'Meta': {'object_name': 'Group'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'unique': 'True', 'max_length': '80'}),
            'permissions': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Permission']", 'symmetrical': 'False', 'blank': 'True'})
        },
        'auth.permission': {
            'Meta': {'ordering': "('content_type__app_label', 'content_type__model', 'codename')", 'unique_together': "(('content_type', 'codename'),)", 'object_name': 'Permission'},
            'codename': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'content_type': ('django.db.models.fields.related.ForeignKey', [], {'to': "orm['contenttypes.ContentType']"}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '50'})
        },
        'auth.user': {
            'Meta': {'object_name': 'User'},
            'date_joined': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'email': ('django.db.models.fields.EmailField', [], {'max_length': '75', 'blank': 'True'}),
            'first_name': ('django.db.models.fields.CharField', [], {'max_length': '30', 'blank': 'True'}),
            'groups': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Group']", 'symmetrical': 'False', 'blank': 'True'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'is_active': ('django.db.models.fields.BooleanField', [], {'default': 'True'}),
            'is_staff': ('django.db.models.fields.BooleanField', [], {'default': 'False'}),
            'is_superuser': ('django.db.models.fields.BooleanField', [], {'default': 'False'}),
            'last_login': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime.now'}),
            'last_name': ('django.db.models.fields.CharField', [], {'max_length': '30', 'blank': 'True'}),
            'password': ('django.db.models.fields.CharField', [], {'max_length': '128'}),
            'user_permissions': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['auth.Permission']", 'symmetrical': 'False', 'blank': 'True'}),
            'username': ('django.db.models.fields.CharField', [], {'unique': 'True', 'max_length': '30'})
        },
        'contenttypes.contenttype': {
            'Meta': {'ordering': "('name',)", 'unique_together': "(('app_label', 'model'),)", 'object_name': 'ContentType', 'db_table': "'django_content_type'"},
            'app_label': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'model': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '100'})
        },
        'scorebrd.competition': {
            'Meta': {'object_name': 'Competition'},
            'groups': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['scorebrd.Group']", 'symmetrical': 'False'}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '200'})
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
            'matches': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['scorebrd.Match']", 'symmetrical': 'False'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '200'}),
            'teams': ('django.db.models.fields.related.ManyToManyField', [], {'to': "orm['scorebrd.Team']", 'symmetrical': 'False'})
        },
        'scorebrd.match': {
            'Meta': {'object_name': 'Match'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'playing': ('django.db.models.fields.CharField', [], {'default': "'N'", 'max_length': '1'}),
            'referee': ('django.db.models.fields.related.ForeignKey', [], {'to': "orm['auth.User']"}),
            'scoreA': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'scoreB': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'teamA': ('django.db.models.fields.related.ForeignKey', [], {'related_name': "'homelanders'", 'to': "orm['scorebrd.Team']"}),
            'teamB': ('django.db.models.fields.related.ForeignKey', [], {'related_name': "'foreigners'", 'to': "orm['scorebrd.Team']"})
        },
        'scorebrd.team': {
            'Meta': {'object_name': 'Team'},
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '200'})
        },
        'scorebrd.teamresult': {
            'Meta': {'object_name': 'TeamResult'},
            'draws': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'end_time': ('django.db.models.fields.DateTimeField', [], {}),
            'goal_diff': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'goal_shot': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'group': ('django.db.models.fields.related.ForeignKey', [], {'to': "orm['scorebrd.Group']"}),
            'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'loses': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'matches_played': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'points': ('django.db.models.fields.IntegerField', [], {'default': '0'}),
            'start_time': ('django.db.models.fields.DateTimeField', [], {}),
            'team': ('django.db.models.fields.related.ForeignKey', [], {'to': "orm['scorebrd.Team']"}),
            'wins': ('django.db.models.fields.IntegerField', [], {'default': '0'})
        }
    }

    complete_apps = ['scorebrd']