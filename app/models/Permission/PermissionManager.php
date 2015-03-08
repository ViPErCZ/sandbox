<?php
/**
 * User: Martin
 * Date: 1.11.13
 * Time: 10:10
 */

namespace Model\Permission;

use Model\Base\BaseModel;
use Model\Permission\Entity\PermissionEntity;
use Model\Permission\Entity\RoleEntity;
use Nette\Database\Context;
use Nette\Diagnostics\Debugger;

class PermissionManager extends BaseModel {

	private $roleRepository;
	private $permissionRepository;
	private $resourceRepository;

	/** Konstruktor
	 * @param Context $connection
	 * @param RoleRepository $roleRepository
	 * @param PermissionRepository $permissionRepository
	 * @param ResourceRepository $resourceRepository
	 */
	public function __construct(Context $connection,
								RoleRepository $roleRepository, PermissionRepository $permissionRepository,
								ResourceRepository $resourceRepository) {
		parent::__construct($connection);

		$this->roleRepository = $roleRepository;
		$this->permissionRepository = $permissionRepository;
		$this->resourceRepository = $resourceRepository;
	}

	/** Insert
	 * @param array $values
	 * @return bool|string
	 */
	public function insert($values) {
		try {
			$this->database->beginTransaction();
			$roleEntity = new RoleEntity();
			$roleEntity->setName($values['name']);
			$repo = $this->roleRepository->push($roleEntity);
			$repo->save();
			$aclRoleID = $repo->getLastInsertID();
			$resources = $this->resourceRepository->read();

			foreach($resources as $resource) {
				foreach ($values[$resource->aclResourceID] as $key => $action) {
					if ($action === TRUE) {
						$permissionEntity = new PermissionEntity();
						$permissionEntity->setAclRoleID($aclRoleID);
						$permissionEntity->setAclModelID($key);
						$permissionEntity->setAllowed($action);
						$this->permissionRepository->push($permissionEntity);
					}
				}
			}
			$this->permissionRepository->save();
			$this->database->commit();
			return TRUE;
		} catch (\PDOException $e) {
			$this->database->rollBack();
			return $e->getMessage();
		}
	}

	/** Update
	 * @param array $values
	 * @return bool|string
	 */
	public function update($values) {
		try {
			$this->database->beginTransaction();
			$roleEntity = $this->roleRepository->get($values['aclRoleID']);
			if ($roleEntity) {
				$roleEntity->setName($values['name']);
				$this->roleRepository->push($roleEntity)->save();
				$resources = $this->resourceRepository->read();
				$this->permissionRepository->read()->where("aclRoleID", $values['aclRoleID'])->getSelection()->delete();
				Debugger::barDump($values);

				foreach($resources as $resource) {
					foreach ($values[$resource->aclResourceID] as $key => $action) {
						$permissionEntity = new PermissionEntity();
						$permissionEntity->setAclRoleID($values['aclRoleID']);
						$permissionEntity->setAclModelID($key);
						$permissionEntity->setAllowed($action);
						$this->permissionRepository->push($permissionEntity);
					}
				}

				$this->permissionRepository->save();

			} else {
				throw new \PDOException("Nepovedlo se načíst roli z databáze.");
			}
			$this->database->commit();
			return TRUE;
		} catch (\PDOException $e) {
			$this->database->rollBack();
			return $e->getMessage();
		}
	}

	/** Remove
	 * @param array $aclRoleID
	 * @return bool|string
	 */
	public function remove($aclRoleID) {
		try {
			$this->database->beginTransaction();
			$this->permissionRepository->read()->where("aclRoleID", $aclRoleID)->getSelection()->delete();
			$this->roleRepository->read()->where("aclRoleID", $aclRoleID)->getSelection()->delete();
			$this->database->commit();
			return TRUE;
		} catch (\PDOException $e) {
			$this->database->rollBack();
			return $e->getMessage();
		}
	}
} 