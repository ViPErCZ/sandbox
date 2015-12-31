<?php
/**
 * User: Martin
 * Date: 13.11.13
 * Time: 17:26
 */

namespace Model\Authenticators;


use Illagrenan\Facebook\FacebookConnect;
use Model\Contact\Entity\ContactEntity;
use Model\Permission\Entity\UserEntity;
use Model\Permission\UserManager;
use Nette\DateTime;
use Nette\Diagnostics\Debugger;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Utils\Strings;

class FacebookAuthenticator implements IAuthenticator {

	/** @var UserManager */
	private $users;

	/** @var \Illagrenan\Facebook\FacebookConnect */
	private $facebookClient;

	/** @var bool */
	private $autoRegister;

	/** Construct
	 * @param \Model\Permission\UserManager $users
	 * @param FacebookConnect $facebookClient
	 * @param bool $autoRegister
	 */
	public function __construct(UserManager $users, FacebookConnect $facebookClient, $autoRegister = FALSE) {
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
		if (!$this->facebookClient->isLoggedIn()) {
			$this->facebookClient->login();
		} else {
			$email = $this->facebookClient->getFacebookUser()->getEmail();
			$user = $this->users->getUser($email);
			if (($user === NULL && $this->autoRegister === FALSE) ||
				($user instanceof UserEntity && $user->getActive() == 0)) {
				throw new AuthenticationException("User '$email' not found.", self::IDENTITY_NOT_FOUND);
			} else if ($user === NULL && $this->autoRegister === TRUE) {
				$result = $this->users->register(array(
					"login"			=> $email,
					"password"		=> Strings::random(),
					"name"			=> $this->facebookClient->getFacebookUser()->getFullName(),
					"firstname"		=> $this->facebookClient->getFacebookUser()->getFirstName(),
					"lastname"		=> $this->facebookClient->getFacebookUser()->getLastName(),
					"lastLogged"	=> new DateTime(),
					"ip"			=> $_SERVER['REMOTE_ADDR']
				));
				if ($result instanceof ContactEntity) {
					return new Identity($result->userID, $result->getUser()->role->name, $result->getUser()->toArray());
				} else {
					throw new AuthenticationException("User '$email' cannot be registered.", self::IDENTITY_NOT_FOUND);
				}
			} else if ($user instanceof UserEntity) {
				$user->setLastLogged(new DateTime());
				$user->setIp($_SERVER['REMOTE_ADDR']);
				$this->users->updateUser($user);
				$data = $user->toArray();
				unset($data['password']);
				return new Identity($user->userID, $user->role->name, $data);
			} else {
				throw new AuthenticationException("User '$email' cannot be connected.", self::IDENTITY_NOT_FOUND);
			}
		}
	}
} 