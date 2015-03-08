<?php

namespace Model\Permission\Entity;
use slimORM\Entity\Entity;

/**
 * Description of ResourceEntity
 *
 * @author Martin Chudoba
 * @table aclResource
 */
class ResourceEntity extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $aclResourceID;

	/**
	 * @column
	 * @var string
	 */
	protected $name;

	/**
	 * 
	 * @param srting $name
	 * @return \Model\Permission\Entity\ResourceEntity
	 */
	public function setName($name) {
		if (is_string($name)) {
			$this->name = $name;
		}
		return $this;
	}

	/**
	 * @param int $aclResourceID
	 */
	public function setAclResourceID($aclResourceID) {
		$this->aclResourceID = $aclResourceID;
	}

	/**
	 * 
	 * @return int
	 */
	public function getAclResourceID() {
		return $this->aclResourceID;
	}

	/**
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

}
