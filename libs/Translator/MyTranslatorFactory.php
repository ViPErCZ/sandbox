<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 19.7.16
 * Time: 13:05
 */

namespace Localization;

/**
 * Interface MyTranslatorFactory
 * @package Localization
 */
interface MyTranslatorFactory {

	/**
	 * @return MyTranslator
	 */
	public function create();
}