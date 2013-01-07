<?php


/**
 * Model base class.
 */
class Model extends NObject
{
	/** @var NConnection */
	public $database;



	public function __construct(NConnection $database)
	{
		$this->database = $database;
	}

        public function getUsers()
        {
                return $this->database->table('users');
        }

        public function getEvents()
        {
                return $this->database->table('events');
        }

        public function getGroups()
        {
                return $this->database->table('groups');
        }

        public function getTeams()
        {
                return $this->database->table('teams');
        }

        public function getResults()
        {
                return $this->database->table('results');
        }

        public function getMatches()
        {
                return $this->database->table('matches');
        }


	public function createAuthenticatorService()
	{
		return new Authenticator($this->database->table('users'));
	}

}
