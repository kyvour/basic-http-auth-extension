<?php

namespace Behat\BasicHttpAuthExtension\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

/**
 * Class BasicHttpAuthContextInitializer.
 */
class BasicHttpAuthContextInitializer implements ContextInitializer
{
    /**
     * Parameters of context.
     *
     * @var array
     */
    private $parameters = [];

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if ($context instanceof BasicHttpAuthContextInterface) {
            $context->setBasicHttpAuthParameters($this->parameters);
        }
    }
}
