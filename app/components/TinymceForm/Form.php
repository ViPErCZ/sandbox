<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 8.7.14
 * Time: 14:54
 */

namespace Component\TinymceForm;


use Component\Base\BaseForm;
use Nette\Application\Responses\JsonResponse;
use \Nette\Application\UI\Form as NetteForm;
use Nette\Diagnostics\Debugger;

class Form extends BaseForm {

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm() {
		$form = parent::createComponentForm();
		$form->addTextArea("area", "Note:")->setHtmlId('area');
		$form->addSubmit("sender", "Odeslat");

		return $form;
	}

	/**
	 *
	 */
	public function handleRefresh() {
		$this->render();
		exit();
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function Submit(NetteForm $form) {
		$json = new \stdClass();
		$json->result = "error";
		$json->message = $form->getValues()->area;

		$response = new JsonResponse($json);
		$this->getPresenter(TRUE)->sendResponse($response);
	}

	/**
	 *
	 */
	public function render() {
		$template = $this->getTemplate();
		$template->setFile(__DIR__ . "/latte/form.latte");

		$template->render();
	}
} 