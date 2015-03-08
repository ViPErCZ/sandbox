<?php

namespace Model\Permission;
use Model\Base\BaseRepository;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

/**
 * Description of PermissionRepository
 *
 * @author Martin Chudoba
 */
class PermissionRepository extends BaseRepository {
	
	/** konstanty */
	const ENTITY = '\Model\Permission\Entity\PermissionEntity';

	/** Konstruktor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, PermissionRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return PermissionEntity|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return PermissionRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return PermissionEntity|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return PermissionRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return PermissionEntity|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(PermissionRepository::ENTITY)->fetch();
	}
}
