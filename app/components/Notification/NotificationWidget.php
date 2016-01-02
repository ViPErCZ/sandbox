<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 2.1.16
 * Time: 15:01
 */

namespace Component\Notification;

use Nette\Application\UI\Control;

/**
 * Class NotificationWidget
 * @package Component\Notification
 */
class NotificationWidget extends Control {

	/** @var array */
	protected $errors;

	/** @var array */
	protected $success;

	/**
	 * NotificationWidget constructor.
	 */
	public function __construct() {
		$this->success = array();
		$this->errors = array();
	}

	/**
	 * @param string $message
	 */
	public function addError($message) {
		$this->errors[] = $message;
	}

	/**
	 * @param $message
	 */
	public function addSuccess($message) {
		$this->success[] = $message;
	}

	/**
	 *
	 */
	public function render() {
		$template = $this->getTemplate();
		$template->setFile(__DIR__ . "/latte/notification.latte");

		$template->errors = $this->errors;
		$template->success = $this->success;

		$template->render();
	}
}