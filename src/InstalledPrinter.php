<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\ClientPrinter;

/**
 * It represents a printer installed in the client machine with an associated OS driver.
 */
class InstalledPrinter extends ClientPrinter
{

    /**
     * Gets or sets the name of the printer installed in the client machine. Default value is an empty string.
     * @var string 
     */
    public $printerName = '';

    /**
     * Gets or sets whether to print to Default printer in case of the specified one is not found or missing. Default is False.
     * @var boolean 
     */
    public $printToDefaultIfNotFound = false;


    /**
     * Gets or sets the name of the tray supported by the client printer. Default value is an empty string.
     * @var string 
     */
    public $trayName = '';

    /**
     * Gets or sets the name of the Paper supported by the client printer. Default value is an empty string.
     * @var string 
     */
    public $paperName = '';


    /**
     * Creates an instance of the InstalledPrinter class with the specified printer name.
     * @param string $printerName The name of the printer installed in the client machine.
     */
    public function __construct($printerName)
    {
        $this->printerId = chr(1);
        $this->printerName = $printerName;
    }

    public function serialize()
    {

        if (Utils::isNullOrEmptyString($this->printerName)) {
            throw new Exception("The specified printer name is null or empty.");
        }

        $serData = $this->printerId . $this->printerName;

        if ($this->printToDefaultIfNotFound) {
            $serData .= Utils::SER_SEP . '1';
        } else {
            $serData .= Utils::SER_SEP . '0';
        }

        if ($this->trayName) {
            $serData .= Utils::SER_SEP . $this->trayName;
        } else {
            $serData .= Utils::SER_SEP . 'def';
        }

        if ($this->paperName) {
            $serData .= Utils::SER_SEP . $this->paperName;
        } else {
            $serData .= Utils::SER_SEP . 'def';
        }

        return $serData;
    }
}