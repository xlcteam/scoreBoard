<?php


class EventList extends NControl
{
        /** @var NTableSelection */
        public $events;

        /** @var NTableSelection */
        public $model;

	public function __construct($model)
	{
		parent::__construct();
                $this->model = $model;
                $this->events = $model->getEvents()->order('finished');
	}


	public function render()
	{
		$template = $this->template;
		$template->setFile(dirname(__FILE__) . '/EventList.latte');
                $template->events = $this->events;
		$template->render();
	}

        public function createComponentGroupList()
        {
                return new GroupList($this->model);
        }
}
