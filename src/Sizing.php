<?php

namespace Neodynamic\SDK\Web;

/**
 * Specifies the print sizing option.
 */
class Sizing
{
    /**
     * The content is printed based on its actual size.
     */
    const None = 0;
    /**
     * The content is printed to fit the printable area.
     */
    const Fit = 1;

    public static function parse($val)
    {
        if ($val === 'None') return 0;
        if ($val === 'Fit') return 1;
        return 0;
    }
}