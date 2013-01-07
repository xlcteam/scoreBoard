<?php


class GroupList extends NControl
{
        /** @var NTableSelection */
        public $model;

	public function __construct($model)
	{
		parent::__construct();
                $this->model = $model;

	}


	public function render($event)
	{
		$template = $this->template;
                $template->groups = $this->model->getGroups()->where('eventID', $event);
		$template->setFile(dirname(__FILE__) . '/GroupList.latte');
		$template->render();
	}

}
