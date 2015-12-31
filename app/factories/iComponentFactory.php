<?php

/**
 *
 * @author Martin Chudoba
 */
interface iComponentFactory {
	public function create($class, array $args = array());
}

?>
