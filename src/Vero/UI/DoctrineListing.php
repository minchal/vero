<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\UI;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;

/**
 * Listing to work with Doctrine\ORM Paginator helper.
 */
class DoctrineListing extends Listing
{
    /**
     * Prepare listing instance from Doctrine ORM Query or QueryBuilder.
     * 
     * Sets 'order by' only for QueryBuilder.
     * 
     * @param Query|QueryBuilder
     * @param boolean
     * @param boolean
     * @param boolean
     * @return self
     * @see Paginator
     */
    public function setQuery($query, $fetchJoinCollection = false, $appendLimit = true, $appendOrder = true)
    {
        if ($query instanceof QueryBuilder) {
            if ($appendOrder && $this -> order()) {
                call_user_func_array([$query, 'orderBy'], (array) $this -> order());
            }
            
            $query = $query -> getQuery();
        }
        
        if (!$query instanceof Query) {
            throw \InvalidArgumentException('Instance of Doctrine\ORM\Query or Doctrine\ORM\QueryBuilder required!');
        }
        
        if ($appendLimit) {
            $paginator = new Paginator($query, $fetchJoinCollection);

            $this -> setCount($paginator -> count());

            $query
                -> setFirstResult($this -> offset())
                -> setMaxResults($this -> limit());

            $this -> setItems($paginator);
        } else {
            $this -> setItems($query -> getResult());
        }
        
        return $this;
    }
    
    /**
     * Prepare listing instance from Doctrine DBAL QueryBuilder.
     * 
     * Total rows count for Listing instance must be set separately.
     * 
     * @param DBALQueryBuilder
     * @param boolean
     * @param boolean
     * @return self
     */
    public function setDBALQuery(DBALQueryBuilder $query, $appendLimit = true, $appendOrder = true)
    {
        if ($appendLimit) {
            $query
                -> setFirstResult($this -> offset())
                -> setMaxResults($this -> limit());
        }
        
        if ($appendOrder && $this -> order()) {
            call_user_func_array([$query, 'orderBy'], (array) $this -> order());
        }
        
        $this -> setItems($query -> execute() -> fetchAll());
        
        return $this;
    }
}
