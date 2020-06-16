<?php

namespace Neodynamic\SDK\Web;

use Exception;

/**
 * Specifies the control protocol used in establishing a serial port communication.
 */
class SerialPortHandshake
{
    const NONE = 0;
    const REQUEST_TO_SEND = 2;
    const REQUEST_TO_SEND_XON_XOFF = 3;
    const XON_XOFF = 1;
    public static function parse($val)
    {
        if ($val === 'NONE') return 0;
        if ($val === 'XON_XOFF') return 1;
        if ($val === 'REQUEST_TO_SEND') return 2;
        if ($val === 'REQUEST_TO_SEND_XON_XOFF') return 3;
        throw new Exception('Invalid value');
    }
}