<?php
/**
 * Created by PhpStorm.
 * User: Martin
 * Date: 31.10.13
 * Time: 14:51
 */

namespace Model\Permission\Entity;

use slimORM\Entity\Entity;

/**
 * Class ModelEntity
 * @package Model\Permission\Entity
 * @table aclModel
 */
class ModelEntity extends Entity {

	/**
	 * @column
	 * @var int
	 */
	protected $aclModelID;

	/**
	 * @column
	 * @var int
	 */
	protected $aclResourceID;

	/**
	 * @column
	 * @var int
	 */
	protected $aclActionID;

	/**
	 * @reference aclResource
	 * @OneToOne(targetEntity="\Model\Permission\Entity\ResourceEntity", mappedBy="aclResourceID")
	 * @var \Model\Permission\Entity\ResourceEntity
	 */
	protected $aclResource;

	/**
	 * @reference aclAction
	 * @OneToOne(targetEntity="\Model\Permission\Entity\ActionEntity", mappedBy="aclActionID")
	 * @var \Model\Permission\Entity\ActionEntity
	 */
	protected $aclAction;

	/**
	 * @param int $aclActionID
	 */
	public function setAclActionID($aclActionID)
	{
		$this->aclActionID = $aclActionID;
	}

	/**
	 * @param int $aclResourceID
	 */
	public function setAclResourceID($aclResourceID)
	{
		$this->aclResourceID = $aclResourceID;
	}

	/**
	 * @param int $aclModelID
	 */
	public function setAclModelID($aclModelID) {
		$this->aclModelID = $aclModelID;
	}

	/**
	 * @return \Model\Permission\Entity\ResourceEntity
	 */
	public function getAclResource()
	{
		return $this->aclResource;
	}

	/**
	 * @return \Model\Permission\Entity\ActionEntity
	 */
	public function getAclAction()
	{
		return $this->aclAction;
	}

	/**
	 * @return int
	 */
	public function getAclActionID()
	{
		return $this->aclActionID;
	}

	/**
	 * @return int
	 */
	public function getAclModelID()
	{
		return $this->aclModelID;
	}

	/**
	 * @return int
	 */
	public function getAclResourceID()
	{
		return $this->aclResourceID;
	}

} 