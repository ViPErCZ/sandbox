<?php

namespace Model\Permission;

use Model\Permission\Entity\UserEntity;
use Nette\Utils\Paginator;
use slimORM\AbstractRepository;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

/**
 * Description of UserRepository
 *
 * @author Martin Chudoba
 */
class UserRepository extends AbstractRepository {

	/** constant */
	const ENTITY = '\Model\Permission\Entity\UserEntity';

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, UserRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return UserEntity|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return UserRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator)->select("*, role.name AS roleName");
	}

	/**
	 * @return UserEntity|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return UserRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return UserEntity|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(UserRepository::ENTITY)->fetch();
	}

	/**
	 * @param $userID
	 * @return bool|string
	 * @throws \slimORM\Exceptions\RepositoryException
	 */
	public function deactivate($userID) {
		try {
			$repository = $this->entityManager->getRepository(UserRepository::ENTITY);
			/** @var UserEntity $user */
			foreach ($repository->read()->where("userID", $userID) as $user) {
				if ($user->getUserID() != 1) {
					$user->setActive(false);
				}
			}
			$repository->save();
			return TRUE;
		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}
}
