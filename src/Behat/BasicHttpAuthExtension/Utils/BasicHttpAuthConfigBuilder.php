<?php

namespace Behat\BasicHttpAuthExtension\Utils;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;

/**
 * Class BasicHttpAuthConfigBuilder.
 */
class BasicHttpAuthConfigBuilder
{
    /**
     * @return ArrayNodeDefinition
     *  Http Auth configuration array node.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function buildAuthArrayNode()
    {
        $auth = new ArrayNodeDefinition('auth');
        $auth->addDefaultsIfNotSet()
            ->disallowNewKeysInSubsequentConfigs()
            ->append($this->buildUserNode())
            ->append($this->buildPasswordNode())
            ->end();

        return $auth;
    }

    /**
     * @return ScalarNodeDefinition
     *  Http Auth `user` configuration node definition.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function buildUserNode()
    {
        $user = new ScalarNodeDefinition('user');
        $user->defaultNull()
            ->validate()
            ->ifTrue(BasicHttpAuthConfigValidator::getUserValidationClosure())
            ->thenInvalid(self::getConfigErrorMessage('user'))
            ->end()
            ->end();

        return $user;
    }

    /**
     * @return ScalarNodeDefinition
     *  Http Auth `password` configuration node definition.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function buildPasswordNode()
    {
        $pass = new ScalarNodeDefinition('password');
        $pass->treatNullLike('')
            ->treatFalseLike('')
            ->defaultValue('')
            ->validate()
            ->ifTrue(BasicHttpAuthConfigValidator::getPassValidationClosure())
            ->thenInvalid(self::getConfigErrorMessage('password'))
            ->end()
            ->end();

        return $pass;
    }

    /**
     * Returns error messages for configs.
     *
     * @param string $configKey
     *
     * @return string
     */
    private static function getConfigErrorMessage($configKey)
    {
        switch ($configKey) {
            case 'user':
                $msg = 'Invalid Http Auth password `%s`. '
                    . 'Value should be null, false or non empty string';
                break;
            case 'password':
                $msg = 'Invalid Http Auth password `%s`.'
                    . ' Value should be null, false or nin empty string';
                break;
            default:
                $msg = $configKey . ' setting has invalid value: `%s`';
                break;
        }

        return $msg;
    }

}
