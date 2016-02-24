<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 4.1.16
 * Time: 14:15
 */

namespace App;

use Nette\Application\UI\Form;
use Sandbox\PasswordRecovery\PasswordRecovery;

/**
 * Class PassRestorePresenter
 * @package App
 */
class PassRestorePresenter extends BasePresenter {

	/** @var PasswordRecovery */
	protected $passwordRecovery;

	/**
	 * @param PasswordRecovery $passwordRecovery
	 */
	public function injectPasswordRecovery(PasswordRecovery $passwordRecovery) {
		$this->passwordRecovery = $passwordRecovery;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentRecovery() {
		$control = $this->passwordRecovery->createDialog();

		$control->getResetForm()->onSuccess[] = function(Form $form) {
			$this->flashMessage('Odkaz pro obnovu hesla byl odeslán na Váš email ' . $form->getValues()['email'] . ".");
			$this->redrawControl('recoveryForm');
		};

		$control->getResetForm()->onError[] = function() {
			$this->redrawControl('recoveryForm');
		};

		$control->getNewPasswordForm()->onSuccess[] = function() {
			$this->flashMessage('Heslo bylo úspěšně nastaveno. Pokračujte na přihlašovací obrazovku.');
			$this->redrawControl('recoveryForm');
		};

		$control->getNewPasswordForm()->onError[] = function() {
			$this->redrawControl('recoveryForm');
		};

		return $control;
	}
}