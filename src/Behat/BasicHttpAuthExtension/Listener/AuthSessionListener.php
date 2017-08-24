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
    protected $config;

    /**
     * @var array
     */
    protected $defaultAuthConfig = [
        'user' => false,
        'password' => '',
    ];

    /**
     * @param Mink $mink
     * @param array $config
     */
    public function __construct(Mink $mink, array $config)
    {
        $this->mink = $mink;
        $this->config = $this->validateConfig($config);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function validateConfig(array $config) {
        // Make sure that auth array exists in the extension's config.
        if (!is_array($config['auth'])) {
            $config['auth'] = [];
        }

        // Make sure that user and password settings exist in the auth config.
        $config['auth'] = array_merge($this->defaultAuthConfig, $config['auth']);

        return $config;
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
        list($user, $password) = $this->config['auth'];
        $this->mink->getSession()->setBasicAuth($user, $password);
    }
}
