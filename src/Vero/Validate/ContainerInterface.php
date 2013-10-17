<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate;

/**
 * Validator Field Container.
 */
interface ContainerInterface
{
    /**
     * Get list of all registered fields.
     * 
     * @return array
     */
    public function getAll();
    
    /**
     * Check, if field exists.
     * 
     * @return boolean
     */
    public function exists($field);
    
    /**
     * Get rule for field.
     * 
     * @return Rule|string
     */
    public function rule($field);
    
    /**
     * Get field additional options.
     * 
     * @return array
     */
    public function options($field);
}
