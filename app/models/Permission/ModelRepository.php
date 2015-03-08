<?php

namespace Model\Permission;

use Model\Base\BaseRepository;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

/**
 * Class ModelRepository
 * @package Model\Permission
 * @author Martin Chudoba
 */
class ModelRepository extends BaseRepository {

	/** konstanty */
	const ENTITY = '\Model\Permission\Entity\ModelEntity';

	/** Konstruktor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, ModelRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return ModelEntity|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return ModelRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return ModelEntity|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return ModelRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return ModelEntity|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(ModelRepository::ENTITY)->fetch();
	}

	/**
	 * @param $key
	 * @param $actionID
	 */
	public function deleteByActionID($key, $actionID) {
		$this->entityManager->getRepository(ModelRepository::ENTITY)
			->read()
			->where("aclResourceID", $key)
			->where("aclActionID", $actionID)
			->getSelection()
			->delete();
	}
} 