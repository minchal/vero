<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n;

/**
 * Objects of this type gan be translated.
 */
interface Translatable
{
    /**
     * Get translor instance and translate yourself.
     * 
     * @return string
     */
    public function translate(Translator $translator);
}
