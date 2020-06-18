<?php

namespace Neodynamic\SDK\Web;

/**
 * Specifies the text alignment
 */
class TextAlignment
{
    /**
     * Left alignment
     */
    const Left = 0;
    /**
     * Right alignment
     */
    const Right = 2;
    /**
     * Center alignment
     */
    const Center = 1;
    /**
     * Justify alignment
     */
    const Justify = 3;
    /**
     * No alignment
     */
    const None = 4;


    public static function parse($val)
    {
        if ($val === 'Left') return 0;
        if ($val === 'Center') return 1;
        if ($val === 'Right') return 2;
        if ($val === 'Justify') return 3;
        if ($val === 'None') return 4;
        return 0;
    }
}