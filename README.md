scoreBoard
==========

scoreBoard is a simple tool for displaying the score of any game. <br>
The [original scoreBoard](https://github.com/xlcteam/scoreBoard-php) has been written in messy PHP.

## Dependencies
The only real dependency is `python` (preferably cpython2) and `pip` so that you
can install other dependencies (see requirements.txt).

## Installing
* You need to create some virtual environment
* Then install all the dependencies by running `$ pip install -r requirements.txt`
* Then, to get all we need `$ python manage.py collectstatic`
* Now we shall sync the DB `$ python manage.py syncdb`


*Note*: This application is still in the alpha version and not meant for actual use. Please [contact us](http://xlc-team.info/contact) if you plan on using it.

We really appreciate feedback, so if you have any questions or find any bug please create [new issue](https://github.com/xlcteam/scoreBoard/issues/new).

## Some screenshots of this application
![dashboard](http://xlcteam.github.com/scoreBoard/imgs/screenshots/0.png)
![group](http://xlcteam.github.com/scoreBoard/imgs/screenshots/5.png)
![match](http://xlcteam.github.com/scoreBoard/imgs/screenshots/7.png)

(c) 2012 - 2013, XLC Team, http://xlc-team.info
