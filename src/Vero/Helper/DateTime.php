<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper;

use DateTime as DT;

/**
 * helpers for standard DateTime class.
 */
class DateTime
{
    /**
     * Try to parse string as DateTime, without throwing exceptions.
     * If string is empty, also returns null.
     * 
     * @param string
     * @return null|DateTime
     */
    public static function parse($input)
    {
        if ($input) {
            try {
                return new DT($input);
            } catch (\Exception $e) {
            }
        }
        
        return null;
    }
}
