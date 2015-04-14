<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * Custom QueryBuilder with hints support.
 */
class QueryBuilder extends DoctrineQueryBuilder
{
    /**
     * @var array
     */
    protected $hints = [];
    
    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        $query = parent::getQuery();
        
        foreach ($this -> hints as $name => $value) {
            $query -> setHint($name, $value);
        }
        
        return $query;
    }
    
    /**
     * @return self
     */
    public function setHint($name, $value)
    {
        $this -> hints[$name] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHint($name)
    {
        return isset($this -> hints[$name]) ? $this -> hints[$name] : false;
    }

    /**
     * @return bool
     */
    public function hasHint($name)
    {
        return isset($this -> hints[$name]);
    }

    /**
     * @return array
     */
    public function getHints()
    {
        return $this -> hints;
    }
}
