<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\ClientPrinter;
use Neodynamic\SDK\Web\SerialPortParity;
use Neodynamic\SDK\Web\SerialPortStopBits;
use Neodynamic\SDK\Web\SerialPortHandshake;

/**
 * It represents a printer which is connected through a serial port in the client machine.
 */
class SerialPortPrinter extends ClientPrinter
{

    /**
     * Gets or sets the serial port name, for example COM1. Default value is "COM1"
     * @var string 
     */
    public $portName = "COM1";
    /**
     * Gets or sets the serial port baud rate in bits per second. Default value is 9600
     * @var integer 
     */
    public $baudRate = 9600;
    /**
     * Gets or sets the serial port parity-checking protocol. Default value is NONE = 0
     * NONE = 0, ODD = 1, EVEN = 2, MARK = 3, SPACE = 4
     * @var integer 
     */
    public $parity = SerialPortParity::NONE;
    /**
     * Gets or sets the serial port standard number of stopbits per byte. Default value is ONE = 1
     * ONE = 1, TWO = 2, ONE_POINT_FIVE = 3
     * @var integer
     */
    public $stopBits = SerialPortStopBits::ONE;
    /**
     * Gets or sets the serial port standard length of data bits per byte. Default value is 8
     * @var integer
     */
    public $dataBits = 8;
    /**
     * Gets or sets the handshaking protocol for serial port transmission of data. Default value is XON_XOFF = 1
     * NONE = 0, REQUEST_TO_SEND = 2, REQUEST_TO_SEND_XON_XOFF = 3, XON_XOFF = 1
     * @var integer
     */
    public $flowControl = SerialPortHandshake::XON_XOFF;

    /**
     * Creates an instance of the SerialPortPrinter class wiht the specified information.
     * @param string $portName The serial port name, for example COM1.
     * @param integer $baudRate The serial port baud rate in bits per second.
     * @param integer $parity The serial port parity-checking protocol.
     * @param integer $stopBits The serial port standard number of stopbits per byte.
     * @param integer $dataBits The serial port standard length of data bits per byte.
     * @param integer $flowControl The handshaking protocol for serial port transmission of data.
     */
    public function __construct($portName, $baudRate, $parity, $stopBits, $dataBits, $flowControl)
    {
        $this->printerId = chr(3);
        $this->portName = $portName;
        $this->baudRate = $baudRate;
        $this->parity = $parity;
        $this->stopBits = $stopBits;
        $this->dataBits = $dataBits;
        $this->flowControl = $flowControl;
    }

    public function serialize()
    {

        if (Utils::isNullOrEmptyString($this->portName)) {
            throw new Exception("The specified serial port name is null or empty.");
        }

        return $this->printerId . $this->portName . Utils::SER_SEP . $this->baudRate . Utils::SER_SEP . $this->dataBits . Utils::SER_SEP . ((int) $this->flowControl) . Utils::SER_SEP . ((int) $this->parity) . Utils::SER_SEP . ((int) $this->stopBits);
    }
}