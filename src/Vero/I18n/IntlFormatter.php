<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n;

use MessageFormatter;
use IntlDateFormatter;
use NumberFormatter;

/**
 * Formatter implemetation based on Intl extension.
 */
class IntlFormatter
{
    protected $locale;
    
    /**
     * This construcor only checks, that Intl extension is available.
     * 
     * @throws \RuntimeException
     */
    public function __construct()
    {
        if (!class_exists('IntlDateFormatter')) {
            throw new \RuntimeException('Intl Extension required to use IntlFormatter.');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this -> locale = $locale;
    }
    
    /**
     * {@inheritdoc}
     * @see MessageFormatter
     */
    public function string($str, array $args = [])
    {
        $formatter = new MessageFormatter($this -> locale, $str);
        
        if (!$formatter) {
            throw new \InvalidArgumentException('String "'.$str.'" is invalid.');
        }
        
        $r = $formatter -> format($args);
        
        if ($r === false) {
            throw new \LogicException($formatter -> getErrorMessage());
        }
        
        return $r;
    }
    
    /**
     * {@inheritdoc}
     * @see IntlDateFormatter
     */
    public function date($date, $type = 'datetime')
    {
        if (is_string($date)) {
            if (is_numeric($date)) {
                $date = (int) $date;
            } else {
                $date = strtotime($date);
            }
        }
        
        if (!$date) {
            return '';
        }
        
        switch ($type) {
            case 'time':
                $fmt = new IntlDateFormatter($this->locale, IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM);
                break;
            case 'date':
                $fmt = new IntlDateFormatter($this->locale, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
                break;
            case 'iso':
                $fmt = new IntlDateFormatter($this->locale, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
                $fmt -> setPattern('yyyy-MM-dd');
                break;
            case 'iso-datetime':
                $fmt = new IntlDateFormatter($this->locale, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
                $fmt -> setPattern('yyyy-MM-dd HH:mm:ss');
                break;
            case 'w3c':
                $fmt = new IntlDateFormatter($this->locale, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
                $fmt -> setPattern('yyyy-MM-dd\'T\'HH:mm:ssZ');
                break;
            case 'short':
                $fmt = new IntlDateFormatter($this->locale, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
                $fmt -> setPattern('dd.MM.yyyy HH:mm');
                break;
            default:
                $fmt = new IntlDateFormatter($this->locale, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);
        }
        
        return $fmt -> format($date);
    }
    
    /**
     * {@inheritdoc}
     * @see NumberFormatter
     */
    public function number($value, $type = 'decimal')
    {
        switch ($type) {
            case 'percent':
            case '%':
                $fmt = new NumberFormatter($this -> locale, NumberFormatter::PERCENT);
                break;
            case 'scientific':
                $fmt = new NumberFormatter($this -> locale, NumberFormatter::SCIENTIFIC);
                break;
            default:
                $fmt = new NumberFormatter($this -> locale, NumberFormatter::DECIMAL);
                break;
        }
        
        return $fmt -> format($value);
    }
    
    /**
     * {@inheritdoc}
     * @see NumberFormatter
     */
    public function currency($value, $currency)
    {
        $fmt = new NumberFormatter($this -> locale, NumberFormatter::CURRENCY);
        return $fmt -> formatCurrency($value, $currency);
    }
}
