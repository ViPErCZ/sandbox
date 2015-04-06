<?php

namespace Component\Login\LoginForm;

use Component\Base\WithLogged\BaseControl;
use Illagrenan\Facebook\FacebookConnect;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Localization\ITranslator;

/**
 * Description of Login
 *
 * @author Martin Chudoba
 * @licence GNU GPL component 
 */
class Login extends BaseControl {

	/** @var User */
	private $user;

	/** @var \Google_Client */
	private $googleClient;

	/** @var FacebookConnect */
	private $facebookClient;

	/** Konstruktor
	 * @param User $user
	 * @param ITranslator $translator
	 */
	public function __construct(User $user, ITranslator $translator) {
		parent::__construct();

		$this->user = $user;
		$this->translator = $translator;
		$this->googleClient = NULL;
		$this->facebookClient = NULL;
	}

	/**
	 * @param \Google_Client $googleClient
	 */
	public function setGoogleClient(\Google_Client $googleClient) {
		$this->googleClient = $googleClient;
	}

	/**
	 * @param FacebookConnect $facebookClient
	 */
	public function setFacebookClient(FacebookConnect $facebookClient) {
		$this->facebookClient = $facebookClient;
	}

	/**
	 * Tato metoda odpovídá nastavené redirectURI adrese pro Google_Clienta
	 */
	public function handleGoogleLogin() {
		$get = $this->getPresenter()->getRequest()->getParameters();
		if (isset($get['code']) && $this->googleClient) {
			try {
				$this->user->login("google", $get['code']);
				$this->notify("Uživatel se úspěšně přihlášil.");
				$this->getPresenter()->redirect("Homepage:");
			} catch (AuthenticationException $e) {
				$this->notify("Uživateli se nepovedlo přihlásit přes službu Google. " . $e->getMessage());
				$this->flashMessage($e->getMessage());
				$this->getPresenter()->redirect("Homepage:");
			}
		} else {
			$this->getPresenter()->redirect("Homepage:");
		}
	}

	/**
	 * Tato metoda odpovídá nastavené redirectURI adrese pro Facebbok
	 */
	public function handleFacebookLogin() {
		$get = $this->getPresenter()->getRequest()->getParameters();
		if (isset($get['code']) && $this->facebookClient) {
			try {
				$this->user->login("facebook", $this->facebookClient->getFacebookUser()->getEmail());
				$this->notify("Uživatel se úspěšně přihlášil.");
				$this->getPresenter()->redirect("Homepage:");
			} catch (AuthenticationException $e) {
				$this->notify("Uživateli se nepovedlo přihlásit přes službu Facebook. " . $e->getMessage());
				$this->flashMessage($e->getMessage());
				$this->getPresenter()->redirect("Homepage:");
			}
		}
	}

	/** Render
	 *
	 */
	public function render() {
		$template = $this->template;
		$template->setFile(dirname(__FILE__) . '/latte/loginform.latte');
		$template->googleClient = $this->googleClient;
		$template->facebookClient = $this->facebookClient;

		$template->render();
	}

	/** Create Component
	 * @return Form
	 */
	protected function createComponentLoginForm() {
		$form = new Form();
		$form->setTranslator($this->translator);

		$form->addText('nick', $this->translator->translate('login_nick_label'))
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Nick')
			->setEmptyValue('')
			->setRequired("Prosím zadejte vaše přihlašovací jméno.");

		$form->addPassword('pass', $this->translator->translate('login_pass_label'))
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Heslo')
			->setEmptyValue('')
			->setRequired("Prosím zadejte vaše přihlašovací heslo.");

		$form->addCheckbox("remember", "Zapamatovat si přihlášení");

		$form->addSubmit('sender', 'Přihlásit se')
			->setAttribute('class', "btn btn-primary btn-lg btn-block");

		$form->onSuccess[] = callback($this, 'LoginFormSubmitted');

		return $form;
	}

	/** Odeslání formuláře
	 * 
	 */
	public function LoginFormSubmitted(Form $form) {
		$values = $form->getValues();
		$username = $values["nick"];
		$password = $values["pass"];

		// nastavíme expiraci
		$this->user->setExpiration('+ 15 minutes', TRUE, TRUE);

		if (!$this->user->isLoggedIn()) {
			try {
				// pokusíme se přihlásit uživatele...
				$this->user->login("db", $username, $password);
				// nastavíme expiraci
				if ($values['remember'])
					$this->user->setExpiration('+ 365 day', TRUE);
				else
					$this->user->setExpiration('+ 15 minutes', TRUE);
				// ...a v případě úspěchu presměrujeme na další stránku

				$this->notify("Uživatel se úspěšně přihlášil.");
				if ($this->getPresenter()->isAjax()) {
					$json = new stdClass();
					$json->isLogin = TRUE;
					$response = new JsonResponse($json);
					$this->getPresenter()->sendResponse($response);
				} else {
					$this->getPresenter()->redirect("Homepage:");
				}
			} catch (AuthenticationException $e) {
				$this->notify("Uživateli " . $username . " se nepovedlo přihlásit.");
				$this->flashMessage($e->getMessage());
				$this->getPresenter()->redirect("Homepage:");
			}
		} elseif ($this->getPresenter()->isAjax()) {
			$json = new stdClass();
			$json->isLogin = TRUE;
			$response = new JsonResponse($json);
			$this->getPresenter()->sendResponse($response);
		}
	}

}