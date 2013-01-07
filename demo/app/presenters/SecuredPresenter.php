<?php

abstract class SecuredPresenter extends BasePresenter
{
    protected function startup()
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }
}
