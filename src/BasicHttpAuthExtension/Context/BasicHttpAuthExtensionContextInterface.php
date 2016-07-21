<?php

namespace BasicHttpAuthExtension\Context;

use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Interface BasicHttpAuthContextInterface.
 */
interface BasicHttpAuthContextInterface extends SnippetAcceptingContext
{
    /**
     * @param array $parameters
     *   An array of parameters from configuration file.
     *
     * @return void
     */
    public function setBasicHttpAuthParameters(array $parameters);
}
