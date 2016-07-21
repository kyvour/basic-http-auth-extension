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
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array<*,array<string|integer>> The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        $priority = static::getBeforeScenarioListenerPriority('MinkSessionListener');

        return array(
          ScenarioTested::BEFORE => array('setBasicAuth', $priority),
        );
    }

    /**
     * Returns priority for the handler the beforeScenarioTested event based on
     * lowest priority of MinkSessionListener handlers.
     *
     * @param EventSubscriberInterface $eventSubscriber
     *
     * @return int
     */
    protected static function getBeforeScenarioListenerPriority(
      EventSubscriberInterface $eventSubscriber
    ) {

        // Set default priority for the event handler.
        $priority = 9;

        /**
         * Get events list on which Mink session listener is subscribed.
         *
         * @var array $subscribedEvents
         */
        $subscribedEvents = $eventSubscriber::getSubscribedEvents();

        /*
         * Check if Mink extension is subscribed to the beforeScenarioTested
         * event and return default priority if it isn't.
         */
        if (empty($subscribedEvents[ScenarioTested::BEFORE])) {
            return $priority;
        }

        /**
         * Get Mink handlers for beforeScenarioTested event.
         *
         * @var array|string $params
         */
        $params = $subscribedEvents[ScenarioTested::BEFORE];

        if (is_string($params)) {
            return -1;
        }

        if (!is_array($params[0])) {
            $params = array($params);
        }

        // Get the lowest priority of existing event handlers.
        $priority = static::findLowestPriority($params);

        return $priority;
    }

    /**
     * Finds lowest priority from the array of event listener handlers.
     *
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
