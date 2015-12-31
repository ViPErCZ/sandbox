<?php

namespace App;

use GettextTranslator\Gettext;
use h4kuna\Gettext\InjectTranslator;
use Illagrenan\Facebook\FacebookConnect;
use Nette;
use Services\iLogger;

/**
 * Base class for all application presenters.
 *
 * @author     Martin Chudoba
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

	use InjectTranslator;

	/** @var \iComponentFactory */
	protected $componentFactory;

	/** @var  \Services\iLogger */
	protected $logger;

	/** @var \Google_Client */
	private $googleClient;

	/** @var \Illagrenan\Facebook\FacebookConnect */
	private $facebookClient;

	/** Inject
	 * @param \Illagrenan\Facebook\FacebookConnect $facebookClient
	 */
	public function injectFacebookClient(FacebookConnect $facebookClient) {
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
		$this->translator->setLanguage("cs");

		if (!$this->user->isLoggedIn() &&
			(!$this->isLinkCurrent("Homepage:default") && !$this->isLinkCurrent("Registration:default") && !$this->isLinkCurrent("Gopay:notification"))
			&& PHP_SAPI != 'cli') {
			$this->redirect("Homepage:");
		} else if ($this->user->isLoggedIn()) {
			$authorizator = $this->user->getAuthorizator(TRUE);
			if ($authorizator)
				$authorizator->setRoleID($this->user->getIdentity()->data['aclRoleID']);
		}
	}

	/** Vytvoreni komponety - Formulář přihlašovacího okna
	 *
	 * @return Login      
	 */
	public function createComponentLoginForm() {
		$form = $this->componentFactory->create('\Component\Login\LoginForm\Login');
		$form->attach($this->logger);
		$form->setGoogleClient($this->googleClient);
		$form->setFacebookClient($this->facebookClient);
		return $form;
	}

}
