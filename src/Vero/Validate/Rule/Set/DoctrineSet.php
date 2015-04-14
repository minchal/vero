<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule\Set;

use Doctrine\ORM\EntityManager;

/**
 * Check, if value is valid in doctrine repository.
 */
class DoctrineSet implements ShowableSetInterface
{
    use ShowableSetTrait;
    
    protected $class;
    
    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;
    
    /** @var Doctrine\ORM\ClassMetadata */
    protected $metadata;
    
    protected $getDesc;
    
    /**
     * Provide Doctrine Entity Manager and entity class, in witch to serach for key.
     */
    public function __construct(EntityManager $em, $class, callable $getDesc = null)
    {
        $this -> class = $class;
        $this -> repository = $em -> getRepository($class);
        $this -> metadata = $em -> getClassMetadata($class);
        
        if (!$getDesc) {
            $getDesc = function ($item) {
                return (string) $item;
            };
        }
        
        $this -> getDesc = $getDesc;
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return (boolean) $this -> repository -> find($key);
    }
    
    /**
     * {@inheritdoc}
     */
    public function value($key)
    {
        return $this -> repository -> find($key);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getKey($item)
    {
        if (!$item instanceof $this -> class) {
            return $item;
        }
        
        return implode(',', $this -> metadata -> getIdentifierValues($item));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDesc($item)
    {
        if (!$item instanceof $this -> class) {
            return '';
        }
        
        $getDesc = $this -> getDesc;
        return $getDesc($item);
    }
}
