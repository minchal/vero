<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\I18n;

/**
 * Backend provides raw string for I18n service.
 */
interface Backend
{
    /**
     * Get string from specified section and lang.
     * Method should return null, if string in section and lang was not found.
     * 
     * @param string
     * @param string
     * @param string
     * @retrun string|null
     */
    public function get($lang, $section, $id);
    
    /**
     * Get all strings from specified section and lang.
     * 
     * @param string
     * @param string
     * @retrun array
     */
    public function getAll($lang, $section);
}
