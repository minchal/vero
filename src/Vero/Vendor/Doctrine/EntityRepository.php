<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine;

use Doctrine\ORM\EntityRepository as DoctrineRepo;

/**
 * Custom repository.
 */
class EntityRepository extends DoctrineRepo
{
    use EntityRepositoryTrait;
}
