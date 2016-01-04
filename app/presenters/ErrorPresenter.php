<?php
namespace App;

use Nette\Application as NA;


/**
 * Class ErrorPresenter
 * @package App
 */
class ErrorPresenter extends BasePresenter {

	/**
	 * @param  Exception
	 * @return void
	 */
	public function renderDefault($exception)
	{
		if ($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = TRUE;
			$this->terminate();

		} elseif ($exception instanceof NA\BadRequestException) {
			$code = $exception->getCode();
			// load template 403.latte or 404.latte or ... 4xx.latte
			$this->setView(in_array($code, array(403, 404, 405, 410, 500)) ? $code : '4xx');

		} else {
			$this->setView('500'); // load template 500.latte
		}
	}

}
