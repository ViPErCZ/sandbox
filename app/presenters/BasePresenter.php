<?php

namespace App;

use Kdyby\Facebook\Facebook;
use Localization\TranslatorInjectTrait;
use Nette;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Services\iLogger;

/**
 * Class BasePresenter
 * @package App
 * @author Martin Chudoba <info@vipersoftware.net>
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

	use TranslatorInjectTrait;
	use SecuredLinksPresenterTrait;

	/** @var \iComponentFactory */
	protected $componentFactory;

	/** @var  \Services\iLogger */
	protected $logger;

	/** @var \Google_Client */
	protected $googleClient;

	/** @var Facebook */
	protected $facebookClient;

	/**
	 * @param Facebook $facebookClient
	 */
	public function injectFacebookClient(Facebook $facebookClient) {
		$this->facebookClient = $facebookClient;
	}

	/** Inject
	 * @param \Google_Client $googleClient
	 */
	public function injectGoogleClient(\Google_Client $googleClient) {
		$this->googleClient = $googleClient;
	}

	/** Injektování služby
	 * 
	 * @param \iComponentFactory $componentFactory
	 */
	public function injectComponentFactory(\iComponentFactory $componentFactory) {
		$this->componentFactory = $componentFactory;
	}

	/** Inject
	 * @param \Services\iLogger $logger
	 */
	public function injectLogger(iLogger $logger) {
		$this->logger = $logger;
	}

	/** Startup
	 * 
	 */
	public function startup() {
		parent::startup();

		if (!$this->user->isLoggedIn() &&
			(!$this->isLinkCurrent("Homepage:default") && !$this->isLinkCurrent("Registration:default") && !$this->isLinkCurrent("Gopay:notification"))
			&& !$this->isLinkCurrent("PassRestore:default") && PHP_SAPI != 'cli') {
			$this->redirect("Homepage:");
		} else if ($this->user->isLoggedIn()) {
			$authorizator = $this->user->getAuthorizator(TRUE);
			if ($authorizator)
				$authorizator->setRoleID($this->user->getIdentity()->data['aclRoleID']);
		}
	}

	/**
	 * @return \Component\Login\LoginForm\Login
	 */
	public function createComponentLoginForm() {
		/** @var \Component\Login\LoginForm\Login $form */
		$form = $this->componentFactory->create('\Component\Login\LoginForm\Login');
		$form->attach($this->logger);
		$form->setGoogleClient($this->googleClient);
		$form->setFacebookClient($this->facebookClient);
		return $form;
	}

}
