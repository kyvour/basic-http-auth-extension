<?php

namespace Behat\BasicHttpAuthExtension\Validation;


/**
 * Class BasicHttpAuthConfigValidator.
 * Used for config nodes validation.
 */
class BasicHttpAuthConfigValidator
{
    /**
     * Validates value of the user setting for the Basic HTTP Auth.
     *
     * @return \Closure
     */
    public function validateConfigUser()
    {
        return function ($value) {
            return !(null === $value || false === $value || (is_string($value) && '' !== $value));
        };
    }

    /**
     * Validates value of the password setting for the Basic HTTP Auth.
     *
     * @return \Closure
     */
    public function validateConfigPass()
    {
        return function ($value) {
            return !is_string($value);
        };
    }
}
