<?php
/**
 * User: Martin
 * Date: 13.11.13
 * Time: 9:30
 */

namespace Model\History\Entity;


use Nette\DateTime;
use Nette\Diagnostics\Debugger;
use slimORM\Entity\Entity;

/**
 * Class SyslogEntity
 * @package Model\History\Entity
 * @table syslog
 */
class SyslogEntity extends Entity {

	/**
	 * @read
	 * @var int
	 */
	protected $syslogID;

	/**
	 * @read
	 * @var int
	 */
	protected $userID;

	/**
	 * @reference user
	 * @OneToOne(targetEntity="\Model\Permission\Entity\UserEntity", mappedBy="userID")
	 * @var \Model\Permission\Entity\UserEntity
	 */
	protected $user;

	/**
	 * @read
	 * @var string
	 */
	protected $message;

	/**
	 * @read
	 * @var DateTime
	 */
	protected $timestamp;

	/**
	 * @read
	 * @var string
	 */
	protected $ip;

	/**
	 * @var string
	 */
	protected $login;

	/**
	 * @param DateTime $timestamp
	 */
	public function setTimestamp(DateTime $timestamp)
	{
		$this->timestamp = $timestamp;
	}

	/**
	 * @return DateTime
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param int $userID
	 */
	public function setUserID($userID)
	{
		$this->userID = $userID;
	}

	/**
	 * @return int
	 */
	public function getUserID()
	{
		return $this->userID;
	}

	/**
	 * @return int
	 */
	public function getSyslogID()
	{
		return $this->syslogID;
	}

	/**
	 * @param string $ip
	 */
	public function setIp($ip)
	{
		$this->ip = $ip;
	}

	/**
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}

	/**
	 * @return \Model\Permission\Entity\UserEntity
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @return null|string
	 */
	public function getLogin() {
		$userEntity = $this->getUser();
		if ($userEntity)
			return $userEntity->getLogin();
		else {
			return NULL;
		}
	}

} 