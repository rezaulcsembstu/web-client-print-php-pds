<?php

namespace Neodynamic\SDK\Web;

use Exception;

/**
 * Specifies the print orientation.
 */
class PrintOrientation
{
    /**
     * Print the document vertically.
     */
    const Portrait = 0;
    /**
     *  Print the document horizontally.
     */
    const Landscape = 1;

    public static function parse($val)
    {
        if ($val === 'Portrait') return 0;
        if ($val === 'Landscape') return 1;
        throw new Exception('Invalid value');
    }
}