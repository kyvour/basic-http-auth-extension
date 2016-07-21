<?php

namespace Behat\BasicHttpAuthExtension\ServiceContainer;

use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Behat\Context\ServiceContainer\ContextExtension;
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
 * Extensions are the core entities in Testwork. Almost all framework
 * functionality in Testwork and its different implementations is provided
 * through extensions.
 */
class BasicHttpAuthExtension implements ExtensionInterface
{

    /**
     * Returns the BasicHttpAuthExtension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'basichttpauth';
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows
     * extensions to hook into the configuration of other extensions providing
     * such an extension point. Need to be implemented due to interface
     * declaration.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {

    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     * This method need to be implemented due to interface declaration.
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function process(ContainerBuilder $containerBuilder)
    {

    }

    /**
     * Setups default configuration for the extension and provides validation
     * for this configuration. Usually this configuration will be provided with
     * behat.yml file.
     *
     * @param ArrayNodeDefinition $nodeBuilder
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function configure(ArrayNodeDefinition $nodeBuilder)
    {
        $invalidUserMsg = 'Invalid Http Auth user `%s`. Value should be null, false or non empty string';
        $invalidPassMsg = 'Invalid Http Auth password `%s`. Value should be null, false or nin empty string';

        /**
         * @var \Closure $ifNotValidUser
         *
         * @param $value
         *
         * @return bool
         *  Boolean flag if user parameter for Basic Http Auth is invalid.
         *  The user parameter should be null, false or non empty string.
         */
        $ifNotValidUser = function ($value) {
            return !(null === $value || false === $value || (is_string($value) && '' !== $value));
        };

        /**
         * @var \Closure $ifNotValidPass
         *
         * @param $value
         *
         * @return bool
         *  Boolean flag if password parameter for Basic Http Auth is invalid.
         *  The password should be a string.
         */
        $ifNotValidPass = function ($value) {
            return !is_string($value);
        };

        // Build configuration's array node.
        $nodeBuilder->children()
          ->arrayNode('auth')
          ->addDefaultsIfNotSet()
          ->disallowNewKeysInSubsequentConfigs()
          ->children()
          ->scalarNode('user')
          ->defaultNull()
          ->validate()
          ->ifTrue($ifNotValidUser)
          ->thenInvalid($invalidUserMsg)
          ->end()
          ->end()
          ->scalarNode('password')
          ->treatNullLike('')
          ->treatFalseLike('')
          ->defaultValue('')
          ->validate()
          ->ifTrue($ifNotValidPass)
          ->thenInvalid($invalidPassMsg)
          ->end()
          ->end()
          ->end()
          ->end()
          ->end();
    }

    /**
     * Loads BasicHttpAuth extension services into container builder and sets
     * parameters related to this extension to the ParameterBag (Class which
     * stores extensions' parameters).
     *
     * @param ContainerBuilder $containerBuilder
     * @param array $config
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    public function load(ContainerBuilder $containerBuilder, array $config)
    {
        $this->loadContextInitializer($containerBuilder);
        $this->loadSessionsListener($containerBuilder);

        $containerBuilder->setParameter('basichttpauth.parameters', $config);
        $containerBuilder->setParameter('basichttpauth.auth', $config['auth']);
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
        'Behat\BasicHttpAuthExtension\Context\BasicHttpAuthContextInitializer',
        array('%basichttpauth.parameters%')
      );

      $this->addDefinitionTag(
        $definition,
        ContextExtension::INITIALIZER_TAG
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
     * @param ContainerBuilder $containerBuilder
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    private function loadSessionsListener(ContainerBuilder $containerBuilder)
    {
        $definition = new Definition(
          'Behat\BasicHttpAuthExtension\Listener\BasicHttpAuthSessionsListener',
          array(new Reference(MinkExtension::MINK_ID), '%basichttpauth.auth%')
        );

        $this->addDefinitionTag(
          $definition,
          EventDispatcherExtension::SUBSCRIBER_TAG
        );

        $containerBuilder->setDefinition(
          'basichttpauth.listener.sessions',
          $definition
        );
    }

    /**
     * Adds tag to definition.
     *
     * @param Definition $definition
     * @param string $tag
     */
    private function addDefinitionTag(Definition $definition, $tag)
    {
      $definition->addTag($tag, array('priority' => 0));
    }

}
