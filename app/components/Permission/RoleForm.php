<?php

namespace Component\Permission;
use Model\Permission\ActionRepository;
use Model\Permission\ModelRepository;
use Model\Permission\PermissionManager;
use Model\Permission\PermissionRepository;
use Model\Permission\ResourceRepository;
use Model\Permission\RoleRepository;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Diagnostics\Debugger;
use Nette\Security\User;

/**
 * Description of RoleForm
 *
 * @author Martin Chudoba
 */
class RoleForm extends Control {
	
	/** @var RoleRepository */
	private $roleRepository;
	
	/** @var  ResourceRepository */
	private $resourceRepository;

	/** @var ActionRepository */
	private $actionRepository;

	/** @var  ModelRepository */
	private $modelRepository;

	/** @var \Model\Permission\PermissionManager  */
	private $permissionManager;

	/** @var \Model\Permission\PermissionRepository  */
	private $permissionRepository;
	
	/** @var int */
	private $aclRoleID;

	/** @var User */
	private $user;

	/** messages */
	const PERMISSION = "Na tuto operaci nemáte dostatečná oprávnění";


	/**
	 * @param RoleRepository $roleRepository
	 * @param ModelRepository $modelRepository
	 * @param PermissionManager $permissionManager
	 * @param PermissionRepository $permissionRepository
	 * @param ResourceRepository $resourceRepository
	 * @param ActionRepository $actionRepository
	 * @param User $user
	 */
	public function __construct(RoleRepository $roleRepository, ModelRepository $modelRepository, PermissionManager $permissionManager,
								PermissionRepository $permissionRepository, ResourceRepository $resourceRepository,
								ActionRepository $actionRepository, User $user) {
		parent::__construct();
		
		$this->roleRepository = $roleRepository;
		$this->modelRepository = $modelRepository;
		$this->resourceRepository = $resourceRepository;
		$this->actionRepository = $actionRepository;
		$this->permissionManager = $permissionManager;
		$this->permissionRepository = $permissionRepository;
		$this->user = $user;
		$this->aclRoleID = NULL;
	}

	/**
	 * 
	 * @return int|NULL
	 */
	public function getAclRoleID() {
		return $this->aclRoleID;
	}

	/**
	 * 
	 * @param int $aclRoleID
	 * @return \Component\Permission\RoleForm
	 */
	public function setAclRoleID($aclRoleID) {
		$this->aclRoleID = $aclRoleID;
		return $this;
	}
	
	/** Render
	 * 
	 */
	public function render() {
		$template = $this->template;
		$template->setFile(__DIR__ . "/latte/roleForm.latte");

		$template->resources = $this->resourceRepository->read();
		$template->render();
	}
	
	/** Vytvoření formuláře
	 * 
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm() {
		$form = new Form();
		$form->getElementPrototype()->id = "roleForm";
		$form->addText("name", "Jméno skupiny:")
				->setHtmlId("name")
				->setRequired("Prosím zadejte jméno skupiny oprávnění.");
		$form->addHidden("aclRoleID");
		$form->addButton("cancel", "Storno")->setHtmlId("cancel");
		$form->addSubmit("sender", "Uložit změny")->setHtmlId("sender");

		$resources = $this->resourceRepository->read();

		foreach($resources as $resource) {
			$container = $form->addContainer($resource->aclResourceID);
			$actions = $this->modelRepository->read()->where("aclResourceID", $resource->aclResourceID);
			foreach($actions as $key => $action) {
				$checkbox = $container->addCheckbox($key, $action->getAclAction()->humanName);
				if ($this->aclRoleID) {
					$permissions = $this->permissionRepository->read()
						->where("aclRoleID", $this->aclRoleID)
						->where("aclModel.aclResourceID", $resource->aclResourceID)
						->where("aclModel.aclActionID", $action->aclActionID)
						->where("allowed", TRUE);

					if ($permissions->count() > 0)
						$checkbox->setValue(TRUE);
				}
			}
		}
		
		if ($this->aclRoleID) {
			$roleEntity = $this->roleRepository->get($this->aclRoleID);
			if ($roleEntity) {
				$form['aclRoleID']->setValue($roleEntity->aclRoleID);
				$form['name']->setValue($roleEntity->name);
			}
		}
		
		$form->onSuccess[] = callback($this, "Submit");
		$form->onError[] = callback($this, "Error");
		
		return $form;
	}

	/** Error Submit
	 * @param Form $form
	 */
	public function Error(Form $form) {
		$json = new \stdClass();
		$json->result = "error";
		$json->message = implode("<br />", $form->getErrors());
		$json->notify = implode(",", $form->getErrors());
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}
	
	/** Submit
	 * 
	 * @param \Nette\Application\UI\Form $form
	 */
	public function Submit(Form $form) {
		$json = new \stdClass();
		$json->result = "success";
		$values = $form->getValues();

		if (!empty($values['aclRoleID'])) {
			if ($this->user->isAllowed("permission", "edit")) {
				$result = $this->permissionManager->update($values);
			} else {
				$result = RoleForm::PERMISSION;
			}
		} else {
			if ($this->user->isAllowed("permission", "add")) {
				$result = $this->permissionManager->insert($values);
			} else {
				$result = RoleForm::PERMISSION;
			}
		}
		/*Debugger::dump($values);
		exit();*/

		if ($result === TRUE) {
			$json->result = "success";
		} else {
			$json->result = "error";
			$json->message = $result;
		}
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}
}
