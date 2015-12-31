<?php

namespace Model\Base;
use Nette\Database\Context;

/**
 * Description of BaseModel
 *
 * @author Martin Chudoba
 */
abstract class BaseModel {
	
	/** @var Context */
	protected $database;
	
	/** Konstruktor
	 * 
	 * @param Context $database
	 */
	public function __construct(Context $database) {
		$this->database = $database;
	}
}

?>
