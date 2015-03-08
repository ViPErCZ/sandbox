<?php


/**
 * Description of ComponentFactory
 *
 * @author Martin Chudoba
 */
class ComponentFactory implements iComponentFactory {
	
	/** @var \Nette\DI\Container */
	private $container;


	/** Konstruktor
	 * 
	 * @param \Nette\DI\Container $container
	 */
	public function __construct(\Nette\DI\Container $container) {
		$this->container = $container;
	}
	
	/** Vytvoření komponenty
	 * 
	 * @param string $class
	 * @param array $args
	 * @return new instance of given class
	 */
	public function create($class, array $args = array()) {
		return $this->container->createInstance($class, $args);
	}
}

?>
