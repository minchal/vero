<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine\Query\Mysql;

use Doctrine\ORM\Query\SqlWalker;

/**
 * Mysql version: ignore CAST
 */
class Cast extends \Vero\Vendor\Doctrine\Query\Postgresql\Cast
{
    public function getSql(SqlWalker $walker)
    {
        return $this -> field -> dispatch($walker);
    }
}
