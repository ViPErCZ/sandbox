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
use Nette\Localization\ITranslator;
use Nette\Templating\FileTemplate;

/**
 * Class BaseForm
 * @package Component\Base
 */
abstract class BaseForm extends BaseControl {

	/** @var string */
	protected $latteFile;

	/**
	 * Odeslání formuláře s Errory
	 * @param Form $form
	 */
	public function Error(Form $form) {
		$this->FormError($form);
	}

	/**
	 * @return Form
	 */
	protected function createComponentForm() {
		$form = new Form();

		$form->onError[] = array($this, "Error");
		$form->onSuccess[] = array($this, "Submit");

		return $form;
	}

	/**
	 *
	 */
	public function render() {
		/** @var FileTemplate $template */
		$template = $this->getTemplate();
		if ($this->translator && $this->translator instanceof ITranslator) {
			$template->setTranslator($this->translator);
		}
		$path = $this->latteFile && !empty($this->latteFile) ? $this->latteFile : __DIR__ . "/latte/" . $this->getName();
		$template->setFile($path);

		$template->render();
	}

	abstract public function Submit(Form $form);
} 