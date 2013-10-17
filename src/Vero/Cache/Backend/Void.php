<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Cache\Backend;

use Vero\Cache\Backend;

/**
 * Dummy backend.
 */
class Void implements Backend
{
    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return true;
    }
}
