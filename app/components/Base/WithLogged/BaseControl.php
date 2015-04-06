<?php
/**
 * User: Martin
 * Date: 2.11.13
 * Time: 18:43
 */

namespace Component\Base\WithLogged;


use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Services\iLogger;
use Services\Logger\iObservable;
use Services\Logger\iObserver;
use Nextras\Application\UI\SecuredLinksControlTrait;
use Nette\Localization\ITranslator;

/**
 * Class BaseControl
 * @package Component\Base\WithLogged
 */
abstract class BaseControl extends Control implements iObservable {

	use SecuredLinksControlTrait;

	/** @var  array */
	protected $observers;

	/** @var ITranslator */
	protected $translator;

	/**
	 * @param iLogger $observer
	 */
	public function attach(iLogger $observer) {
		$this->observers[] = $observer;
	}

	/**
	 * @param iLogger $observer
	 */
	public function detach(iLogger $observer) {
		$this->observers = array_diff($this->observers, array($observer));
	}

	/**
	 * @param $message
	 */
	public function notify($message) {
		foreach ($this->observers as $observer) {
			$observer->log($message);
		}
	}

	/**
	 * Odeslání formuláře s Errory
	 * @param Form $form
	 */
	public function FormError(Form $form) {
		$json = new \stdClass();
		$json->result = "error";
		$json->message = implode("<br />", $form->getErrors());
		$json->notify = implode(",", $form->getErrors());
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}
} 