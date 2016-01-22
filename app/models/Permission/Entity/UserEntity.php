<?php

namespace Model\Permission\Entity;
use Nette\Utils\DateTime;
use slimORM\Entity\Entity;

/**
 * Description of UserEntity
 *
 * @author Martin Chudoba
 * @table user
 */
class UserEntity extends Entity {
	
	/**
	 * @column
	 * @var int
	 */
	protected $userID;
	
	/**
	 * @column
	 * @var string
	 */
	protected $login;
	
	/**
	 * @column
	 * @var string
	 */
	protected $password;
	
	/**
	 * @column
	 * @var boolean
	 */
	protected $active;
	
	/**
	 * @column
	 * @var \Nette\DateTime
	 */
	protected $lastLogged;
	
	/**
	 * @column
	 * @var string
	 */
	protected $ip;
	
	/** 
	 * @column
	 * @var int
	 */
	protected $aclRoleID;
	
	/**
	 * @reference aclRole
	 * @OneToOne(targetEntity="\Model\Permission\Entity\RoleEntity", mappedBy="aclRoleID")
	 * @var \Model\Permission\Entity\RoleEntity
	 */
	protected $role;

	/**
	 * @reference contact
	 * @OneToOne(targetEntity="\Model\Contact\Entity\ContactEntity", mappedBy="userID")
	 * @var \Model\Contact\Entity\ContactEntity
	 */
	protected $contact;

	/**
	 * @var string
	 */
	protected $roleName;

	/**
	 * @return string
	 */
	public function getRoleName()
	{
		return $this->role->getName();
	}

	/**
	 * @param string $roleName
	 */
	public function setRoleName($roleName)
	{
		$this->roleName = $roleName;
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
	 * @param int $userID
	 */
	public function setUserID($userID) {
		$this->userID = $userID;
	}

	/**
	 * 
	 * @param type $login
	 * @return \Model\Permission\Entity\UserEntity
	 */
	public function setLogin($login) {
		$this->login = $login;
		return $this;
	}

	/**
	 * @param string $password
	 * @return $this
	 */
	public function setPassword($password) {
		$this->password = $this->calculateHash($password);
		return $this;
	}

	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public function calculateHash($password) {
		return md5($password . str_repeat('dadaddada4454DSDAd45a4d54da5d', 10));
	}

	/**
	 * 
	 * @param boolean $active
	 * @return \Model\Permission\Entity\UserEntity
	 */
	public function setActive($active) {
		$this->active = $active;
		return $this;
	}

	/**
	 * @param DateTime $lastLogged
	 * @return $this
	 */
	public function setLastLogged(DateTime $lastLogged) {
		$this->lastLogged = $lastLogged;
		return $this;
	}

	/**
	 * 
	 * @param type $ip
	 * @return \Model\Permission\Entity\UserEntity
	 */
	public function setIp($ip) {
		$this->ip = $ip;
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
	 * @return boolean
	 */
	public function getActive()
	{
		return $this->active;
	}

	/**
	 * @return \Model\Contact\Entity\ContactEntity
	 */
	public function getContact()
	{
		return $this->contact;
	}

	/**
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}

	/**
	 * @return \Nette\DateTime
	 */
	public function getLastLogged()
	{
		return $this->lastLogged;
	}

	/**
	 * @return string
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @return \Model\Permission\Entity\RoleEntity
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @return int
	 */
	public function getUserID()
	{
		return $this->userID;
	}



}
