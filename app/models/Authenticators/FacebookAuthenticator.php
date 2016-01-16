<?php
/**
 * User: Martin
 * Date: 13.11.13
 * Time: 17:26
 */

namespace Model\Authenticators;

use Kdyby\Facebook\Facebook;
use Model\Contact\Entity\ContactEntity;
use Model\Permission\Entity\UserEntity;
use Model\Permission\UserManager;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Utils\DateTime;
use Nette\Utils\Random;
use Nette\Utils\Strings;
use Tracy\Debugger;

final class FacebookAuthenticator implements IAuthenticator {

	/** @var UserManager */
	private $users;

	/** @var Facebook */
	private $facebookClient;

	/** @var bool */
	private $autoRegister;

	/**
	 * FacebookAuthenticator constructor.
	 * @param UserManager $users
	 * @param Facebook $facebookClient
	 * @param bool $autoRegister
	 */
	public function __construct(UserManager $users, Facebook $facebookClient, $autoRegister = FALSE) {
		$this->users = $users;
		$this->facebookClient = $facebookClient;
		$this->autoRegister = $autoRegister;
	}

	/**
	 * @param array $credentials
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials) {
		$email = $credentials[0]['email'];
		$user = $this->users->getUser($email);
		if (($user === NULL && $this->autoRegister === FALSE) ||
			($user instanceof UserEntity && $user->getActive() == 0)) {
			throw new AuthenticationException("User '$email' not found.", self::IDENTITY_NOT_FOUND);
		} else if ($user === NULL && $this->autoRegister === TRUE) {
			$result = $this->users->register(array(
				"login"			=> $email,
				"password"		=> Random::generate(),
				"name"			=> $credentials[0]['firstName'] . " " . $credentials[0]['lastName'],
				"firstname"		=> $credentials[0]['firstName'],
				"lastname"		=> $credentials[0]['lastName'],
				"lastLogged"	=> new DateTime(),
				"ip"			=> $_SERVER['REMOTE_ADDR']
			));
			if ($result instanceof ContactEntity) {
				return new Identity($result->getUserID(), $result->getUser()->getRole()->getName(), $result->getUser()->toArray());
			} else {
				throw new AuthenticationException("User '$email' cannot be registered.", self::IDENTITY_NOT_FOUND);
			}
		} else if ($user instanceof UserEntity) {
			$user->setLastLogged(new DateTime());
			$user->setIp($_SERVER['REMOTE_ADDR']);
			$this->users->updateUser($user);
			$data = $user->toArray();
			unset($data['password']);
			return new Identity($user->getUserID(), $user->getRole()->getName(), $data);
		} else {
			throw new AuthenticationException("User '$email' cannot be connected.", self::IDENTITY_NOT_FOUND);
		}
	}
} 