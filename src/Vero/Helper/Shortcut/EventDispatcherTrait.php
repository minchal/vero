<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Shortcuts to use EventDispatcher service.
 * 
 * Requires: DITrait
 */
trait EventDispatcherTrait
{
    /**
     * Dispatch event in EventDispatcher
     * 
     * @param type $k
     * @param \Symfony\EventDispatcher\Event
     */
    public function dispatch($name, $event = null)
    {
        if (is_array($event)) {
            $event = new GenericEvent($this, $event);
        }
        
        return $this -> get('event-dispatcher') -> dispatch($name, $event);
    }
}
