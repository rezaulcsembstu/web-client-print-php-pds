<?php

namespace Neodynamic\SDK\Web;

/**
 * Specifies the printer's double-sided (duplex) printing capability.
 */
class Duplex
{
    /**
     * Use default value from driver.
     */
    const DEF = 0;
    /**
     * Use single-sided printing.
     */
    const SIMPLEX = 1;
    /**
     * Use double-sided printing with vertical page turning.
     */
    const VERTICAL = 2;
    /**
     * Use double-sided printing with horizontal page turning.
     */
    const HORIZONTAL = 3;

    public static function parse($val)
    {
        if ($val === 'DEF') return 0;
        if ($val === 'SIMPLEX') return 1;
        if ($val === 'VERTICAL') return 2;
        if ($val === 'HORIZONTAL') return 3;
        return 0;
    }
}