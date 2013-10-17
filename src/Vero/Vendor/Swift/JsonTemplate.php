<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Swift;

/**
 * Simple implementation for mail template in JSON file.
 */
class JsonTemplate extends Template
{
    /**
     * {@inheritdoc}
     */
    public function setTemplate($tpl)
    {
        if (!file_exists($tpl)) {
            throw new \LogicException("JSON mail template file '$tpl' not found.");
        }
        
        $vars = json_decode(file_get_contents($tpl), true);
        
        if ($vars === null) {
            throw new \LogicException("JSON mail template file '$tpl' has wrong format [".json_last_error()."].");
        }
        
        $this -> subject = isset($vars['subject']) ? $vars['subject'] : '';
        $this -> body    = isset($vars['body'])    ? $vars['body']    : '';
        $this -> format  = isset($vars['format'])  ? $vars['format']  : self::FORMAT_TEXT;
        
        return $this;
    }
}
