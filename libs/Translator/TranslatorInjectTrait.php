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
	protected $translator;

	public function injectGettexSetup(MyTranslator $translator) {
		$this->translator = $translator;
	}

	protected function startup() {
		parent::startup();
		$this->lang = $this->translator->setLanguage($this->lang);
	}
}