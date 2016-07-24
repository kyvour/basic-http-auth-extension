<?php

namespace Behat\BasicHttpAuthExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\MinkExtension\Listener\SessionsListener as MinkSessionListener;
use Behat\Mink\Mink;
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
     * @return array<*,array<string|integer>>
     *  The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        $priority = self::getBeforeScenarioListenerPriority();

        return array(
            ScenarioTested::BEFORE => array('setBasicAuth', $priority),
        );
    }

    /**
     * Returns priority for the event handler.
     *
     * @return integer
     */
    private static function getBeforeScenarioListenerPriority()
    {

        $eventName = ScenarioTested::BEFORE;
        $minkEvents = MinkSessionListener::getSubscribedEvents();
        $dummyArray = array($eventName => array('dummyArray', 9));

        /**
         * Gets Mink's beforeScenario event handlers or use dummy array when
         * Mink don't have them.
         *
         * @var array|string $handlers
         */
        $handlers = array_replace($dummyArray, $minkEvents)[$eventName];

        // Returns -1 to make sure that our priority is lover than default.
        if (is_string($handlers)) {
            return -1;
        }

        return self::findLowestPriority($handlers);
    }

    /**
     * Gets over array of arrays of event handlers, adds default priority if it
     * does not exist and returns minimal priority
     *
     * @param array $handlers
     *
     * @return integer
     */
    private static function findLowestPriority(array $handlers)
    {
        /**
         * Normalize event handlers array.
         *
         * @var string|array<*,array<string|integer>> $handlers
         */
        if (!is_array($handlers[0])) {
            $handlers = array($handlers);
        }

        /**
         * Make sure that each handler has priority and replace the element with
         * this priority.
         *
         * @var int[] $handlers
         */
        $handlers = array_map(function ($element) {
            return array_replace(array($element[0], 0), $element)[1];
        }, $handlers);

        return min($handlers) - 1;
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
