<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Auth;

/**
 * Interface represents instance of User object, that can be authorised.
 */
interface User
{
    /**
     * Get user ID.
     * 
     * @return mixed
     */
    public function getId();
}
