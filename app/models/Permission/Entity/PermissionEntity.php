<?php

namespace Model\Permission\Entity;
use slimORM\Entity\Entity;

/**
 * Description of PermissionEntity
 *
 * @author Martin Chudoba
 * @table aclPermission
 */
class PermissionEntity extends Entity {
	
	/**
	 * @column
	 * @var int
	 */
	protected $aclPermissionID;
	
	/**
	 * @column
	 * @var int
	 */
	protected $aclRoleID;

	
	/**
	 * @column
	 * @var int
	 */
	protected $aclModelID;
	
	/**
	 * @column
	 * @var boolean
	 */
	protected $allowed;

	/**
	 * @reference aclModel
	 * @OneToOne(targetEntity="\Model\Permission\Entity\ModelEntity", mappedBy="aclModelID")
	 * @var \Model\Permission\Entity\ModelEntity
	 */
	protected $aclModel;
	
	/**
	 * 
	 * @return boolean
	 */
	public function getAllowed() {
		return $this->allowed;
	}

	/**
	 * 
	 * @param boolean $allowed
	 * @return \Model\Permission\Entity\PermissionEntity
	 */
	public function setAllowed($allowed) {
		$this->allowed = $allowed;
		return $this;
	}

	/**
	 * @param int $aclModelID
	 * @return $this
	 */
	public function setAclModelID($aclModelID)
	{
		$this->aclModelID = $aclModelID;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAclModelID()
	{
		return $this->aclModelID;
	}

	/**
	 * @param int $aclRoleID
	 * @return $this
	 */
	public function setAclRoleID($aclRoleID)
	{
		$this->aclRoleID = $aclRoleID;
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
	 * @return mixed
	 */
	public function getAclModel()
	{
		return $this->aclModel;
	}

	/**
	 * @param int $aclPermissionID
	 */
	public function setAclPermissionID($aclPermissionID)
	{
		$this->aclPermissionID = $aclPermissionID;
	}

	/**
	 * @return int
	 */
	public function getAclPermissionID()
	{
		return $this->aclPermissionID;
	}
	
}
