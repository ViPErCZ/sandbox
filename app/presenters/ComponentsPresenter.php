<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 8.7.14
 * Time: 13:55
 */

namespace App;

use Component\Notification\NotificationWidget;
use Nextras\Application\UI\SecuredLinksPresenterTrait;

/**
 * Class ComponentsPresenter
 * @package App
 */
class ComponentsPresenter extends BasePresenter {

	use SecuredLinksPresenterTrait;

	/**
	 * @return \Component\TinymceForm\Form
	 */
	protected function createComponentTinyMceForm() {
		return $this->componentFactory->create('\Component\TinymceForm\Form');
	}

	/**
	 * @return \Component\Dropzone\Form
	 */
	protected function createComponentDropzone() {
		return $this->componentFactory->create('\Component\Dropzone\Form');
	}

	/**
	 * @return NotificationWidget
	 */
	protected function createComponentNotification() {
		return new NotificationWidget();
	}
} 