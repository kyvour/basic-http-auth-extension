<?php

namespace BasicHttpAuthExtension\Context;

/**
 * Class RawBasicHttpAuthContext.
 */
class RawBasicHttpAuthContext implements BasicHttpAuthContextInterface
{
    /**
     * Parameters of context.
     *
     * @var array $parameters
     */
    private $parameters = array();

    /**
     * @param array $parameters
     *   An array of parameters from configuration file.
     */
    public function setBasicHttpAuthParameters(array $parameters)
    {
        if (0 === count($this->parameters)) {
            $this->parameters = $parameters;
        }
    }

    /**
     * @param string $name
     *   The name of parameter from configuration.
     *
     * @return mixed
     */
    protected function getParameter($name)
    {

        /** @var array $parameters */
        $parameters = $this->parameters;

        return isset($parameters[$name]) ? $parameters[$name] : null;
    }
}
