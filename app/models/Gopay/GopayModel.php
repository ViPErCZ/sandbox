<?php
/**
 * User: viper
 * Date: 11.6.2015
 * Time: 12:41
 */

namespace Model\Gopay;

use Model\Base\BaseModel;

/**
 * Class GopayModel
 * @package Model\Gopay
 */
class GopayModel extends BaseModel {

	/**
	 * @param $data
	 */
	public function insert($data) {
		$this->database->table("gopay")->insert($data);
	}

	/**
	 * @param $number
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 */
	public function getOrder($number) {
		return $this->database->table("gopay")->where("number", $number)->fetch();
	}

	/**
	 * @param $number
	 */
	public function paied($number) {
		$this->database->table("gopay")->where("number", $number)->update(array("paid" => true));
	}
}