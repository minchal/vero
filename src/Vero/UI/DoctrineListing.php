<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\UI;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Listing to work with Doctrine\ORM Paginator helper.
 */
class DoctrineListing extends Listing
{
    /**
     * Prepare listing instance from Doctrine Query or QueryBuilder.
     * 
     * Sets 'order by' only for QueryBuilder.
     * 
     * @param Doctrine\ORM\Query|Doctrine\ORM\QueryBuilder
     * @param boolean
     * @return self
     * @see Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function setQuery($query, $fetchJoinCollection = false)
    {
        if ($query instanceof QueryBuilder) {
            if ($this -> order()) {
                call_user_func_array([$query, 'orderBy'], (array) $this -> order());
            }
            
            $query = $query -> getQuery();
        }
        
        if (!$query instanceof Query) {
            throw \InvalidArgumentException('Instance of Doctrine\ORM\Query or Doctrine\ORM\QueryBuilder required!');
        }
        
        $paginator = new Paginator($query, $fetchJoinCollection);
        
        $this -> setCount($paginator -> count());
        
        $query
            -> setFirstResult($this -> offset())
            -> setMaxResults($this -> limit());
        
        $this -> setItems($paginator);
        
        return $this;
    }
}
