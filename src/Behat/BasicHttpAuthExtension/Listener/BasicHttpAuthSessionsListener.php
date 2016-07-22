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
     * @return array <*,array<string|integer>>
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

        $dummyHandler = array(ScenarioTested::BEFORE => array('dummyHandler', 9));

        /**
         * Gets Mink's beforeScenario event handlers or use dummy handler when
         * Mink don't have them.
         *
         * @var array|string $params
         */
        $params = array_replace(
          $dummyHandler,
          MinkSessionListener::getSubscribedEvents()
        )[ScenarioTested::BEFORE];

        // Returns -1 to make sure that our priority is lover than default.
        if (is_string($params)) {
            return -1;
        }

        return self::findLowestPriority($params);
    }

    /**
     * Gets over array of arrays of event handlers, adds default priority if it
     * does not exist and returns minimal priority
     *
     * @param array $params
     *
     * @return integer
     */
    private static function findLowestPriority(array $params)
    {
        // Normalize event handlers array.
        if (!is_array($params[0])) {
            $params = array($params);
        }

        /*
         * Make sure that each handler has priority and replace the element with
         * this priority.
         */
        $params = array_map(function($element) {
            return array_replace(array($element[0], 0), $element)[1];
        }, $params);

        return min($params) - 1;
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
