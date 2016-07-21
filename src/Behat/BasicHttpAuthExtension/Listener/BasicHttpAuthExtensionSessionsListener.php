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
        $priority = self::getBeforeScenarioListenerPriority();

        return array(
          ScenarioTested::BEFORE => array('setBasicAuth', $priority),
        );
    }

    /**
     * Returns priority for the handler the beforeScenarioTested event based on
     * lowest priority of MinkSessionListener handlers.
     *
     * @return int
     */
    protected static function getBeforeScenarioListenerPriority()
    {
        /**
         * Set default priority for the event handler.
         *
         * @see \Behat\MinkExtension\Listener\SessionsListener::getSubscribedEvents
         */
        $priority = 9;

        /**
         * Get events list on which Mink session listener is subscribed.
         *
         * @var array $minkSubscribedEvents
         */
        $minkSubscribedEvents = MinkSessionListener::getSubscribedEvents();

        /*
         * Check if Mink extension is subscribed to the beforeScenarioTested
         * event and return default priority if it isn't.
         */
        if (empty($minkSubscribedEvents[ScenarioTested::BEFORE]) ||
          !is_array($minkSubscribedEvents[ScenarioTested::BEFORE])
        ) {
            return $priority;
        }

        /**
         * Get Mink handlers for beforeScenarioTested event.
         *
         * @var array|string $params
         */
        $params = $minkSubscribedEvents[ScenarioTested::BEFORE];

        // Get the lowest priority of existing event handlers.
        if (is_array($params[0])) {
            return array_reduce(
              $params,
              function($carry, $item) {
                  return array_key_exists(1, $item) ? min($carry, $item[1] - 1) : $carry;
              },
              $priority
            );
        }

        if (array_key_exists(1, $params)) {
            return min($priority, $params[1] - 1);
        }

        return $priority;
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
