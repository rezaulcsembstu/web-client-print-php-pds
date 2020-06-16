<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\ClientPrinter;

/**
 * It represents a printer which is connected through a parallel port in the client machine.
 */
class ParallelPortPrinter extends ClientPrinter
{

    /**
     * Gets or sets the parallel port name, for example LPT1. Default value is "LPT1"
     * @var string 
     */
    public $portName = "LPT1";

    /**
     * Creates an instance of the ParallelPortPrinter class with the specified port name.
     * @param string $portName The parallel port name, for example LPT1.
     */
    public function __construct($portName)
    {
        $this->printerId = chr(2);
        $this->portName = $portName;
    }

    public function serialize()
    {

        if (Utils::isNullOrEmptyString($this->portName)) {
            throw new Exception("The specified parallel port name is null or empty.");
        }

        return $this->printerId . $this->portName;
    }
}