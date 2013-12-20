<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n;

interface Formatter
{
    /**
     * Set current locale.
     * 
     * @param string
     */
    public function setLocale($locale);
    
    /**
     * Format string with speciefied parameters.
     * 
     * @param string
     * @param array
     */
    public function string($str, array $args = []);
    
    /**
     * Format date and time in typical uses.
     * 
     * Available types:
     *  - datetime (default)
     *  - short
     *  - date
     *  - time
     *  - iso
     *  - iso-datetime
     *  - w3c
     * 
     * @param \DateTime|string|integer
     * @param string
     * @return string
     */
    public function date($date, $type = 'datetime');
    
    /**
     * Format number.
     * 
     * Available types:
     *  - decimal (default)
     *  - scientific
     *  - percent, %
     * 
     * @return string
     */
    public function number($value);
    
    /**
     * Format currency.
     * 
     * @param mixed
     * @param string @currency 3-letter ISO code.
     * @return string
     */
    public function currency($value, $currency);
}
