<?php

namespace Neodynamic\SDK\Web;

use Exception;

/**
 * Some utility functions used by WebClientPrint for PHP solution.
 */
class Utils
{
    const SER_SEP = '|';

    static function isNullOrEmptyString($s)
    {
        return (!isset($s) || trim($s) === '');
    }

    static function formatHexValues($s)
    {

        $buffer = '';

        $l = strlen($s);
        $i = 0;

        while ($i < $l) {
            if ($s[$i] == '0') {
                if ($i + 1 < $l && ($s[$i] == '0' && $s[$i + 1] == 'x')) {
                    if (
                        $i + 2 < $l &&
                        (($s[$i + 2] >= '0' && $s[$i + 2] <= '9') || ($s[$i + 2] >= 'a' && $s[$i + 2] <= 'f') || ($s[$i + 2] >= 'A' && $s[$i + 2] <= 'F'))
                    ) {
                        if (
                            $i + 3 < $l &&
                            (($s[$i + 3] >= '0' && $s[$i + 3] <= '9') || ($s[$i + 3] >= 'a' && $s[$i + 3] <= 'f') || ($s[$i + 3] >= 'A' && $s[$i + 3] <= 'F'))
                        ) {
                            try {
                                $buffer .= chr(intval(substr($s, $i, 4), 16));
                                $i += 4;
                                continue;
                            } catch (Exception $ex) {
                                throw new Exception("Invalid hex notation in the specified printer commands at index: " . $i);
                            }
                        } else {
                            try {

                                $buffer .= chr(intval(substr($s, $i, 3), 16));
                                $i += 3;
                                continue;
                            } catch (Exception $ex) {
                                throw new ArgumentException("Invalid hex notation in the specified printer commands at index: " . $i);
                            }
                        }
                    }
                }
            }

            $buffer .= substr($s, $i, 1);

            $i++;
        }

        return $buffer;
    }

    public static function intToArray($i)
    {
        return pack(
            'C4',
            ($i >>  0) & 0xFF,
            ($i >>  8) & 0xFF,
            ($i >> 16) & 0xFF,
            ($i >> 24) & 0xFF
        );
    }

    public static function strleft($s1, $s2)
    {
        return substr($s1, 0, strpos($s1, $s2));
    }

    public static function strContains($s1, $s2)
    {
        return (strpos($s1, $s2) !== false);
    }

    public static function strEndsWith($s1, $s2)
    {
        return substr($s1, -strlen($s2)) === $s2;
    }

    public static function strStartsWith($s1, $s2)
    {
        return substr($s1, 0, strlen($s2)) === $s2;
    }
}