<?php

namespace App;

/**
 * Homepage presenter.
 *
 * @author     Martin Chudoba
 * @package    Sandbox
 */
class HomepagePresenter extends BasePresenter {
	
	/** Odhlášení uživatele
	 *  
	 * @param void
	 * @return void     
	 */
	public function renderLogout() {
		$user = $this->getUser();

		$this->logger->log("Uživatel se úspěšně odhlásil.");
		$user->logout(true);
		$this->redirect('default');
		$this->terminate();
	}

}
