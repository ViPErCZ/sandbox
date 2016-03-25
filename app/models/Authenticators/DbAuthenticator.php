<?php

namespace Model\Authenticators;

use Model\Permission\UserRepository;
use Nette\Utils\DateTime;
use Nette\Object;
use Nette\Security as NS;


/**
 * Users authenticator.
 *
 * @author     Martin Chudoba
 */
class DbAuthenticator extends Object implements NS\IAuthenticator
{
	/** @var UserRepository */
	private $users;

	/** Konstruktor
	 * @param UserRepository $users
	 */
	public function __construct(UserRepository $users)
	{
		$this->users = $users;
	}

	/**
	 * Performs an authentication
	 * @param array $credentials
	 * @return NS\Identity
	 * @throws NS\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$userSel = $this->users->read();
		$userSel->where('login', $username)->where("active", TRUE);
		$user = $userSel->fetch();

		if (!$user) {
			throw new NS\AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
		}

		if ($user->getPassword() !== $user->calculateHash($password)) {
			throw new NS\AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
		}
		
		$user->setLastLogged(new DateTime());
		$user->setIp($_SERVER['REMOTE_ADDR']);
		$this->users->save();
		$data = $user->toArray();
		unset($data['password']);

		return new NS\Identity($user->getUserID(), $user->getRole()->getName(), $data);
	}

}
