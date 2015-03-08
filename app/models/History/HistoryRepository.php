<?php
/**
 * User: Martin
 * Date: 13.11.13
 * Time: 9:37
 */

namespace Model\History;


use Model\Base\BaseRepository;
use Nette\Utils\Paginator;
use slimORM\Entity\Entity;
use slimORM\EntityManager;

class HistoryRepository extends BaseRepository {

	/** konstanty */
	const ENTITY = '\Model\History\Entity\SyslogEntity';

	/** Konstruktor
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		parent::__construct($entityManager, HistoryRepository::ENTITY);
	}

	/**
	 * @param string $key
	 * @return HistoryRepository|NULL
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * @param Paginator $paginator
	 * @return HistoryRepository
	 */
	public function read(Paginator $paginator = NULL) {
		return parent::read($paginator);
	}

	/**
	 * @return SyslogEntity|TRUE
	 */
	public function save() {
		return parent::save();
	}

	/**
	 * @param Entity $entity
	 * @return HistoryRepository
	 */
	public function push(Entity $entity) {
		return parent::push($entity);
	}

	/**
	 * @return SyslogEntity|NULL
	 */
	public function fetch() {
		return $this->entityManager->getRepository(HistoryRepository::ENTITY)->fetch();
	}
} 