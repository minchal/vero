<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;
use Doctrine\ORM\EntityManager;

/**
 * Rule to check if value is unique in database.
 * 
 * Options:
 *  - entity (Entity class name recognized by Doctrine, required)
 *  - field (column name, required)
 *  - id (Entity ID to ignore)
 *  - id_field (default: 'id', Entity ID column)
 */
class Unique extends BasicRule
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * This rule needs Doctrine's EntityManager instance to work with.
     */
    public function __construct(EntityManager $em)
    {
        $this -> em = $em;
    }
    
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        $qb = $this -> em -> createQueryBuilder();
        $qb -> select('COUNT(e)')
            -> from($this -> option($options, 'entity'), 'e')
            -> where($qb->expr()->eq('e.'.$this -> option($options, 'field'), ':value'))
            -> setParameter('value', $value);
        
        if ($id = $this ->option($options, 'id')) {
            $qb -> andWhere($qb->expr()->neq('e.'.$this -> option($options, 'id_field', 'id'), ':id'))
                -> setParameter('id', $id);
        }
        
        if ($qb -> getQuery() -> getSingleScalarResult()) {
            $this -> optionalError($options, 'unique');
            return false;
        }
        
        return true;
    }
}
