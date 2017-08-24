<?php

namespace Behat\BasicHttpAuthExtension\Config\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;

/**
 * Class BasicHttpAuthConfigBuilderUtil.
 */
class AuthConfigBuilder
{

    /**
     * Appends ArrayNodeDefinition for HTTP Auth to the root node.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root
     *  The root node for HTTP Auth config.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function appendAuthNode(ArrayNodeDefinition $root)
    {
        $root->append($this->authNode());
    }

    /**
     * Returns node definition for array with HTTP auth parameters.
     *
     * @return ArrayNodeDefinition
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function authNode()
    {
        $auth = new ArrayNodeDefinition('auth');
        $auth->cannotBeEmpty()
            ->addDefaultsIfNotSet()
            ->disallowNewKeysInSubsequentConfigs()
            ->append($this->userNode())
            ->append($this->passwordNode())
        ;

        return $auth;
    }

    /**
     * Returns definition for HTTP Auth user parameter.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function userNode()
    {
        $user = new ScalarNodeDefinition('user');

        $user->cannotBeEmpty()
            ->defaultFalse()
            ->treatNullLike(false)
            ->validate()
                ->ifTrue(function ($v) {
                    // Valid values are false or not empty string.
                    return !(false === $v || (is_string($v) && '' !== $v));
                })
                ->thenInvalid('HTTP Auth user should be non empty string')
            ->end();

        return $user;
    }

    /**
     * Returns definition for HTTP Auth password parameter.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function passwordNode()
    {
        $password = new ScalarNodeDefinition('password');

        $password->defaultValue('')
            ->treatFalseLike('')
            ->treatNullLike('')
            ->validate()
                ->ifTrue(function ($v) {
                    // Valid values are null or false or any string.
                    return !(null === $v || false === $v || is_string($v));
                })
                ->thenInvalid('HTTP Auth password should be a string')
            ->end();

        return $password;
    }
}
