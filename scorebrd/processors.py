
def user(request):
    if hasattr(request, 'user'):
        return {'user': request.user }
    return {}

def next(request):
    url = '/'
    if 'next' in request.GET:
        url = request.GET['next']
    return {'next': url}
 
