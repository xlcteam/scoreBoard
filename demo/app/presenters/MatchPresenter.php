<?php

/**
 * Match presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class MatchPresenter extends SecuredPresenter 
{
        private $groups;

        private $events;
        
        public function renderDefault()
        {
                
        }
        
        public function renderGenerate($id = 0)
        {
                if ($id === 0)
                        return $this->renderDefault();

                $model = $this->getService('model');

                $group = $model
                        ->getGroups()
                        ->get($id);
                if (!$group) {
                        throw new NBadRequestException('Group not found');
                }

                $matches = $model->getMatches()->where('groupID', $group);

                if ($matches->count() !== 0) {
                        $this->template->msg = 'Some matches were generated before. There is no need to generate another.';
                } else {
                        $tmp = $model->getTeams()->where('groupID', $group)
                                                ->fetchPairs('id', 'name');

                        $teams = array();
                        foreach($tmp as $k=>$v){
                                $teams[] = array($k, $v); 
                        }


                        $combinations = $this->combinations($teams);
                        $this->template->msg = 'These combinations were generated:';

                        foreach($combinations as $combination) {
                                $matches->insert(array(
                                        'team1ID' => $combination[0][0],
                                        'team2ID' => $combination[1][0],
                                        'team1goals' => 0,
                                        'team2goals' => 0,
                                        'groupID' => $id,
                                        'userID' => $this->getUser()
                                                        ->getIdentity()->id,
                                        'state' => 'ready'
                                ));
                        }

                        $this->template->combinations = $combinations;
                }

        }

        public function renderList($id = 0)
        {
                if ($id === 0)
                        return $this->renderDefault();

                $model = $this->getService('model');

                $group = $model
                        ->getGroups()
                        ->get($id);
                if (!$group) {
                        throw new NBadRequestException('Group not found');
                }
 
                $matches = $this->getService('model')->getMatches();

                $rows = $matches->where('groupID', $id);
                $this->template->matches = $rows;
                $this->template->group = $group;

        }

        public function handleUpdate($id = 0)
        {
                if (!$this->isAjax())
                        return $this->renderDefault();

                if ($id === 0)
                        return $this->sendResponse(new NJsonResponse(
                                array('error' => 'id not provided')
                        ));
                
                $matches = $this->getService('model')->getMatches();
                $match = $matches->get($id);

                if(!$match)
                        return $this->sendResponse(new NJsonResponse(
                                array('error' => 'match not found')
                        ));
                
                if ($match['userID'] != $this->getUser()->getIdentity()->id)
                        return $this->sendResponse(new NJsonResponse(
                                array('error' => 'You are not authorized to do this.')
                        ));


                $match['state'] = 'playing';


                $data = $this->request->post;
                $action = $data['action'];
                switch($action){
                        case 'team1_increase':
                                $match['team1goals'] = $match['team1goals'] + 1;
                                break;

                        case 'team2_increase':
                                $match['team2goals'] = $match['team2goals'] + 1;
                                break;
                        case 'team1_decrease':
                                $match['team1goals'] = ($match['team1goals'] > 0) ? ($match['team1goals'] - 1): 0;
                                break;

                        case 'team2_decrease':
                                $match['team2goals'] = ($match['team2goals'] > 0) ? ($match['team2goals'] - 1): 0;
                                break;

                        case 'finish':
                                $match['team1goals'] = $data['team1goals'];
                                $match['team2goals'] = $data['team2goals'];
                                $match['state'] = 'played';
                                $match['date'] = 'NOW()';
                                break;


                }

                $match->update();

                $this->sendResponse(new NJsonResponse(
                        array('success' => true)
                ));

        }

        public function renderPlay($id = 0)
        {
                if ($id === 0)
                        return $this->renderDefault();

                $model = $this->getService('model');
                $matches = $model->getMatches();

                $match = $matches->get($id);
                if(!$match)
                        throw new NBadRequestException('Match not found');

                

                if($match->state !== 'ready' && 
                   $match->userID !== $this->getUser()->getIdentity()->id){
                        $this->flashMessage('Match already started.');
                        $this->redirect('Group:list', $match['groupID']);
                }

                // reset score if already started 
                if($match->state === 'playing') {
                        $match->userID = $this->getUser()->getIdentity()->id;
                        $match->team1goals = 0;
                        $match->team2goals = 0;
                }

                $teams = $model->getTeams();

                $match['userID'] = $this->getUser()
                                        ->getIdentity()->id;

                $match->update();
                $match = $matches->get($id);

                $this->template->match = $match;
                $this->template->teams = $teams;
                $this->template->id = $id;
                $this->template->gid = $match['groupID'];

        }

        public function renderFinish($id = 0)
        {
                if ($id == 0)
                        return $this->renderDefault();

                $match = $this->getService('model')->getMatches()->get($id);

                if ($match['userID'] != $this->getUser()->getIdentity()->id) {
                        $this->flashMessage('You are not authorized to do that.');
                        $this->redirect('Group:list', $match['groupID']);
                }
                
                $results = $this->getService('model')->getResults();
               
                if($match['team1goals'] == $match['team2goals']){
                        $result = $results->get($match['team1ID']);

                        $result->matches_played = $result->matches_played + 1;
                        $result->draws = $result->draws + 1;
                        $result->points = $result->points + 1;
                        $result->goals_shot = $result->goals_shot + $match['team1goals'];
                        $result->update();

                        $result = $results->get($match['team2ID']);
                        
                        $result->matches_played = $result->matches_played + 1;
                        $result->points = $result->points + 1;
                        $result->draws = $result->draws + 1;
                        $result->goals_shot = $result->goals_shot + $match['team2goals'];
                        $result->update();

                } else {
                        $score1 = $match['team1goals'];
                        $score2 = $match['team2goals'];

                        $winnerID = ($score1 > $score2)?$match['team1ID']:$match['team2ID'];
                        $loserID = ($score1 > $score2)?$match['team2ID']:$match['team1ID'];
                        $winnerGoalDiff = ($score1 > $score2)?($score1 -$score2):($score2-$score1);
                        $winnerGoals = max($score1, $score2);
                        $loserGoals = min($score1, $score2);

                        $result = $results->get($winnerID);

                        $result->matches_played = $result->matches_played + 1;
                        $result->wins = $result->wins + 1;
                        $result->goal_diff = $result->goal_diff + $winnerGoalDiff;
                        $result->points = $result->points + 3;
                        $result->goals_shot = $result->goals_shot + $winnerGoals;
                        $result->update();

                        $result = $results->get($loserID);
                        
                        $result->matches_played = $result->matches_played + 1;
                        $result->loses = $result->loses + 1;
                        $result->goal_diff = $result->goal_diff - $winnerGoalDiff;
                        $result->goals_shot = $result->goals_shot + $loserGoals;
                        $result->update();
                }

               


                $names = $this->getService('model')->getTeams();

                $this->flashMessage("Match '".$names[$match['team1ID']]->name.
                        "' vs. '". $names[$match['team2ID']]->name .
                        "' finished with result ". $match['team1goals'] . ":" . 
                        $match['team2goals']);



                $this->redirect('Group:list', $match['groupID']);



        }

        public function createComponentTeamList()
        {
                return new TeamList($this->getService('model'));
        }

        public function createComponentMatchList()
        {
                return new MatchList($this->getService('model'));
        }



        /*
         * Returns all possible two items combinations from an array
         */
        private function combinations($array) {
                $out = array();
                
                for($i = 0; $i < count($array); $i++) {
                        for($j = $i; $j < count($array); $j++) {
                                if($i !== $j)
                                        $out[] = array($array[$i], 
                                                        $array[$j]);
                        }
                }

                return $out;
        }


}
