<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n;

use DateTime;

/**
 * Tool for transforming strings to URL-safe string.
 */
class TextDateHelper
{
    /** @var Translator */
    protected $i18n;
    protected $domain = 'textdate';
    protected $prefix;
    
    /**
     * Create helper instance.
     */
    public function __construct(Translator $i18n)
    {
        $this -> i18n = $i18n;
    }
    
    /**
     * Set translator domain.
     * 
     * @param string
     * @return self
     */
    public function setDomain($domain)
    {
        $this -> domain = $domain;
        return $this;
    }
    
    /**
     * Set translator domain.
     * 
     * @param string
     * @return self
     */
    public function setTranslationKeyPrefix($prefix)
    {
        $this -> prefix = $prefix;
        return $this;
    }
    
    /**
     * Get text reprezentation of date.
     * 
     * @param DateTime|int|string
     * @param int
     * @return string
     */
    public function format($date, $now = null)
    {
        if ($now === null) {
            $now = new DateTime();
        }
        
		$date = $this -> forceDateTime($date);
		$now = $this -> forceDateTime($now);
        
        $midnight = clone $now;
        $midnight -> modify('midnight');
        
        $smdiff = $midnight -> getTimestamp() - $date -> getTimestamp();
        $sdiff = $now -> getTimestamp() - $date -> getTimestamp();
        
        $year = $date -> format('Y');
        $day = $date -> format('j');
        $hi = $date -> format('H:i');
        
        if ($year != $now -> format('Y')) {
            return $this -> i18n('before this year', [$day, $this -> i18n('month '.$date -> format('n')), $year, $hi]);
        }
        
        if ($smdiff >= 6*24*60*60) {
            return $this -> i18n('this year', [$day, $this -> i18n('month '.$date -> format('n')), $hi]);
        }
        
        if ($smdiff > 2*24*60*60) {
            return $this -> i18n('week', [$this -> i18n('weekday '.$date -> format('N')), $hi]);
        }
        
        if ($smdiff > 24*60*60) {
            return $this -> i18n('before yesterday', [$hi]);
        }
        
        if ($smdiff > 0) {
            return $this -> i18n('yesterday', [$hi]);
        }
        
        if ($sdiff >= 12*60*60) {
            return $this -> i18n('today', [$hi]);
        }
        
        if ($sdiff >= 60*60-30) {
            return $this -> i18n('hours', [round($sdiff/(60*60))]);
        }
        
        if ($sdiff >= 60) {
            return $this -> i18n('minutes', [round($sdiff/60)]);
        }
        
        if ($sdiff > 0) {
            return $this -> i18n('seconds', [$sdiff]);
        }
        
        if ($sdiff == 0) {
            return $this -> i18n('now');
        }
        
		return $this -> i18n -> getFormatter() -> date($date);
    }
    
    /**
     * Shortcut for Translator::get() with helper domain.
     * 
     * @return string
     */
    private function i18n($id, $args = [])
    {
        return $this -> i18n -> get($this -> prefix . $id, $args, $this -> domain);
    }
    
    /**
     * Try to transform any variable to DateTime object.
     * 
     * @return DateTime
     */
    private function forceDateTime($date)
    {
        if ($date instanceof DateTime) {
            return $date;
        }
        
        if (ctype_digit($date)) {
            $d = new DateTime();
            $d -> setTimestamp($date);
            return $d;
        }
        
        return new DateTime($date);
    }
}
