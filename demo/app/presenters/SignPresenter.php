<?php



/**
 * Sign in/out presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class SignPresenter extends BasePresenter
{
        /** @persistent */                                                    
        public $backlink = '';                                                



	/**
	 * Sign in form component factory.
	 * @return NAppForm
	 */
	protected function createComponentSignInForm()
	{
		$form = new NAppForm;
		$form->addText('username', 'Username:')
			->setRequired('Please provide a username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please provide a password.');

		$form->addCheckbox('remember', 'Remember me on this computer');

		$form->addSubmit('send', 'Sign in');

		$form->onSuccess[] = callback($this, 'signInFormSubmitted');
		return $form;
	}

	/**
	 * Sign up form component factory.
	 * @return NAppForm
	 */
	protected function createComponentSignUpForm()
	{
		$form = new NAppForm;
		$form->addText('name', 'Name:')
			->setRequired('Please provide a name.');
        
                $form->addText('username', 'Username:')
			->setRequired('Please provide a username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please provide a password.');

		$form->addSubmit('send', 'Sign up');

		$form->onSuccess[] = callback($this, 'signUpFormSubmitted');
		return $form;
	}



	public function signInFormSubmitted($form)
	{
                
		try {
			$values = $form->getValues();
			if ($values->remember) {
				$this->getUser()->setExpiration('+ 14 days', FALSE);
			} else {
				$this->getUser()->setExpiration('+ 2 hours', TRUE);
			}
			$this->getUser()->login($values->username, $values->password);
                        $this->application->restoreRequest($this->backlink);  
			$this->redirect('Dashboard:');

		} catch (NAuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	public function signUpFormSubmitted($form)
	{
		try {
			$values = $form->getValues();
                        $values->password = $this->calculateHash($values->password);
			$this->getService('model')->getUsers()
                                ->insert($values);

			$this->redirect('Dashboard:');

		} catch (NAuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}



	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('in');
	}

	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public function calculateHash($password)
	{
		return hash('sha512', $password . str_repeat($this->context->params['salt'], 10));
	}

}
