<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 25.3.16
 * Time: 9:31
 */

namespace Localization;

/**
 * Class TranslatorInjectTrait
 * @package Localization
 */
trait TranslatorInjectTrait {
	
	/** @persistent */
	public $lang;

	/** @var MyTranslator */
	public $translator;

	public function injectGettexSetup(MyTranslatorFactory $translator) {
		$this->translator = $translator->create();
	}

	protected function startup() {
		parent::startup();
		$this->lang = $this->translator->setLanguage($this->lang);
		//$this->translator->setLanguage("cs");
	}
}