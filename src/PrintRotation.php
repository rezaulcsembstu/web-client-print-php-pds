<?php

namespace Neodynamic\SDK\Web;

use Exception;

/**
 * Specifies the print rotation.
 */
class PrintRotation
{
    /**
     * Print page without rotation.
     */
    const None = 0;
    /**
     * Print page rotated by 90 degrees clockwise.
     */
    const Rot90 = 1;
    /**
     * Print page rotated by 180 degrees.
     */
    const Rot180 = 2;
    /**
     * Print page rotated by 270 degrees clockwise.
     */
    const Rot270 = 3;

    public static function parse($val)
    {
        if ($val === 'None') return 0;
        if ($val === 'Rot90') return 1;
        if ($val === 'Rot180') return 2;
        if ($val === 'Rot270') return 3;
        throw new Exception('Invalid value');
    }
}