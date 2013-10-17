<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Log;

/**
 * Backend for Logger.
 */
interface Backend
{
    /**
     * Add message to log.
     * 
     * @param string
     * @param string
     * @param string
     */
    public function log($message, $level, $ip);
}
