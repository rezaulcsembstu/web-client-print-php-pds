<?php

namespace Neodynamic\SDK\Web;

use Exception;

/**
 * Specifies the parity bit for Serial Port settings. 
 */
class SerialPortParity
{
    const NONE = 0;
    const ODD = 1;
    const EVEN = 2;
    const MARK = 3;
    const SPACE = 4;
    public static function parse($val)
    {
        if ($val === 'NONE') return 0;
        if ($val === 'ODD') return 1;
        if ($val === 'EVEN') return 2;
        if ($val === 'MARK') return 3;
        if ($val === 'SPACE') return 4;
        return 0;
    }
}