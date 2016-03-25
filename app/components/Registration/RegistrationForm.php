<?php
/**
 * User: Martin
 * Date: 29.11.13
 * Time: 13:30
 */

namespace Component\Registration;


use Component\Base\WithLogged\BaseControl;
use Model\Contact\ContactRepository;
use Model\Contact\Entity\ContactEntity;
use Model\Permission\Entity\UserEntity;
use Nette\Application\UI\Form;

class RegistrationForm extends BaseControl {

	/** @var \Model\Contact\ContactRepository */
	private $contactRepository;

	/**
	 * @param ContactRepository $contactRepository
	 */
	public function __construct(ContactRepository $contactRepository) {
		parent::__construct();

		$this->contactRepository = $contactRepository;
	}

	/** Render
	 *
	 */
	public function render() {
		$template = $this->getTemplate();
		$template->setFile(__DIR__ . "/latte/form.latte");

		$template->render();
	}

	/** Create Form
	 * @return Form
	 */
	protected function createComponentRegistrationForm() {
		$form = new Form();
		$form->addText("firstname", "Jméno")
				->addRule(Form::FILLED, "Prosím zadejte Vaše křestní jméno.");
		$form->addText("lastname", "Příjmení")
			->addRule(Form::FILLED, "Prosím zadejte Vaše příjmení.");
		$form->addText("login", "Email:")
			->setAttribute("autocomplete", "off")
			->addRule(Form::EMAIL, "Prosím zadejte Vaši emailovou schránku.");
		$form->addPassword("password", "Heslo")
			->setAttribute("autocomplete", "off")
			->addRule(Form::FILLED, "Prosím zadejte Vaše heslo.");
		$form->addPassword("password2", "Heslo pro kontrolu")
			->setAttribute("autocomplete", "off")
			->addRule(Form::FILLED, "Prosím zadejte heslo raději pro kontrolu ještě jednou.")
			->addCondition(Form::FILLED)
				->addRule(Form::EQUAL, "Hesla se musí shodovat. Prosím zadejte obě hesla shodná.", $form['password']);
		$form->addSubmit("sender", "Registrovat se");

		$form->onSuccess[] = array($this, "Submit");
		$form->addProtection('Vypršel časový limit, odešlete formulář znovu');

		return $form;
	}

	/** Submit
	 * @param Form $form
	 */
	public function Submit(Form $form) {
		$values = $form->getValues();
		$contactEntity = new ContactEntity();
		$userEntity = new UserEntity();
		$contactEntity->setValues((array)$values);
		$userEntity->setLogin($values->login);
		$userEntity->setAclRoleID(10); // guest role
		$userEntity->setPassword($values->password);
		$userEntity->setActive(TRUE);
		$contactEntity->setUser($userEntity);

		try {
			$this->contactRepository->push($contactEntity);
			$result = $this->contactRepository->save();

			if ($result) {
				$this->flashMessage("Vaše registrace proběhla úspěšně.");
				$this->redirect('this');
			} else {
				$form->addError("Vaše registrace neproběhla úspěšně.");
			}
		} catch (\PDOException $e) {
			if (strpos($e->getMessage(), "1062 Duplicate entry") !== FALSE) {
				$form->addError("Uživatel $values->login již existuje. Zvolte si prosím jiný přihlašovací email.");
			} else {
				$form->addError($e->getMessage());
			}
		}
	}
} 