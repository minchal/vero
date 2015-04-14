<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine;

use Vero\Application\TranslatableException as AppExc;

/**
 * Exception to report problem with finding Entity.
 */
class Exception extends AppExc
{
    /**
     * {@inheritdocs}
     */
    public function __construct($msg = 'item not found', $domain = 'global', $params = [])
    {
        parent::__construct($msg, $domain, $params);
    }
}
