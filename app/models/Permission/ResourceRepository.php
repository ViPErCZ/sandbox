<?php

namespace Model\Permission;

use Model\Permission\Entity\ResourceEntity;
use Nette\Utils\Paginator;
use slimORM\AbstractRepository;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

/**
 * Description of ResourceRepository
 *
 * @author Martin Chudoba
 */
class ResourceRepository extends AbstractRepository {

	/** constant */
	const ENTITY = '\Model\Permission\Entity\ResourceEntity';

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, ResourceRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return ResourceEntity|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return ResourceRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return ResourceEntity|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return ResourceRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return ResourceRepository|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(ResourceRepository::ENTITY)->fetch();
	}

	/**
	 * @param $resourceID
	 * @return bool|string
	 */
	public function delete($resourceID) {
		try {
			$this->entityManager->getRepository(ResourceRepository::ENTITY)->delete($resourceID);
			return TRUE;
		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}
}
