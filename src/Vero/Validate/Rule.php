<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate;

/**
 * Rule interface for validation.
 * Rule implementation can have any parameters.
 */
interface Rule
{
    /**
     * Rule must be created for specific instance of validator.
     */
    public function setValidator(Validator $validator);
    
    /**
     * Test value with this Rule.
     * 
     * @param mixed
     * @param array
     * @return boolean
     */
    public function test(&$value, array $options = array());
    
    /**
     * Get Error of last test.
     * If method if used before test() or after succed test(), returned value is undefined!
     * 
     * @return Error
     */
    public function getLastError();
}
