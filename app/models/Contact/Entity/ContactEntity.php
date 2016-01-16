<?php
/**
 * User: Martin
 * Date: 4.12.13
 * Time: 12:48
 */

namespace Model\Contact\Entity;

use Model\Permission\Entity\UserEntity;
use slimORM\Entity\Entity;

/**
 * Description of ContactEntity
 *
 * @author Martin Chudoba
 * @table contact
 */
class ContactEntity extends Entity {

	/**
	 * @read
	 * @var int
	 */
	protected $contactID;

	/**
	 * @read
	 * @var int
	 */
	protected $userID;

	/**
	 * @read
	 * @var string
	 */
	protected $name;

	/**
	 * @read
	 * @var string
	 */
	protected $firstname;

	/**
	 * @read
	 * @var string
	 */
	protected $lastname;

	/**
	 * @reference user
	 * @OneToOne(targetEntity="\Model\Permission\Entity\UserEntity", mappedBy="userID")
	 * @var \Model\Permission\Entity\UserEntity
	 */
	protected $user;

	/**
	 * @param $contactID
	 * @return $this
	 */
	public function setContactID($contactID)
	{
		$this->contactID = $contactID;
		return $this;
	}

	/**
	 * @param string $firstname
	 * @return $this
	 */
	public function setFirstname($firstname)
	{
		$this->firstname = $firstname;
		if (!empty($this->firstname) && !empty($this->firstname)) {
			$this->setName($this->firstname . " " . $this->lastname);
		}
		return $this;
	}

	/**
	 * @param string $lastname
	 * @return $this
	 */
	public function setLastname($lastname)
	{
		$this->lastname = $lastname;
		if (!empty($this->firstname) && !empty($this->firstname)) {
			$this->setName($this->firstname . " " . $this->lastname);
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @param int $userID
	 * @return $this
	 */
	public function setUserID($userID)
	{
		$this->userID = $userID;
		return $this;
	}

	/**
	 * @param \Model\Permission\Entity\UserEntity $user
	 * @return $this
	 */
	public function setUser(UserEntity $user = null)
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getContactID()
	{
		return $this->contactID;
	}

	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return \Model\Permission\Entity\UserEntity
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return int
	 */
	public function getUserID()
	{
		return $this->userID;
	}

} 