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

        // Set default priority for the event handler.
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
        if (empty($minkSubscribedEvents[ScenarioTested::BEFORE])) {
            return $priority;
        }

        /**
         * Get Mink handlers for beforeScenarioTested event.
         *
         * @var array|string $params
         */
        $params = $minkSubscribedEvents[ScenarioTested::BEFORE];

        if (is_string($params[0])) {
            $params = array_replace(array($params[0], -1), $params);
            $priority = $params[1];
        }

        // Get the lowest priority of existing event handlers.
        if (is_array($params[0])) {

            $priority = array_reduce(
              $params,
              function ($carry, $item) {
                  $item = array_replace(array($item[0], -1), $item);
                  return min($carry, $item[1] - 1);
              },
              $priority
            );
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
