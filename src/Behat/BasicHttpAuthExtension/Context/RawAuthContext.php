<?php

namespace Behat\BasicHttpAuthExtension\Context;

/**
 * Class RawBasicHttpAuthContext.
 */
class RawAuthContext implements AuthContextInterface
{

    /**
     * @var array $parameters
     *  An array of Basic Auth parameters.
     */
    private $parameters = [];

    /**
     * @inheritdoc
     *
     * @throws \LogicException
     */
    public function setBasicHttpAuthParameters(array $parameters)
    {
        if (count($this->parameters)) {
            throw new \LogicException(
                'If Basic Auth parameters are set, they cannot be overwritten.'
            );
        }

        $this->parameters = $parameters;
    }
}
