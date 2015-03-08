<?php
/**
 * User: Martin
 * Date: 4.12.13
 * Time: 13:00
 */

namespace Model\Contact;


use Model\Base\BaseRepository;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

class ContactRepository extends BaseRepository {

	/** konstanty */
	const ENTITY = '\Model\Contact\Entity\ContactEntity';

	/** Konstruktor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, ContactRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return ContactEntity|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return ContactRepository
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
	 * @return ContactRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return ContactEntity|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(ContactRepository::ENTITY)->fetch();
	}
} 