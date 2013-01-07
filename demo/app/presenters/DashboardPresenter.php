<?php

/**
 * Dashboard presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class DashboardPresenter extends SecuredPresenter
{

	public function renderDefault()
	{
    $this->template->model = $this->getService('model');
	}

        public function createComponentEventList()
        {
                return new EventList($this->getService('model'));
        }
        
        public function createComponentGroupList()
        {
                return new GroupList($this->getService('model'));
        }

        public function createComponentMatchList()
        {
                return new MatchList($this->getService('model'));
        }
}
