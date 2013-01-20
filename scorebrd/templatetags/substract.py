from django import template
from django.template.defaultfilters import stringfilter

register = template.Library()

@register.filter
def substract(a, b):
    return a - b
