<?php

namespace Model\Permission\Entity;
use slimORM\Entity\Entity;

/**
 * Description of RoleEntity
 *
 * @author Martin Chudoba
 * @table aclRole
 */
class RoleEntity extends Entity {

	/**
	 * @column
	 * @var string
	 */
	protected $name;
	
	/**
	 * @column
	 * @var int
	 */
	protected $aclRoleID;
	
	/** Pole s uživateli
	 * @reference user
	 * @OneToMany(targetEntity="\Model\Permission\Entity\UserEntity", mappedBy="aclRoleID")
	 * @var \Model\Permission\UserRepository
	 */
	protected $users;

	/**
	 * @param int $aclRoleID
	 */
	public function setAclRoleID($aclRoleID) {
		$this->aclRoleID = $aclRoleID;
	}

	/** Set name
	 * 
	 * @param string $name
	 * @return RoleEntity
	 */
	public function setName($name) {
		if (is_string($name)) {
			$this->name = $name;
		}
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAclRoleID()
	{
		return $this->aclRoleID;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return \Model\Permission\UserRepository
	 */
	public function getUsers()
	{
		return $this->users;
	}


	/**
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

}
