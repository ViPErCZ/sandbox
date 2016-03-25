<?php

namespace Services;

use Nette\Database\Context;
use Nette\Utils\DateTime;
use Nette\Security\User;

/**
 * Description of SQLLogger
 *
 * @author Martin Chudoba
 */
class SQLLogger implements iLogger {

	/** @var Context */
	private $database;
	
	/** @var User */
	private $user;

	/** Konstanty
	 * 
	 */
	const DB_TABLE = "syslog";


	/** Konstruktor
	 * @param Context $database
	 * @param User $user
	 */
	public function __construct(Context $database, User $user) {
		$this->database = $database;
		$this->user = $user;
	}

	/** Funkce pro zápis zprávy
	 * 
	 * @param string $message
	 */
	public function log($message) {
		if (is_string($message) && !empty($message)) {
			$record = array(
				'timestamp' => new DateTime(),
				'message' => $message,
				'ip' => $_SERVER["REMOTE_ADDR"],
			);
			if ($this->user && $this->user->isLoggedIn()) {
				$record['userID'] = $this->user->getIdentity()->userID;
			}

			$this->database->table(SQLLogger::DB_TABLE)->insert($record);
		}
	}

}

?>
