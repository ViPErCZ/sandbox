<?php

namespace App;
use Component\Notification\NotificationWidget;
use Model\Permission\RoleRepository;
use Model\Permission\UserRepository;
use Nette\Forms\Container;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Nextras\Datagrid\MyDatagrid;

/**
 * Description of UserPresenter
 *
 * @author Martin Chudoba
 */
class UserPresenter extends BasePresenter {

	use SecuredLinksPresenterTrait;
	
	/** @var \Model\Permission\UserRepository */
	protected $userRepository;

	/** @var \Model\Permission\RoleRepository */
	protected $roleRepository;

	/** @var int */
	protected $state = 0;

	/** Inject
	 * @param \Model\Permission\RoleRepository $roleRepository
	 */
	public function injectRoleRepository(RoleRepository $roleRepository) {
		$this->roleRepository = $roleRepository;
	}
	
	/** Injekt
	 * 
	 * @param \Model\Permission\UserRepository $userRepository
	 */
	public function injectUserRepository(UserRepository $userRepository) {
		$this->userRepository = $userRepository;
	}

	/**
	 * @param $message
	 */
	public function handleRefresh($message) {
		if ($this->isAjax()) {
			$this->redrawControl("wrapper");
			$this->redrawControl("content");
			if ($message) {
				$this['notification']->addSuccess($message);
				$this['notification']->redrawControl('success');
			}
		}
	}

	/**
	 * @param $userID
	 */
	public function handleLoadForm($userID) {
		$this['userForm']->setUserID($userID);
		$this->state = 1;
		if ($this->isAjax()) {
			$this->redrawControl("wrapper");
			$this->redrawControl("content");
		}
	}

	/**
	 * @secured
	 * @param $usersID
	 */
	public function handleDelete($usersID) {
		if ($this->isAjax()) {
			$this['notification']->addError("Tato funkce není momentálně implementována.");
			$this['notification']->redrawControl("error");
		}
	}

	/**
	 * @param $data
	 * @throws \Nette\Application\AbortException
	 */
	public function handleGenerateRemoveUrl($data) {
		echo $this->link('delete!', array('usersID' => $data));
		$this->terminate();
	}

	/**
	 * @throws \Nette\Application\AbortException
	 */
	public function handleRemove() {
		$this->terminate();
	}

	/**
	 *
	 */
	public function renderDefault() {
		$this->template->state = $this->state;
	}

	/**
	 * @return NotificationWidget
	 */
	protected function createComponentNotification() {
		return new NotificationWidget();
	}

	/** Vytvoří komponentu gridu
	 * 
	 */
	protected function createComponentGrid() {
		$grid = new MyDatagrid();
		$grid->setTranslator($this->translator);
		$grid->setShowCheckboxes(TRUE);
		$grid->addColumn('login', 'Uživatelské jméno')->enableSort();
		$grid->addColumn('roleName', 'Skupina')->enableSort();
		$grid->addColumn('active', 'Aktivní')->enableSort();
		$grid->addColumn('lastLogged', 'Poslední přihlášení')->enableSort();
		$grid->addColumn('ip', 'IP adresa')->enableSort();
		$grid->addCellsTemplate(APP_DIR . "/templates/" . $this->getName() . "/row-actions.latte");
		$grid->setRowPrimaryKey('userID');

		$model = $this->userRepository;
		$user = $this->user;

		$grid->setDatasourceCallback(function($filter, $order, $paginator) use ($model, $user) {
			if ($user->isInRole("root"))
				$data = $model->read($paginator);
			else
				$data = $model->read($paginator)->where("login != ?", "root")->where("userID = ? OR user.aclRoleID != ?", array($user->getId(), $user->getIdentity()->data['aclRoleID']));

			foreach ($filter as $k => $v) {
				$k = str_replace("roleName", "role.name", $k);
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			if ($order) {
				$order = str_replace("roleName", "role.name", implode(" ", $order));
				$data->order($order);
			}
			// Role name hacking
			foreach ($data as $entity) {
				$entity->setRoleName($entity->getRole()->getName());
			}
			return $data;
		});
		$grid->setPagination(10, function($filter) use ($model) {
			$data = $model->read();
			foreach ($filter as $k => $v) {
				$k = str_replace("roleName", "role.name", $k);
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			return $data->count('*');
		});
		$roleModel = $this->roleRepository;
		$grid->setFilterFormFactory(function() use ($roleModel) {
			$form = new Container();
			$form->addText("login", "Uživatelské jméno");
			$form->addSelect("roleName", "Skupina", $roleModel->read()->fetchPairs("name", "name"))
				->setPrompt("Vše");
			$form->addSelect("active", "Aktivní", array("0" => "Neaktivní", "1" => "Aktivní"))
				->setPrompt("Vše");
			$form->addText("lastLogged", "Poslední přihlášení");
			$form->addText("ip", "IP adresa");

			// these buttons are not compulsory
			$form->addSubmit('filter', 'Filtrovat')->getControlPrototype()->class = 'btn btn-primary';
			$form->addSubmit('cancel', 'Storno')->getControlPrototype()->class = 'btn';

			return $form;
		});

		return $grid;
	}
	
	/**
	 * 
	 * @return \Component\UserWidget\UserForm
	 */
	protected function createComponentUserForm() {
		$form = $this->componentFactory->create('\Component\UserWidget\UserForm');
		return $form;
	}
}

?>
