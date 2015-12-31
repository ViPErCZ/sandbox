<?php
/**
 * User: Martin
 * Date: 2.11.13
 * Time: 18:30
 */

namespace Services\Logger;


use Services\iLogger;

interface iObservable {
	public function attach(iLogger $observer);
	public function detach(iLogger $observer);
	public function notify($message);
} 