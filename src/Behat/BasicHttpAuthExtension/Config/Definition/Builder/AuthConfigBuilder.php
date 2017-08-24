<?php

namespace Behat\BasicHttpAuthExtension\Config\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class BasicHttpAuthConfigBuilderUtil.
 */
class AuthConfigBuilder
{

    /**
     * Returns node definition for array with HTTP auth parameters.
     *
     * @return ArrayNodeDefinition
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function authNode()
    {
        $auth = new ArrayNodeDefinition('auth');
        $auth->cannotBeEmpty()
            ->addDefaultsIfNotSet()
            ->disallowNewKeysInSubsequentConfigs()
            ->children()
                ->scalarNode('user')
                    ->cannotBeEmpty()
                    ->defaultFalse()
                    ->treatNullLike(false)
                    ->validate()
                        ->ifTrue($this->invalidUserParameter())
                        ->thenInvalid('HTTP Auth user should be non empty string')
                    ->end()
                ->end()
                ->scalarNode('password')
                    ->defaultValue('')
                    ->treatFalseLike('')
                    ->treatNullLike('')
                    ->validate()
                        ->ifTrue($this->invalidPasswordParameter())
                        ->thenInvalid('HTTP Auth password should be non empty string')
                    ->end()
                ->end()
            ->end()
        ;

        return $auth;
    }

    /**
     * Returns closure for HTTP Auth parameters validation.
     *
     * @return \Closure
     */
    protected function invalidUserParameter()
    {
        return function ($v) {
            // Valid values are false or not empty string.
            return !(false === $v || (is_string($v) && '' !== $v));
        };
    }

    /**
     * Returns closure for HTTP Auth parameters validation.
     *
     * @return \Closure
     */
    protected function invalidPasswordParameter()
    {
        return function ($v) {
            // Valid values are null or false or any string.
            return !(null === $v || false === $v || is_string($v));
        };
    }
}
