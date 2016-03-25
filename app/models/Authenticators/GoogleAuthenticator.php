<?php
/**
 * User: Martin
 * Date: 13.11.13
 * Time: 14:40
 */

namespace Model\Authenticators;

use Model\Contact\Entity\ContactEntity;
use Model\Permission\Entity\UserEntity;
use Model\Permission\UserManager;
use Nette\Utils\DateTime;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Utils\Strings;

class GoogleAuthenticator extends Object implements IAuthenticator {

	/** @var UserManager */
	private $users;

	/** @var \Google_Client */
	private $googleClient;

	/** @var bool  */
	private $autoRegister;

	/** Construct
	 * @param UserManager $users
	 * @param \Google_Client $googleClient
	 * @param bool $autoRegister
	 */
	public function __construct(UserManager $users, \Google_Client $googleClient, $autoRegister = FALSE) {
		$this->users = $users;
		$this->googleClient = $googleClient;
		$this->autoRegister = $autoRegister;
	}

	/**
	 * @param array $credentials
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($code) = $credentials;
		try {
			$this->googleClient->authenticate($code);
			$this->googleClient->setAccessToken($this->googleClient->getAccessToken());
			$oauth2 = new \Google_Oauth2Service($this->googleClient);
			$googleUser = $oauth2->userinfo->get();
			if (isset($googleUser['email'])) {
				$email = filter_var($googleUser['email'], FILTER_SANITIZE_EMAIL);
				$user = $this->users->getUser($email);
				if (($user === NULL && $this->autoRegister === FALSE) ||
					($user instanceof UserEntity && $user->getActive() == 0)) {
					throw new AuthenticationException("User '$email' not found.", self::IDENTITY_NOT_FOUND);
				} else if ($user === NULL && $this->autoRegister === TRUE) {
					$result = $this->users->register(array(
						"login"			=> $email,
						"password"		=> Strings::random(),
						"name"			=> isset($googleUser['name']) ? $googleUser['name'] : NULL,
						"firstname"		=> isset($googleUser['given_name']) ? $googleUser['given_name'] : NULL,
						"lastname"		=> isset($googleUser['family_name']) ? $googleUser['family_name'] : NULL,
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
			} else {
				throw new AuthenticationException("Uživatel nenalezen.");
			}
		} catch (\Google_AuthException $e) {
			throw new AuthenticationException($e->getMessage());
		} catch (\Google_ServiceException $e) {
			throw new AuthenticationException($e->getMessage());
		}
	}
} 