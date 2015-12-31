<?php
/**
 * User: Martin
 * Date: 5.12.13
 * Time: 7:49
 */

namespace Model\Registrators;


use Model\Contact\ContactRepository;
use Model\Contact\Entity\ContactEntity;
use Model\Permission\Entity\UserEntity;

class Basic implements iRegistrator {

	/** @var \Model\Contact\ContactRepository */
	private $contactRepository;

	/** Construct
	 * @param ContactRepository $contactRepository
	 */
	public function __construct(ContactRepository $contactRepository) {
		$this->contactRepository = $contactRepository;
	}

	/** Registrer
	 * @param array $data
	 * @return mixed|ContactEntity
	 */
	public function register(array $data) {
		$contactEntity = new ContactEntity();
		$userEntity = new UserEntity();
		$contactEntity->setValues($data);
		$userEntity->setLogin($data['login']);
		$userEntity->setAclRoleID(10); // guest role
		$userEntity->setPassword($data['password']);
		$userEntity->setActive(TRUE);
		if (isset($data['lastLogged']))
			$userEntity->setLastLogged($data['lastLogged']);
		if (isset($data['ip']))
			$userEntity->setIp($data['ip']);
		$contactEntity->setUser($userEntity);

		$this->contactRepository->push($contactEntity);
		return $this->contactRepository->save();
	}
} 