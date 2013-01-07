<?php


class TeamList extends NControl
{
        /** @var NTableSelection */
        public $teams;

        /** @var NTableSelection */
        public $results;

        /** @var NTableSelection */
        public $model;

	public function __construct($model)
	{
		parent::__construct();
                $this->model = $model;
                $this->teams = $model->getTeams();
                $this->results = $model->getResults();
	}


	public function render($id)
	{
		$template = $this->template;
		$template->setFile(dirname(__FILE__) . '/TeamList.latte');
        $teams = $this->results->where('groupID', $id)
                    ->order('points DESC, goal_diff DESC');
        //dump($teams);
        $template->names = $this->teams;
        $template->teams = $teams;
		$template->render();
	}

        public function createComponentGroupList()
        {
                return new GroupList($this->model);
        }
}
