<?php

namespace Neodynamic\SDK\Web;

/**
 * It represents the default printer installed in the client machine.
 */
class DefaultPrinter extends ClientPrinter
{
    public function __construct()
    {
        $this->printerId = chr(0);
    }

    public function serialize()
    {
        return $this->printerId;
    }
}