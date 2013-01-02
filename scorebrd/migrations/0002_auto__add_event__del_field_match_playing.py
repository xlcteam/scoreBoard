# -*- coding: utf-8 -*-
import datetime
from south.db import db
from south.v2 import SchemaMigration
from django.db import models


class Migration(SchemaMigration):

    def forwards(self, orm):
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

        # Deleting field 'Match.playing'
        db.delete_column('scorebrd_match', 'playing')


    def backwards(self, orm):
        # Deleting model 'Event'
        db.delete_table('scorebrd_event')

        # Removing M2M table for field competitions on 'Event'
        db.delete_table('scorebrd_event_competitions')

        # Adding field 'Match.playing'
        db.add_column('scorebrd_match', 'playing',
                      self.gf('django.db.models.fields.CharField')(default='N', max_length=1),
                      keep_default=False)


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