<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 8.7.14
 * Time: 14:45
 */

namespace Component\Base;


use Component\Base\WithLogged\BaseControl;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;

abstract class BaseForm extends BaseControl {

	/** Error Submit
	 * @param Form $form
	 */
	public function Error(Form $form) {
		$json = new \stdClass();
		$json->result = "error";
		$json->message = implode("<br />", $form->getErrors());
		$json->notify = implode(",", $form->getErrors());
		$response = new JsonResponse($json);
		$this->getPresenter(TRUE)->sendResponse($response);
	}

	/**
	 * @return Form
	 */
	protected function createComponentForm() {
		$form = new Form();

		$form->onError[] = callback($this, "Error");
		$form->onSuccess[] = callback($this, "Submit");

		return $form;
	}

	abstract public function Submit(Form $form);
	abstract public function render();
} 