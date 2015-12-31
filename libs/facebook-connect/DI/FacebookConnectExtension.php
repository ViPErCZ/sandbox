<?php

/**
 * Tento soubor je součástí rozšíření "Nette Facebook Connect"
 * @link https://github.com/illagrenan/nette-facebook-connect
 * 
 * Copyright (c) 2013 Václav Dohnal, http://www.vaclavdohnal.cz
 */

namespace Illagrenan\Facebook\DI;

use Nette;
use Nette\Config\Configurator;

/**
 * Registrace nette rozšíření.
 * 
 * @author Vašek Dohnal http://www.vaclavdohnal.cz
 */
class FacebookConnectExtension extends Nette\DI\CompilerExtension
{

    /**
     * @var array 
     */
    public $defaults = array(
        'appName'      => FALSE,
        'description'  => FALSE,
        'scope'        => FALSE,
        'appId'        => FALSE,
        'secret'       => FALSE,
        'appNamespace' => FALSE,
        'canvasUrl'    => FALSE,
        'tabUrl'       => FALSE
    );

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config  = $this->getConfig($this->defaults);

        $fbConnectParams = array(
            $config,
            '@application',
            '@httpResponse'
        );

        $builder->addDefinition($this->prefix('client'))
                ->setClass('\Illagrenan\Facebook\FacebookConnect', $fbConnectParams);
    }

	/**
	 * @param Configurator $configurator
	 */
    public static function register(Configurator $configurator)
    {
        $configurator->onCompile[] = function (Configurator $config, Nette\DI\Compiler $compiler)
                {
                    $compiler->addExtension('facebookConnect', new FacebookConnectExtension());
                };
    }

}