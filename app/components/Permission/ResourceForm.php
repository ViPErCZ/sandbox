<?php

namespace Component\Permission;
use Model\Permission\ActionRepository;
use Model\Permission\ModelManager;
use Model\Permission\ModelRepository;
use Model\Permission\ResourceRepository;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Diagnostics\Debugger;
use Nette\Security\User;

/**
 * Description of ResourceForm
 * Sestavení modulů a jejich pravidel
 * @author Martin Chudoba
 */
class ResourceForm extends Control {
	
	/** @var \Model\Permission\ResourceRepository */
	private $resourceRepository;

	/** @var ActionRepository */
	private $actionRepository;

	/** @var ModelRepository */
	private $modelRepository;

	/** @var  ModelManager */
	private $modelManager;
	
	/** @var int */
	private $aclResourceID;

	/** @var User */
	private $user;

	/** messages */
	const PERMISSION = "Na tuto operaci nemáte dostatečná oprávnění";


	/**
	 * @param ModelManager $modelManager
	 * @param ResourceRepository $resourceRepository
	 * @param ActionRepository $actionRepository
	 * @param ModelRepository $modelRepository
	 * @param User $user
	 */
	public function __construct(ModelManager $modelManager, ResourceRepository $resourceRepository,
								ActionRepository $actionRepository, ModelRepository $modelRepository, User $user) {
		parent::__construct();

		$this->modelManager = $modelManager;
		$this->resourceRepository = $resourceRepository;
		$this->actionRepository = $actionRepository;
		$this->modelRepository = $modelRepository;
		$this->aclResourceID = NULL;
		$this->user = $user;
	}

	/**
	 * 
	 * @return int
	 */
	public function getAclResourceID() {
		return $this->aclResourceID;
	}

	/**
	 * 
	 * @param int $aclResourceID
	 * @return \Component\Permission\ResourceForm
	 */
	public function setAclResourceID($aclResourceID) {
		$this->aclResourceID = $aclResourceID;
		return $this;
	}
	
	/** Render
	 * 
	 */
	public function render() {
		$template = $this->template;
		$template->setFile(__DIR__ . "/latte/resourceForm.latte");

		$template->actions = $this->actionRepository->read();
		$template->render();
	}
	
	/** Vytvoření formuláře
	 * 
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm() {
		$form = new Form();
		$form->getElementPrototype()->id = "resourceForm";
		$form->addText("name", "Jméno modulu:")
				->setHtmlId("name")
				->setRequired("Prosím zadejte jméno modulu.");
		$form->addHidden("aclResourceID");
		$form->addButton("cancel", "Storno")->setHtmlId("cancel");
		$form->addSubmit("sender", "Uložit změny")->setHtmlId("sender");

		$actions = $this->actionRepository->read();
		$container = $form->addContainer("actions");
		foreach($actions as $key => $action) {
			$container->addCheckbox($key, $action->humanName);
		}

		if ($this->aclResourceID) {
			$modelEntities = $this->modelRepository->read()->where("aclResourceID", $this->aclResourceID);
			$modelEntity = $modelEntities->fetch();
			if ($modelEntity) {
				$form['aclResourceID']->setValue($modelEntity->getAclResource()->getAclResourceID());
				$form['name']->setValue($modelEntity->getAclResource()->name);
				foreach ($modelEntities as $entity) {
					$form["actions"][$entity->aclActionID]->setValue(TRUE);
				}
			} else {
				$resourceEntity = $this->resourceRepository->get($this->aclResourceID);
				if ($resourceEntity) {
					$form['aclResourceID']->setValue($this->aclResourceID);
					$form['name']->setValue($resourceEntity->getName());
				}
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
		if(array_search(TRUE, (array)$values["actions"]) === FALSE) {
			$json->result = "error";
			$json->message = "Prosím vyberte alespoň jednu akci pro modul.";
		} else {
			if (!empty($values['aclResourceID'])) {
				if ($this->user->isAllowed("permission", "edit")) {
					$result = $this->modelManager->update($values);
				} else {
					$result = ResourceForm::PERMISSION;
				}
			} else {
				if ($this->user->isAllowed("permission", "add")) {
					$result = $this->modelManager->insert($values);
				} else {
					$result = ResourceForm::PERMISSION;
				}
			}

			if ($result === TRUE) {
				$json->result = "success";
			} else {
				$json->result = "error";
				$json->message = $result;
			}
		}
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}
}
