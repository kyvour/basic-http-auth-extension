<?php

namespace Behat\BasicHttpAuthExtension\ServiceContainer;

use Behat\BasicHttpAuthExtension\Config\Definition\Builder\AuthConfigBuilder;
use Behat\BasicHttpAuthExtension\Context\Initializer\AuthContextInitializer;
use Behat\BasicHttpAuthExtension\Listener\AuthSessionListener;
use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * BasicHttpAuth extension for Behat class.
 *
 * This extension extends MinkExtension and allows easily set up Basic HTTP Auth
 * parameters (i.e. username and password) from behat configuration file.
 */
class BasicHttpAuthExtension implements ExtensionInterface
{

    /**
     * BasicHttpAuthExtension config key.
     */
    const CONFIG_KEY = 'basichttpauth';

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return static::CONFIG_KEY;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {

    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function configure(ArrayNodeDefinition $nodeBuilder)
    {
        $builder = new AuthConfigBuilder();
        $builder->appendAuthNode($nodeBuilder);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadContextInitializer($container);
        $this->loadSessionsListener($container);

        $container->setParameter('basichttpauth.parameters', $config);
    }

    /**
     * Creates a definition for a context initializer.
     *
     * @param ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition(
            AuthContextInitializer::class,
            ['%basichttpauth.parameters%']
        );
        $definition->addTag(
            ContextExtension::INITIALIZER_TAG,
            ['priority' => 0]
        );

        $container->setDefinition(
            'basichttpauth.context.initializer',
            $definition
        );
    }

    /**
     * Creates a definition for a session listener and it to the subscriber's
     * queue in the service container.
     *
     * @param ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    private function loadSessionsListener(ContainerBuilder $container)
    {
        $minkReference = new Reference(MinkExtension::MINK_ID);

        $definition = new Definition(
            AuthSessionListener::class,
            [$minkReference, '%basichttpauth.parameters%']
        );
        $definition->addTag(
            EventDispatcherExtension::SUBSCRIBER_TAG,
            ['priority' => 0]
        );

        $container->setDefinition(
            'basichttpauth.listener.session',
            $definition
        );
    }
}
