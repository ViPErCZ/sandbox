<?php

namespace Model\Permission;

use Model\Permission\Entity\RoleEntity;
use Nette\Utils\Paginator;
use slimORM\AbstractRepository;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

/**
 * Description of RoleRepository
 *
 * @author Martin Chudoba
 */
class RoleRepository extends AbstractRepository {

	/** constant */
	const ENTITY = '\Model\Permission\Entity\RoleEntity';

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, RoleRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return RoleEntity|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return RoleRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return RoleEntity|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return RoleRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return RoleEntity|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(RoleRepository::ENTITY)->fetch();
	}

}
