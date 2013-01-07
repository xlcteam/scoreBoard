<?php

/**
 * Group presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class GroupPresenter extends SecuredPresenter
{
        private $groups;

        private $events;
        
        public function renderDefault()
        {
                
        }
        
        public function renderNew($id = 0)
        {
        }

        public function renderList($id = 0)
        {
                if ($id === 0)
                        return $this->renderDefault();

                $this->groups = $this->getService('model')->getGroups();

                $row = $this->groups->get($id);
                if(!$row) {
                        throw new NBadRequestException('Group not found');
                }
 
                $this->template->group = $row;
                $this->template->events = $this->getService('model')->getEvents();

        }

	protected function createComponentGroupForm($id = 0)
	{
                $this->events = $this->getService('model')->getEvents();

		$form = new NAppForm;
		$form->addText('name', 'Name of the group:')
			->setRequired('Please provide a name.');
                
                $events = $this->events->fetchPairs('id', 'name');

                $form->addSelect('eventID', 'Group in event', $events)
                        ->setDefaultValue((int) $this->getParam('id'));

		$form->addSubmit('send', 'Create');

		$form->onSuccess[] = callback($this, 'groupFormSubmitted');
		return $form;
	}
        
        public function groupFormSubmitted($form)
        {
                $this->groups = $this->getService('model')->getGroups();

                if ($form['send']->isSubmittedBy()) {
                        $row = (int) $this->getParam('id');
                        $action = $this->getParam('action');
                        $values = $form->getValues();

                        if($action == 'edit') {
                                $this->groups->find($row)->update($values);
                                $this->flashMessage("Group '{$values->name}' saved.");
                        } else {
                                $this->groups->find($row)->insert($values);
                                $this->flashMessage("Group '{$values->name}' created.");
                        }
                        $this->redirect('Dashboard:');
                }
                
        }


        public function handleMatches($id)
        {
                
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=matches$id.xls");               

                $this->template->model = $this->getService('model');
                $this->template->setFile(APP_DIR."/templates/Match/table.latte");
                $this->template->groups= $this->getService('model')->getGroups();
                $this->template->id = $id;
                $this->template->names = $this->getService('model')->getTeams();

        }



        public function renderEdit($id = 0)
        {
                $this->groups = $this->getService('model')->getGroups();
                
                $form = $this['groupForm'];
                $form['send']->caption = 'Save';
                if(!$form->isSubmitted()) {
                        $row = $this->groups->get($id);
                        if(!$row) {
                                throw new NBadRequestException('Group not found');
                        }
                        $form->setDefaults($row);
                }

        }

        public function createComponentTeamList()
        {
                return new TeamList($this->getService('model'));
        }

        public function createComponentMatchList()
        {
                return new MatchList($this->getService('model'));
        }

        public function handleExport($id = 0)
        {
                if($id === 0)
                        throw new NBadRequestException('No groupID provided');

                $groups = $this->getService('model')->getGroups();

                $row = $groups->get($id);
                if(!$row) {
                        throw new NBadRequestException('Group not found');
                }
 
                $group = $row;
                $events = $this->getService('model')->getEvents();
                $results = $this->getService('model')->getResults();

                $teams = $results->where('groupID', $group->id)
                        ->order('points DESC, goal_diff DESC');
                $names = $this->getService('model')->getTeams();

                $matches = $this->getService('model')->getMatches()
                        ->where(array(
                                'groupID'=> $group->id,
                                'state' => 'played'
                        ));


                $this->template->setFile(APP_DIR."/templates/Group/grouplist.latte");
                $this->template->teams = $teams;
                $this->template->names = $names;
                $this->template->matches = $matches;
                $this->template->group = $group;

                $out = $this->template->__toString();

                $mpdf = new mPDF('c','A4','','',32,25,27,25,16,13); 

                $mpdf->SetDisplayMode('fullpage');
                #$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
                
                $mpdf->WriteHTML(file_get_contents(WWW_DIR."/css/pdf.css"), 1);
                $mpdf->WriteHTML($out, 2);
                $mpdf->Output('mpdf.pdf','I');

                exit;

        }



}
