<?php

namespace Behat\BasicHttpAuthExtension\Context\Initializer;

use Behat\BasicHttpAuthExtension\Context\AuthContextInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

/**
 * Class BasicHttpAuthContextInitializer.
 */
class AuthContextInitializer implements ContextInitializer
{

    /**
     * @var array
     *  An array of Basic Auth parameters.
     */
    private $parameters;

    /**
     * @param array $parameters
     *  An array of Basic Auth parameters.
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     *  The context object.
     */
    public function initializeContext(Context $context)
    {
        if ($context instanceof AuthContextInterface) {
            $context->setBasicHttpAuthParameters($this->parameters);
        }
    }
}
