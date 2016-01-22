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
	protected function createComponentRecoveryForm() {
		$form = $this->passwordRecovery->createForm();

		$form->onSuccess[] = function(Form $form) {
			$this->flashMessage('Heslo bylo odesláno na Váš email ' . $form->getValues()['email'] . ".");
			$this->redrawControl('recoveryForm');
		};

		$form->onError[] = function() {
			$this->redrawControl('recoveryForm');
		};

		return $form;
	}
}