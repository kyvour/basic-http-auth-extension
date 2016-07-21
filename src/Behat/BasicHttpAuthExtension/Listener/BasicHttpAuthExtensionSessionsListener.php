<?php

namespace Behat\BasicHttpAuthExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Mink\Mink;
use Behat\MinkExtension\Listener\SessionsListener as MinkSessionListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * BasicHttpAuth sessions listener.
 * Listens Behat events and configures Mink sessions.
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
     * BasicHttpAuth session listener constructor.
     * Stores Mink instance and Basic HTTP Auth configuration for Mink session
     * updates.
     *
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
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
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
     * Returns priority for the handler of BasicHttpAuthSessionsListener for the
     * beforeScenarioTested event based on lowest priority of
     * MinkSessionListener handlers.
     *
     * @return int
     */
    protected static function getBeforeScenarioListenerPriority()
    {
        /*
         * Set default priority for the event handler.
         * Mink session listener has priority 10, so by default BasicHttpAuth
         * session listener set ups to 9 to make sure that BasicHttpAuth session
         * listener will be called after the Mink listener.
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
     * Updates Mink session before each scenario.
     * Sets Basic HTTP Auth for the current session.
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
