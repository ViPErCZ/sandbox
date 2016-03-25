<?php

namespace Component\UserWidget;
use Component\Base\WithLogged\BaseControl;
use Model\Permission\Entity\UserEntity;
use Model\Permission\RoleRepository;
use Model\Permission\UserRepository;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;
use Nette\Diagnostics\Debugger;
use Nette\Security\User;

/**
 * Description of UserForm
 *
 * @author Martin Chudoba
 */
class UserForm extends BaseControl {

	/**
	 * @persistent
	 * @var int
	 */
	public $rsuserID;

	/** @var \Model\Permission\UserRepository */
	private $userRepository;

	/** @var \Model\Permission\RoleRepository */
	private $roleRepository;

	/** @var User */
	private $user;

	/** messages */
	const PERMISSION = "Na tuto operaci nemáte dostatečná oprávnění";

	/**
	 * @param UserRepository $userRepository
	 * @param RoleRepository $roleRepository
	 * @param User $user
	 */
	public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, User $user) {
		parent::__construct();
		$this->userRepository = $userRepository;
		$this->roleRepository = $roleRepository;
		$this->user = $user;
	}

	/** Nastaví rsuserID
	 *
	 * @param int $rsuserID
	 */
	public function setUserID($rsuserID) {
		$this->rsuserID = $rsuserID;
	}

	/** Render
	 *
	 */
	public function render() {
		$template = $this->template;
		$template->setFile(dirname(__FILE__) . "/latte/form.latte");

		$template->render();
	}

	/**
	 * @param Form $form
	 */
	public function Submit(Form $form) {
		$json = new \stdClass();
		$json->result = "success";
		$values = $form->getValues();
		$result = false;

		if (!empty($values['userID'])) {
			$userEntity = $this->userRepository->get($values['userID']);
			if ($userEntity) {
				if ($this->user->isAllowed("user_management", "edit")) {
					$userEntity->setLogin($values['login']);
					if (!empty($values['password1'])) {
						$userEntity->setPassword($values['password1']);
					}
					if ($userEntity->getLogin() !== "root" && $userEntity->getUserID() !== $this->user->getId()) {
						$userEntity->setActive($values['active']);

						if ($userEntity->getRole()->getAclRoleID() != $this->user->getIdentity()->data['aclRoleID']) {
							$userEntity->setAclRoleID($values['role']);
						}
					}
					try {
						$result = $this->userRepository->save();
					} catch (\PDOException $e) {
						$result = $e->getMessage();
					}
				} else {
					$result = UserForm::PERMISSION;
				}
			}
		} else {
			if ($this->user->isAllowed("user_management", "add")) {
				$userEntity = new UserEntity();
				$userEntity->setLogin($values['login'])
					->setPassword($values['password1'])
					->setActive($values['active'])
					->setAclRoleID($values['role']);
				try {
					$result = $this->userRepository->push($userEntity)->save();
				} catch (\PDOException $e) {
					$result = $e->getMessage();
					if (preg_match("/Duplicate entry/", $result)) {
						$result = "Nick <strong>" . $values['login'] . "</strong> již existuje. Zvolte prosím jiný login.";
					}
				}
				if ($result instanceof UserEntity || $result === TRUE) {
					$result = TRUE;
				}
			} else {
				$result = UserForm::PERMISSION;
			}
		}

		if ($result === TRUE) {
			$json->result = "success";
		} else {
			$json->result = "error";
			$json->message = $result;
		}
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}

	/** Vytvoření komponenty
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm()
	{
		$form = new Form();
		$form->addText("login", "Přihlašovací jméno:")
			->setAttribute("autocomplete", "off")
			->setRequired("Prosím zadejte přihlašovací jméno.");
		$form->addPassword("password1", "Heslo:")->setAttribute("class", "form-control")->setAttribute("autocomplete", "off");
		$form->addPassword("password2", "Heslo pro kontrolu:")->setAttribute("class", "form-control")->setAttribute("autocomplete", "off");
		$roles = $this->roleRepository->read()->where("name != ?", "root");
		if (!$this->user->isInRole("root")) {
			$roles->where("name NOT(?)", $this->user->getRoles());
		}
		$roles = $roles->fetchPairs("aclRoleID", "name");
		$form->addSelect("role", "Oprávnění:", $roles)->setAttribute("class", "form-control");
		$form->addCheckbox("active", "Aktivní");
		$form->addButton("cancel", "Storno")->setHtmlId("cancel");
		$form->addSubmit("sender", "Uložit změny")->setHtmlId("sender");
		$form->addHidden("userID");
		$form['password2']->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password1']);

		if ($this->rsuserID) {
			$userEntity = $this->userRepository->get($this->rsuserID);
			if ($userEntity) {
				$form['login']->setValue($userEntity->login);
				$form['login']->setAttribute("readonly");
				$form['userID']->setValue($this->rsuserID);
				$form['active']->setValue($userEntity->getActive());
				if ($userEntity->getUserID() == $this->user->getId()) {
					$form['role']->setDisabled();
				}
				if ($userEntity->getLogin() != "root" && $userEntity->getUserID() !== $this->user->getId()) {
					$form['role']->setValue($userEntity->aclRoleID);
				}
			}
		} else {
			$form['password1']->setRequired("Prosím zadejte heslo.");
		}

		$form->onSuccess[] = array($this, "Submit");
		$form->onError[] = array($this, "FormError");

		return $form;
	}

}

?>
