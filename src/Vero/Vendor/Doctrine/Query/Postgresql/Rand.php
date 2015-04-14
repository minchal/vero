<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine\Query\Postgresql;

use Doctrine\ORM\Query\SqlWalker;

/**
 * Postgresql version of mysql's RAND() implementation
 */
class Rand extends \Vero\Vendor\Doctrine\Query\Mysql\Rand
{
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'RANDOM()';
    }
}
