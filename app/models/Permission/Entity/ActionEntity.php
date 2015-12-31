<?php

namespace Model\Permission\Entity;
use slimORM\Entity\Entity;

/**
 * Description of ActionEntity
 *
 * @author Martin Chudoba
 * @table aclAction
 */
class ActionEntity extends Entity {
	
	/**
	 * @column
	 * @var int 
	 */
	protected $aclActionID;
	
	/**
	 * @column
	 * @var string 
	 */
	protected $name;
	
	/**
	 * @column
	 * @var string 
	 */
	protected $humanName;

	/**
	 * 
	 * @param string $name
	 * @return \Model\Permission\Entity\ActionEntity
	 */
	public function setName($name) {
		$this->name = (string)$name;
		return $this;
	}

    /**
     * @param string $humanName
     * @return $this
     */
    public function setHumanName($humanName) {
		$this->humanName = (string)$humanName;
		return $this;
	}

	/**
	 * @param int $aclActionID
	 */
	public function setAclActionID($aclActionID) {
		$this->aclActionID = $aclActionID;
	}
	
	/**
	 * 
	 * @return int
	 */
	public function getAclActionID() {
		return $this->aclActionID;
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
	public function getHumanName() {
		return $this->humanName;
	}

}
