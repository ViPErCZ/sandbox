<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 27.8.14
 * Time: 16:16
 */

namespace Component\Dropzone;

use Component\Base\BaseForm;
use Nette\Application\Responses\JsonResponse;
use \Nette\Application\UI\Form as NetteForm;
use Nette\Diagnostics\Debugger;
use Nette\Http\FileUpload;

/**
 * Class Form
 * @package Component\Dropzone
 */
class Form extends BaseForm {

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm() {
		$form = parent::createComponentForm();
		$form->addUpload("file", "")->setHtmlId("drop");
		/*$form->addTextArea("area", "Note:")->setHtmlId('area');
		$form->addSubmit("sender", "Odeslat");*/

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
		$json->result = "success";

		$values = $form->getValues();

		if ($values['file'] instanceof FileUpload) {
			$values['file']->move(APP_DIR . "/../" . $values['file']->getName());
		}

		if (!is_file(APP_DIR . "/../" . $values['file']->getName())) {
			$json->result = "error";
			$json->message = "File not found.";
		}

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