<?php
$container = require __DIR__ . '/bootstrap.php';

Tester\Environment::setup();

use Model\Contact\ContactRepository;
use Model\Permission\UserRepository;
use Tester\Assert;

/**
 * Created by PhpStorm.
 * User: viper
 * Date: 11.3.16
 * Time: 9:32
 */
class Users extends \Tester\TestCase {

	/** @var \Nette\DI\Container */
	private $container;

	/** @var UserRepository */
	private $userRepository;

	/** @var ContactRepository */
	private $contactRepository;

	/**
	 * Users constructor.
	 * @param \Nette\DI\Container $container
	 */
	function __construct(Nette\DI\Container $container)	{
		$this->container = $container;
	}

	public function setUp() {
		# Příprava
		$this->userRepository = $this->container->getByType('\Model\Permission\UserRepository');
		$this->contactRepository = $this->container->getByType('\Model\Contact\ContactRepository');
	}

	public function tearDown() {
		# Úklid
	}

	public function testRead() {
		$user = $this->userRepository->read()->wherePrimary(1)->fetch();
		Assert::same("root", $user->getLogin());
    }

	public function testInsertAndUpdate() {
		$user = new \Model\Permission\Entity\UserEntity();
		$user->setLogin("tester@domain.tld");
		$user->setPassword("tester.");
		$user->setAclRoleID(1);
		$user->setActive(true);

		$contact = new \Model\Contact\Entity\ContactEntity();
		$contact->setFirstname("Test");
		$contact->setLastname("Tester");
		$contact->setName("Test Tester");
		$contact->setUser($user);

		$this->contactRepository->clear();
		$this->contactRepository->push($contact)->save();

		$test = $this->userRepository->read()->where("login", "tester@domain.tld")->fetch();
		Assert::same("tester@domain.tld", $test->getLogin());
		Assert::same("Test Tester", $test->getContact()->getName());
		Assert::true(true, $test->getActive());

		$test->setActive(false);
		$this->userRepository->save();

		$test = $this->userRepository->read()->where("login", "tester@domain.tld")->fetch();
		Assert::false(false, $test->getActive());

		$test->toRow()->delete();
	}
}

$users = new Users($container);
$users->run();