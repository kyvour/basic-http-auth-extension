<?php

namespace Behat\BasicHttpAuthExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Mink\Mink;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * BasicHttpAuth sessions listener for Mink session updating.
 */
class AuthSessionListener implements EventSubscriberInterface
{

    /**
     * @var Mink
     */
    protected $mink;

    /**
     * @var array
     */
    protected $parameters;


    /**
     * @param Mink $mink
     * @param array $parameters
     */
    public function __construct(Mink $mink, array $parameters)
    {
        $this->mink = $mink;
        $this->parameters = $parameters;
    }

    /**
     * @return array<*,array<string|integer>>
     *  The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::BEFORE => ['setBasicAuth', 9],
            ExampleTested::BEFORE => ['setBasicAuth', 9],
        ];
    }

    /**
     * Adds Basic HTTP Auth to the Mink session before each scenario.
     *
     * @throws \InvalidArgumentException
     */
    public function setBasicAuth()
    {
        $auth = $this->parameters['auth'];

        $this->mink->getSession()
            ->setBasicAuth($auth['user'], $auth['password']);
    }
}
