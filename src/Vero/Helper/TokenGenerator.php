<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper;

/**
 * Generate pseudo-random token for session keys etc.
 */
class TokenGenerator
{
    public static function get($len = 32)
    {
        $str = '';
        
        while (strlen($str) < $len) {
            $str .= hash('sha512', uniqid(mt_rand(), true));
        }
        
        return substr($str, 0, $len);
    }
}
