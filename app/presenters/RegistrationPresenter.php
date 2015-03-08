<?php
/**
 * User: Martin
 * Date: 29.11.13
 * Time: 13:35
 */

namespace App;

class RegistrationPresenter extends BasePresenter {

	/**
	 * @return \Component\Registration\RegistrationForm
	 */
	protected function createComponentRegForm() {
		$form = $this->componentFactory->create('\Component\Registration\RegistrationForm');

		return $form;
	}
} 