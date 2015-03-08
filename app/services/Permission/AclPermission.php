<?php
/**
 * User: Martin
 * Date: 12.11.13
 * Time: 13:40
 */

namespace Services\Permission;


use Model\Permission\PermissionRepository;
use Model\Permission\ResourceRepository;
use Model\Permission\RoleRepository;
use Nette\InvalidStateException;
use Nette\Security\IAuthorizator;
use Nette\Security\Permission;

class AclPermission implements IAuthorizator {

	/** @var RoleRepository */
	private $roleRepository;

	/** @var ResourceRepository */
	private $resourceRepository;

	/** @var PermissionRepository */
	private $permissionRepository;

	/** @var \Nette\Security\Permission */
	private $acl;

	/** @var int */
	private $aclRoleID;

	/** @var boolean */
	private $isInitialized;

	/** Konstruktor
	 * @param RoleRepository $roleRepository
	 * @param ResourceRepository $resourceRepository
	 * @param PermissionRepository $permissionRepository
	 */
	public function __construct(RoleRepository $roleRepository, ResourceRepository $resourceRepository,
								  PermissionRepository $permissionRepository) {
		$this->roleRepository = $roleRepository;
		$this->resourceRepository = $resourceRepository;
		$this->permissionRepository = $permissionRepository;
		$this->acl = new Permission();
		$this->aclRoleID = NULL;
		$this->isInitialized = FALSE;
	}

	/**
	 * @param $roleID
	 */
	public function setRoleID($roleID) {
		$this->aclRoleID = $roleID;
	}

	/**
	 *
	 */
	private function initRole() {
		foreach ($this->roleRepository->read()->order("aclRoleID ASC") as $item) {
			$this->acl->addRole($item->name);
		}
	}

	/**
	 *
	 */
	private function InitResource() {
		foreach ($this->resourceRepository->read()->order("aclResourceID ASC") as $resource) {
			$this->acl->addResource($resource->name);
		}
	}

	/**
	 *
	 */
	private function InitPermission($role) {
		$allowPerm = $this->permissionRepository->read()
			->where("aclRoleID", $this->aclRoleID)
			->where("allowed", TRUE);
		foreach ($allowPerm as $permission) {
			$this->acl->allow($role, $permission->getAclModel()->getAclResource()->name, $permission->getAclModel()->getAclAction()->name);
		}
	}

	/**
	 * Init
	 */
	protected function Init($role) {
		if ($this->isInitialized === FALSE) {
			if ($this->aclRoleID) {
				$this->InitRole();
				$this->InitResource();
				$this->InitPermission($role);

				$this->acl->allow('root');
				$this->isInitialized = TRUE;
			} else {
				throw new InvalidStateException("Please set first aclRoleID variable.");
			}
		}
	}

	/**
	 * @param null $role
	 * @param null $resource
	 * @param null $privilege
	 * @return bool|null
	 */
	public function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL) {
		if ($role == "root")
			return TRUE;
		try {
			$this->Init($role);
			return $this->acl->isAllowed($role, $resource, $privilege);
		} catch (InvalidStateException $e) {
			return FALSE;
		}
	}
} 