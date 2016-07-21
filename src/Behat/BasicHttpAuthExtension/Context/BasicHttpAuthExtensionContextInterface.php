<?php

namespace Behat\BasicHttpAuthExtension\Context;

use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Interface BasicHttpAuthContextInterface.
 */
interface BasicHttpAuthContextInterface extends SnippetAcceptingContext
{
    /**
     * Set parameters from configuration.
     *
     * @param array $parameters
     *   An array of parameters from configuration file.
     */
    public function setBasicHttpAuthParameters(array $parameters);
}
