<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Log\Backend;

use Vero\Log\Backend;

/**
 * Void backend.
 */
class Void implements Backend
{
    /**
     * {@inheritdoc}
     */
    public function log($message, $level, $ip)
    {
        
    }
}
