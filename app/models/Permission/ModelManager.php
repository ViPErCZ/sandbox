<?php
/**
 * User: Martin Chudoba
 * Date: 1.11.13
 * Time: 9:29
 */

namespace Model\Permission;

use Model\Base\BaseModel;
use Model\Permission\Entity\ModelEntity;
use Model\Permission\Entity\ResourceEntity;
use Nette\Database\Context;
use Nette\Diagnostics\Debugger;

class ModelManager extends BaseModel {

	/** @var  ResourceRepository */
	private $resourceRepository;

	/** @var ActionRepository */
	private $actionRepository;

	/** @var  ModelRepository */
	private $modelRepository;

	/** Konstruktor
	 * @param Context $connection
	 * @param ModelRepository $modelRepository
	 * @param ResourceRepository $resourceRepository
	 * @param ActionRepository $actionRepository
	 */
	public function __construct(Context $connection, ModelRepository $modelRepository,
								ResourceRepository $resourceRepository, ActionRepository $actionRepository) {
		parent::__construct($connection);

		$this->modelRepository = $modelRepository;
		$this->resourceRepository = $resourceRepository;
		$this->actionRepository = $actionRepository;
	}

	/**
	 * @param array $data
	 * @return bool|string
	 */
	public function insert($data) {
		try {
			$this->database->beginTransaction();
			$resourceEntity = new ResourceEntity();
			$resourceEntity->setName($data['name']);
			$repo = $this->resourceRepository->push($resourceEntity);
			$repo->save();
			$ent = $this->resourceRepository->get($repo->getLastInsertID());
			$actions = $this->actionRepository->read();
			foreach($actions as $key => $action) {
				if (isset($data["actions"][$key]) && $data["actions"][$key] === TRUE) {
					$modelEntity = new ModelEntity();
					$modelEntity->setAclResourceID($ent->getAclResourceID());
					$modelEntity->setAclActionID($action->getAclActionID());
					$this->modelRepository->push($modelEntity);
				}
			}
			$this->modelRepository->save(FALSE);
			$this->database->commit();
			return TRUE;
		} catch (\PDOException $e) {
			$this->database->rollBack();
			return $e->getMessage();
		}
	}

	/**
	 * @param array $values
	 * @return bool|string
	 */
	public function update($values) {
		try {
			$this->database->beginTransaction();
			$resourceEntity = $this->resourceRepository->get($values['aclResourceID']);
			if ($resourceEntity) {
				$resourceEntity->setName($values['name']);
				$this->resourceRepository->save(FALSE);
				$modelEntities = $this->modelRepository->read()->where("aclResourceID", $resourceEntity->getAclResourceID());
				$modelEntities->fetchAll();
				$actions = $this->actionRepository->read();
				foreach($actions as $key => $action) {
					if (isset($values["actions"][$key]) && $values["actions"][$key]) {
						$hasAction = FALSE;
						foreach ($modelEntities as $entity) {
							if ($entity->aclActionID == $action->aclActionID) {
								$hasAction = TRUE;
								break;
							}
						}
						if ($hasAction === FALSE) {
							$modelEntity = new ModelEntity();
							$modelEntity->setAclResourceID($values['aclResourceID']);
							$modelEntity->setAclActionID($action->aclActionID);
							$this->modelRepository->push($modelEntity);
						}
					} elseif (isset($values["actions"][$key]) && $values["actions"][$key] === FALSE) {
						$this->modelRepository->deleteByActionID($values['aclResourceID'], $action->getAclActionID());
					}
				}
				$this->modelRepository->save();
			} else {
				throw new \PDOException("Item with ID " . $values['aclResourceID'] . " not found");
			}
			$this->database->commit();
			return TRUE;
		} catch (\PDOException $e) {
			$this->database->rollBack();
			return $e->getMessage();
		}
	}

	/** Remove
	 * @param array $keys
	 * @return bool|string
	 */
	public function remove($keys) {
		try {
			$this->database->beginTransaction();
			$this->modelRepository->read()->where("aclResourceID", $keys)->getSelection()->delete();
			$this->resourceRepository->read()->where("aclResourceID", $keys)->getSelection()->delete();
			$this->database->commit();
			return TRUE;
		} catch (\PDOException $e) {
			$this->database->rollBack();
			return $e->getMessage();
		}
	}
} 