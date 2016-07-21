<?php

namespace Behat\BasicHttpAuthExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Mink\Mink;
use Behat\MinkExtension\Listener\SessionsListener as MinkSessionListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * BasicHttpAuth sessions listener for Mink session updating.
 */
class BasicHttpAuthSessionsListener implements EventSubscriberInterface
{

    /**
     * @var Mink
     */
    protected $mink;

    /**
     * @var array
     */
    protected $auth;


    /**
     * @param Mink $mink
     * @param array $auth
     */
    public function __construct(Mink $mink, array $auth)
    {
        $this->mink = $mink;
        $this->auth = $auth;
    }

    /**
     * @return array<*,array<string|integer>> The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        $priority = static::getBeforeScenarioListenerPriority();

        return array(
          ScenarioTested::BEFORE => array('setBasicAuth', $priority),
        );
    }

    /**
     * Returns priority for the event handler.
     *
     * @return int
     */
    protected static function getBeforeScenarioListenerPriority()
    {

        $priority = 9;

        /** @var array $subscribedEvents */
        $subscribedEvents = MinkSessionListener::getSubscribedEvents();

        if (empty($subscribedEvents[ScenarioTested::BEFORE])) {
            return $priority;
        }

        /** @var array|string $params */
        $params = $subscribedEvents[ScenarioTested::BEFORE];

        if (is_string($params)) {
            return -1;
        }

        if (!is_array($params[0])) {
            $params = array($params);
        }

        $priority = static::findLowestPriority($params);

        return $priority;
    }

    /**
     * @param array $params
     *
     * @return int
     */
    protected static function findLowestPriority(array $params)
    {
        foreach ($params as $key => $handler) {
            $params[$key] = array_replace(array($handler[0], -1), $handler);
        }

        $params = array_map(function ($element) {
            return $element[1];
        }, $params);

        return min($params);
    }

    /**
     * Adds Basic HTTP Auth to the Mink session before each scenario.
     *
     * @throws \InvalidArgumentException
     */
    public function setBasicAuth()
    {
        $auth = $this->auth;

        if (null !== $auth['user']) {
            $this->mink->getSession()->setBasicAuth(
              $auth['user'],
              $auth['password']
            );
        }
    }
}
