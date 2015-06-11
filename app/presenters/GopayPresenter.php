<?php
/**
 * User: viper
 * Date: 9.6.2015
 * Time: 13:27
 */

namespace App;

use Markette\Gopay;
use Nette\Application\UI\Form;
use Nette\Database\Table\IRow;
use Tracy\Debugger;

/**
 * Class GopayPresenter
 * @package App
 */
class GopayPresenter extends BasePresenter {

	/** @var Gopay\Service @inject */
	public $gopay;

	/** @var \Model\Gopay\GopayModel @inject */
	public $gopayModel;

	/**
	 *
	 */
	public function renderDefault() {
		$this->template->channels = $this->gopay->getChannels();
	}

	/**
	 *
	 */
	public function renderFailure() {
		$this->redirect("Gopay:default");
	}

	/**
	 * @param $paymentSessionId
	 * @param $targetGoId
	 * @param $orderNumber
	 * @param $encryptedSignature
	 */
	public function renderSuccess($paymentSessionId, $targetGoId, $orderNumber, $encryptedSignature) {

		$order = $this->gopayModel->getOrder($orderNumber);

		if ($order instanceof IRow) {
			$payment = $this->gopay->restorePayment(array(
				'sum' => $order->sum,
				'variable' => $order->variable,
				'specific' => 0,
				'productName' => "Test",
			), array(
				'paymentSessionId' => $paymentSessionId,
				'targetGoId' => $targetGoId,
				'orderNumber' => $orderNumber,
				'encryptedSignature' => $encryptedSignature,
			));


			Debugger::dump($payment);
			Debugger::dump($payment->isFraud());
			Debugger::dump($payment->isPaid());
		}
	}

	/**
	 * @param $paymentSessionId
	 * @param $targetGoId
	 * @param $orderNumber
	 * @param $encryptedSignature
	 * @throws \Nette\Application\AbortException
	 */
	public function actionNotification($paymentSessionId, $targetGoId, $orderNumber, $encryptedSignature) {
		try {
			$order = $this->gopayModel->getOrder($orderNumber);

			if ($order instanceof IRow) {
				$payment = $this->gopay->restorePayment(array(
					'sum' => $order->sum,
					'variable' => $order->variable,
					'specific' => 0,
					'productName' => "Test",
				), array(
					'paymentSessionId' => $paymentSessionId,
					'targetGoId' => $targetGoId,
					'orderNumber' => $orderNumber,
					'encryptedSignature' => $encryptedSignature,
				));

				$this->logger->log("Notifikace o zaplaceni: " . $payment->isPaid());
				if ($payment->isPaid()) {
					$this->gopayModel->paied($orderNumber);
				}
			}
		} catch(\Exception $e) {
			$this->logger->log("Notifikace o zaplaceni: " . $e->getMessage());
		}
		$this->terminate();
	}

	/**
	 * @return Form
	 */
	protected function createComponentForm() {
		$form = new Form();
		$this->gopay->bindPaymentButtons($form, array($this->submittedForm));

		return $form;
	}

	/**
	 * @param Gopay\PaymentButton $button
	 */
	public function submittedForm(Gopay\PaymentButton $button) {
		$this->gopay->successUrl = $this->link('//success');
		$this->gopay->failureUrl = $this->link('//failure');
		srand((double) microtime() * 1000000);

		$order = array(
			'sum'         => rand(10, 2000),      // placená částka
			'variable'    => intval(date("Y") . date("m") . date("d") . date("H") . date("m") . date("s")), // variabilní symbol
			'specific'    => 0, // specifický symbol
			'productName' => "Test",  // název produktu (popis účelu platby)
			'customer' => array(
				'firstName'   => "Martin",
				'lastName'    => NULL,    // všechna parametry jsou volitelné
				'street'      => NULL,    // pokud některý neuvedete,
				'city'        => NULL,    // použije se prázdný řetězec
				'postalCode'  => "55101",
				'countryCode' => 'CZE',
				'email'       => "info@vipersoftware.net",
				'phoneNumber' => NULL,
			),
		);

		$payment = $this->gopay->createPayment($order);
		$model = $this->gopayModel;

		try {
			$storeIdCallback = function ($paymentId) use ($model, $order) {
				$record = array(
					"number" 	=> $paymentId,
					"variable"	=> $order['variable'],
					"sum"		=> $order['sum'],
					"name"		=> "Test",
				);
				$this->gopayModel->insert($record);
			};
			//$gopay->denyChannel($gopay::METHOD_TRANSFER);
			$response = $this->gopay->pay($payment, $button->getChannel(), $storeIdCallback);
			$this->sendResponse($response);
		} catch (Gopay\GopayException $e) {
			echo "<br><br><br>";
			echo $e->getMessage();
			echo 'Platební služba Gopay bohužel momentálně nefunguje. Zkuste to prosím za chvíli.';
		}
	}

}