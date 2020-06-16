<?php

namespace Neodynamic\SDK\Web;

/**
 *  It represents a printer which will be selected by the user in the client machine. The user will be prompted with a print dialog.
 */
class UserSelectedPrinter extends ClientPrinter
{
    public function __construct()
    {
        $this->printerId = chr(5);
    }

    public function serialize()
    {
        return $this->printerId;
    }
}