<?php

namespace App;
use Model\Permission\ActionRepository;
use Model\Permission\ModelManager;
use Model\Permission\PermissionManager;
use Model\Permission\ResourceRepository;
use Model\Permission\RoleRepository;
use Nette\Application\Responses\JsonResponse;
use Nette\Forms\Container;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Nextras\Datagrid\MyDatagrid;

/**
 * Description of PermissionPresenter
 *
 * @author Martin Chudoba
 */
class PermissionPresenter extends BasePresenter {

	use SecuredLinksPresenterTrait;
	
	/** @var RoleRepository */
	private $roleRepository;
	
	/** @var ResourceRepository */
	private $resourceRepository;
	
	/** @var ActionRepository */
	private $actionRepository;

	/** @var PermissionManager  */
	private $permissionManager;

	/** @var ModelManager */
	private $modelManager;


	/** Injekt
	 * @param ModelManager $modelManager
	 */
	public function injectModelManager(ModelManager $modelManager) {
		$this->modelManager = $modelManager;
	}

	/** Injekt
	 * @param PermissionManager $permissionManager
	 */
	public function injectPermissionManager(PermissionManager $permissionManager) {
		$this->permissionManager = $permissionManager;
	}

	/** Injekt
	 * 
	 * @param RoleRepository $roleRepository
	 */
	public function injectRoleRepository(RoleRepository $roleRepository) {
		$this->roleRepository = $roleRepository;
	}
	
	/** Injekt
	 * 
	 * @param ResourceRepository $resourceRepository
	 */
	public function injectResourceRepository(ResourceRepository $resourceRepository) {
		$this->resourceRepository = $resourceRepository;
	}
	
	/** Injekt
	 * 
	 * @param ActionRepository $actionRepository
	 */
	public function injectActionRepository(ActionRepository $actionRepository) {
		$this->actionRepository = $actionRepository;
	}

	/** Signál pro načtení formuláře
	 * 
	 * @param int $roleID
	 */
	public function handleLoadRoleForm($roleID) {
		$this['roleForm']->setAclRoleID($roleID);
		$this['roleForm']->render();
		$this->terminate();
	}
	
	/** Signál pro načtení formuláře
	 * 
	 * @param int $resourceID
	 */
	public function handleLoadResourceForm($resourceID) {
		$this['resourceForm']->setAclResourceID($resourceID);
		$this['resourceForm']->render();
		$this->terminate();
	}

    /** Signál pro načtení formuláře
     * @param int $actionID
     */
    public function handleLoadActionForm($actionID) {
        $this['actionForm']->setAclActionID($actionID);
        $this['actionForm']->render();
        $this->terminate();
    }

	/** Signál pro refresh
	 * 
	 * @param string $templateName
	 */
	public function handleRefresh($templateName) {
		$template = $this->template;
		$template->setFile(APP_DIR . "/templates/" . $this->getName() . "/" . $templateName . ".latte");

		$template->render();
		$this->terminate();
	}

	/**
	 * @secured
	 * @param string $aclRoleID
	 */
	public function handleDeleteRole($aclRoleID) {
		$json = new \stdClass();
		$json->result = "error";

		if ($this->user->isAllowed("permission", "delete")) {
			if (is_string($aclRoleID)) {
				try {
					$aclRoleID = (array)Json::decode($aclRoleID);
					$aclRoleID = array_values($aclRoleID);
				} catch (JsonException $e) {
					$json->message = $e->getMessage();
					$json->result = "error";
					$response = new JsonResponse($json);
					$this->getPresenter()->sendResponse($response);
				}
			}
			$result = $this->permissionManager->remove($aclRoleID);
			if ($result === TRUE) {
				$json->result = "success";
			} else {
				$json->result = "error";
				if (strpos("Integrity constraint violation", $result) != -1) {
					$json->message = "Skupinu stále využívá někdo z uživatelů, proto ji nebylo možné vymazat.";
				} else {
					$json->message = $result;
				}
			}
		}
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}

	/**
	 * @secured
	 * @param string $aclModelID
	 */
	public function handleDeleteModel($aclModelID) {
		$json = new \stdClass();
		$json->result = "error";

		if ($this->user->isAllowed("permission", "delete")) {
			if (is_string($aclModelID)) {
				try {
					$aclModelID = (array)Json::decode($aclModelID);
					$aclModelID = array_values($aclModelID);
				} catch (JsonException $e) {
					$json->message = $e->getMessage();
					$json->result = "error";
					$response = new JsonResponse($json);
					$this->getPresenter()->sendResponse($response);
				}
			}
			$result = $this->modelManager->remove($aclModelID);
			if ($result === TRUE) {
				$json->result = "success";
			} else {
				$json->result = "error";
				if (strpos("Integrity constraint violation", $result) != -1) {
					$json->message = "Model stále využívá některá z komponent, proto ji nebylo možné vymazat.";
				} else {
					$json->message = $result;
				}
			}
		}
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}

	/**
	 * @secured
	 * @param $actionID
	 */
	public function handleDeleteAction($actionID) {
		$json = new \stdClass();
		$json->result = "error";

		if ($this->user->isAllowed("permission", "delete")) {
			if (is_string($actionID)) {
				try {
					$actionID = (array)Json::decode($actionID);
					$actionID = array_values($actionID);
				} catch (JsonException $e) {
					$json->message = $e->getMessage();
					$json->result = "error";
					$response = new JsonResponse($json);
					$this->getPresenter()->sendResponse($response);
				}
			}
			$result = $this->actionRepository->delete($actionID);
			if ($result === TRUE) {
				$json->result = "success";
			} else {
				$json->result = "error";
				if (strpos("Integrity constraint violation", $result) != -1) {
					$json->message = "Akci stále využívá některý z modelů, proto ji nebylo možné vymazat.";
				} else {
					$json->message = $result;
				}
			}
		}
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}

	/**
	 * @param $data
	 * @throws \Nette\Application\AbortException
	 */
	public function handleGenerateActionRemoveUrl($data) {
		echo $this->link('deleteAction!', array('actionID' => $data));
		$this->terminate();
	}

	/**
	 * @param $data
	 * @throws \Nette\Application\AbortException
	 */
	public function handleGenerateResourceRemoveUrl($data) {
		echo $this->link('deleteModel!', array('aclModelID' => $data));
		$this->terminate();
	}

	/**
	 * @param $data
	 * @throws \Nette\Application\AbortException
	 */
	public function handleGenerateRoleRemoveUrl($data) {
		echo $this->link('deleteRole!', array('aclRoleID' => $data));
		$this->terminate();
	}

	/** Vytvoří komponentu gridu
	 *  pro role
	 */
	protected function createComponentGrid() {
		$grid = new MyDatagrid();
		$grid->setTranslator($this->translator);
		$grid->setShowCheckboxes(TRUE);
		$grid->addColumn('name', 'Skupina')->enableSort();
		$grid->addCellsTemplate(APP_DIR . "/templates/" . $this->getName() . "/row-actions.latte");
		$grid->setRowPrimaryKey('aclRoleID');

		$model = $this->roleRepository;
		$user = $this->getUser();

		$grid->setDatasourceCallback(function($filter, $order, $paginator) use ($model, $user) {
			if ($user->isInRole("root")) {
				$data = $model->read($paginator)->where("name != ?", "root");
			} else {
				$data = $model->read($paginator)->where("name != ?", "root")->where("name NOT IN (?)", implode(",", $user->getRoles()));
			}

			foreach ($filter as $k => $v) {
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			if ($order) {
				$data->order(implode(" ", $order));
			}
			return $data;
		});
		$grid->setPagination(10, function($filter) use ($model) {
			$data = $model->read();
			foreach ($filter as $k => $v) {
				$k = str_replace("login", "user.login", $k);
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			return $data->count('*');
		});
		$grid->setFilterFormFactory(function() {
			$form = new Container();
			$form->addText("name", "Jméno skupiny");

			// these buttons are not compulsory
			$form->addSubmit('filter', 'Filtrovat')->getControlPrototype()->class = 'btn btn-primary';
			$form->addSubmit('cancel', 'Storno')->getControlPrototype()->class = 'btn';

			return $form;
		});

		return $grid;
	}
	
	/** Vytvoří komponentu gridu
	 *  pro zdroje
	 */
	protected function createComponentGridResource() {
		$grid = new MyDatagrid();
		$grid->setTranslator($this->translator);
		$grid->setShowCheckboxes(TRUE);
		$grid->addColumn('name', 'Modul')->enableSort();
		$grid->addCellsTemplate(APP_DIR . "/templates/" . $this->getName() . "/resource-row-actions.latte");
		$grid->setRowPrimaryKey('aclResourceID');

		$model = $this->resourceRepository;

		$grid->setDatasourceCallback(function($filter, $order, $paginator) use ($model) {
			$data = $model->read($paginator)->where("name != ?", "root");

			foreach ($filter as $k => $v) {
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			if ($order) {
				$data->order(implode(" ", $order));
			}
			return $data;
		});
		$grid->setPagination(10, function($filter) use ($model) {
			$data = $model->read();
			foreach ($filter as $k => $v) {
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			return $data->count('*');
		});
		$grid->setFilterFormFactory(function() {
			$form = new Container();
			$form->addText("name", "Jméno modulu");

			// these buttons are not compulsory
			$form->addSubmit('filter', 'Filtrovat')->getControlPrototype()->class = 'btn btn-primary';
			$form->addSubmit('cancel', 'Storno')->getControlPrototype()->class = 'btn';

			return $form;
		});

		return $grid;
	}
	
	/** Vytvoří komponentu gridu
	 * 
	 */
	protected function createComponentGridAction() {
		$grid = new MyDatagrid();
		$grid->setTranslator($this->translator);
		$grid->setShowCheckboxes(TRUE);
		$grid->addColumn('name', 'Jméno akce')->enableSort();
		$grid->addColumn('humanName', 'Lidský formát')->enableSort();
		$grid->addCellsTemplate(APP_DIR . "/templates/" . $this->getName() . "/action-row-actions.latte");
		$grid->setRowPrimaryKey('aclActionID');

		$model = $this->actionRepository;

		$grid->setDatasourceCallback(function($filter, $order, $paginator) use ($model) {
			$data = $model->read($paginator);

			foreach ($filter as $k => $v) {
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			if ($order) {
				$data->order(implode(" ", $order));
			}
			return $data;
		});
		$grid->setPagination(10, function($filter) use ($model) {
			$data = $model->read();
			foreach ($filter as $k => $v) {
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			return $data->count('*');
		});
		$grid->setFilterFormFactory(function() {
			$form = new Container();
			$form->addText("name", "Jméno akce");
			$form->addText("humanName", "Jméno akce - lidský formát");

			// these buttons are not compulsory
			$form->addSubmit('filter', 'Filtrovat')->getControlPrototype()->class = 'btn btn-primary';
			$form->addSubmit('cancel', 'Storno')->getControlPrototype()->class = 'btn';

			return $form;
		});

		return $grid;
	}

	/** Vytvoření komponenty formuláře
	 * 
	 * @return \Component\Permission\RoleForm
	 */
	protected function createComponentRoleForm() {
		$form = $this->componentFactory->create('\Component\Permission\RoleForm');
		return $form;
	}
	
	/** Vytvoření komponenty formuláře
	 * 
	 * @return \Component\Permission\RoleForm
	 */
	protected function createComponentResourceForm() {
		$form = $this->componentFactory->create('\Component\Permission\ResourceForm');
		return $form;
	}

    /** Vytvoření komponenty formuláře
     * @return \Component\Permission\ActionForm
     */
    protected function createComponentActionForm() {
        $form = $this->componentFactory->create('\Component\Permission\ActionForm');
        return $form;
    }

}

?>
