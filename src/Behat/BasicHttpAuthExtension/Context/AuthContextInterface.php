<?php

namespace Behat\BasicHttpAuthExtension\Context;

use Behat\Behat\Context\Context;

/**
 * Interface BasicHttpAuthContextInterface.
 */
interface AuthContextInterface extends Context
{

    /**
     * @param array $parameters
     *  An array of Basic Auth parameters.
     */
    public function setBasicHttpAuthParameters(array $parameters);
}
