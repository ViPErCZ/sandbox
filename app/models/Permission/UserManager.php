<?php
/**
 * User: Martin
 * Date: 4.12.13
 * Time: 14:54
 */

namespace Model\Permission;


use Model\Contact\ContactRepository;
use Model\Contact\Entity\ContactEntity;
use Model\Permission\Entity\UserEntity;
use Model\Registrators\iRegistrator;
use Nette\Object;

class UserManager extends Object {

	/** @var iRegistrator */
	private $registrator;

	/** @var \Model\Contact\ContactRepository */
	private $contactRepository;

	/**
	 * @param ContactRepository $contactRepository
	 * @param iRegistrator $registrator
	 */
	public function __construct(ContactRepository $contactRepository, iRegistrator $registrator) {
		$this->registrator = $registrator;
		$this->contactRepository = $contactRepository;
	}

	/** Regisration
	 * @param array $data
	 * @return bool|ContactEntity|\PDOException
	 */
	public function register(array $data) {
		try {
			return $this->registrator->register($data);
		} catch (\PDOException $e) {
			return $e;
		}
	}

	/**
	 * @param $user
	 * @return UserEntity|NULL
	 */
	public function getUser($user) {
		$contactEntity = NULL;
		if (is_string($user)) {
			$contactEntity = $this->contactRepository->read()->where("user.login LIKE ?", $user)->fetch();
		} else if (is_int($user)) {
			$contactEntity = $this->contactRepository->read()->where("userID", $user)->fetch();
		}
		return $contactEntity ? $contactEntity->getUser() : NULL;
	}

	/**
	 * @param UserEntity $user
	 * @return ContactEntity|boolean
	 */
	public function updateUser(UserEntity $user) {
		$contactEntity = $user->getContact();
		if ($contactEntity instanceof ContactEntity) {
			$contactEntity->setUser($user); // set modified user entity
			return $this->contactRepository->save(TRUE, $contactEntity);
		} else
			return FALSE;
	}
} 