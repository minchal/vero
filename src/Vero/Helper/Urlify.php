<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper;

/**
 * Tool for transforming strings to URL-safe string.
 */
class Urlify
{
    /**
     * Transform any string to URL-safe string.
     * 
     * @param string
     * @param string String to replace spaces
     * @param int
     * @return string
     */
    public static function transform($str, $replace = '-', $maxLength = 250)
    {
		$str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$str = preg_replace('/[^\-\.\_\w]/', $replace, strtolower($str));
		$str = preg_replace('/[\\'.$replace.']+/', $replace, $str);
		return substr($str, 0, $maxLength);
    }
}
