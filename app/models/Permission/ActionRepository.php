<?php

namespace Model\Permission;
use Model\Base\BaseRepository;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

/**
 * Description of ActionRepository
 *
 * @author Martin Chudoba
 */
class ActionRepository extends BaseRepository {
	
	/** konstanty */
	const ENTITY = '\Model\Permission\Entity\ActionEntity';

	/** Konstruktor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, ActionRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return ActionEntity|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return ActionRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return ActionEntity|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return ActionRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return ActionEntity|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(ActionRepository::ENTITY)->fetch();
	}

	/**
	 * @param $aclActionIDs
	 * @return bool|string
	 */
	public function delete($aclActionIDs) {
		try {
			$this->entityManager->getRepository(ActionRepository::ENTITY)->delete($aclActionIDs);
			return TRUE;
		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}
}
