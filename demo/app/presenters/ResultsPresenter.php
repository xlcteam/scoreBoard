<?php

/**
 * Dashboard presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class ResultsPresenter extends SecuredPresenter
{

	public function renderDefault()
	{
	}

        public function renderLive()
        {
                $model = $this->getService('model');

                $active_events = $model->getEvents()->where('finished', false);
                $active_groups = $model->getGroups()->where('eventID', $active_events);

                $matches = $model->getMatches();
                $playing_matches = $matches->where(
                        array('groupID' => $active_groups,
                                'state' => 'playing'));

                $results = $model->getResults();


                $this->template->groups = $active_groups;                
                $this->template->events = $model->getEvents();;                
                $this->template->model = $model;

                $this->template->matches = $playing_matches;
                $this->template->names = $model->getTeams();

                





        }

        public function handleUpdate()
        {
              //if (!$this->isAjax())
              //        return $this->renderDefault();

                $model = $this->getService('model');

                $active_events = $model->getEvents()->where('finished', false);
                $active_groups = $model->getGroups()->where('eventID', $active_events);

                $matches = $model->getMatches();
                $playing_matches = $matches->where(
                        array('groupID' => $active_groups,
                                'state' => 'playing'));

                $results = $model->getResults();

                $tmp = $this->template;
                $group_template = $this->template;
                $group_template->setFile(APP_DIR . '/templates/Results/grouplist.latte');

                $group_template->groups = $active_groups;                
                $group_template->model = $model;

                $teams = $model->getTeams();

                $group_template->matches = $playing_matches;
                $group_template->names = $teams;
                $group_template->events = $model->getEvents();
                $groups = $group_template->__toString(TRUE);

                $tmp->setFile(APP_DIR . '/templates/Results/matchlist.latte');
                $tmp->matches = $playing_matches;
                $tmp->names = $teams;
                $matches = $tmp->__toString(TRUE);

                return $this->sendResponse(new NJsonResponse(
                        array('groups' => $groups,
                                'matches' => $matches)
                ));
        }

        public function createComponentEventList()
        {
                return new EventList($this->getService('model'));
        }

        public function createComponentGroupList()
        {
                return new GroupList($this->getService('model'));
        }
        
}
