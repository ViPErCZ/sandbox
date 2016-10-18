<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 25.3.16
 * Time: 9:13
 */

namespace Localization;

use h4kuna\Gettext\GettextSetup;
use Nette\Localization\ITranslator;

/**
 * Class MyTranslator
 * @package Localization
 */
class MyTranslator implements ITranslator {

	/** @var GettextSetup */
	protected $translator;

	/**
	 * MyTranslator constructor.
	 */
	public function __construct(GettextSetup $gettextSetup) {
		$this->translator = $gettextSetup;
	}

	/**
	 * @param $lang
	 */
	public function setLanguage($lang) {
		$this->translator->setLanguage($lang);
	}

	/**
	 * @param $message
	 * @param null $count
	 * @return string
	 */
	function translate($message, $count = NULL) {
		return $this->translator->translate($message, $count);
	}


}