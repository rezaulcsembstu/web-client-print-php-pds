<?php

namespace Neodynamic\SDK\Web;


/**
 * Specifies the number of stop bits used for Serial Port settings.
 */
class SerialPortStopBits
{
    const NONE = 0;
    const ONE = 1;
    const TWO = 2;
    const ONE_POINT_FIVE = 3;
    public static function parse($val)
    {
        if ($val === 'NONE') return 0;
        if ($val === 'ONE') return 1;
        if ($val === 'TWO') return 2;
        if ($val === 'ONE_POINT_FIVE') return 3;
        return 0;
    }
}