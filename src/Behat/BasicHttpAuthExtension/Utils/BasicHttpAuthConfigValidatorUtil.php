<?php

namespace Behat\BasicHttpAuthExtension\Utils;

/**
 * Class BasicHttpAuthConfigValidator.
 */
class BasicHttpAuthConfigValidatorUtil
{
    /**
     * Returns closure for validation of user setting.
     *
     * @return \Closure
     */
    public static function getUserValidationClosure()
    {
        return function ($value) {
            return !(null === $value || false === $value || (is_string($value) && '' !== $value));
        };
    }

    /**
     * Returns closure for validation of password setting.
     *
     * @return \Closure
     */
    public static function getPassValidationClosure()
    {
        return function ($value) {
            return !is_string($value);
        };
    }
}
