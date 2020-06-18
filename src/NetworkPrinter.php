<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\ClientPrinter;

/**
 * It represents a Network IP/Ethernet printer which can be reached from the client machine.
 */
class NetworkPrinter extends ClientPrinter
{

    /**
     * Gets or sets the DNS name assigned to the printer. Default is an empty string
     * @var string
     */
    public $dnsName = "";
    /**
     * Gets or sets the Internet Protocol (IP) address assigned to the printer. Default value is an empty string
     * @var string
     */
    public $ipAddress = "";
    /**
     * Gets or sets the port number assigned to the printer. Default value is 0
     * @var integer
     */
    public $port = 0;

    /**
     * Creates an instance of the NetworkPrinter class with the specified DNS name or IP Address, and port number.
     * @param string $dnsName The DNS name assigned to the printer.
     * @param string $ipAddress The Internet Protocol (IP) address assigned to the printer.
     * @param integer $port The port number assigned to the printer.
     */
    public function __construct($dnsName, $ipAddress, $port)
    {
        $this->printerId = chr(4);
        $this->dnsName = $dnsName;
        $this->ipAddress = $ipAddress;
        $this->port = $port;
    }

    public function serialize()
    {

        if (Utils::isNullOrEmptyString($this->dnsName) && Utils::isNullOrEmptyString($this->ipAddress)) {
            throw new Exception("The specified network printer settings is not valid. You must specify the DNS Printer Name or its IP address.");
        }

        return $this->printerId . $this->dnsName . Utils::SER_SEP . $this->ipAddress . Utils::SER_SEP . $this->port;
    }
}