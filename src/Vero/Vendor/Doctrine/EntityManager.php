<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine;

use Doctrine\ORM\EntityManager as DoctrineManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Events;
use Doctrine\Common\EventManager;

/**
 * Custom manager.
 */
class EntityManager extends DoctrineManager
{
    /**
     * {@inheritdoc}
     */
    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        if (!$config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }
        
        switch (true) {
            case (is_array($conn)):
                if (!$eventManager) {
                    $eventManager = new EventManager();
                }
                
                if (isset($conn['prefix']) && $conn['prefix']) {
                    $eventManager->addEventListener(
                        Events::loadClassMetadata,
                        new TablePrefix($conn['prefix'])
                    );
                }
                
                $conn = \Doctrine\DBAL\DriverManager::getConnection($conn, $config, $eventManager);
                break;
            case ($conn instanceof Connection):
                if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                     throw ORMException::mismatchedEventManager();
                }
                break;
            default:
                throw new \InvalidArgumentException("Invalid argument: " . $conn);
        }

        return new self($conn, $config, $conn->getEventManager());
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return self
     */
    public function persist($entity)
    {
        parent::persist($entity);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return self
     */
    public function remove($entity)
    {
        parent::remove($entity);
        return $this;
    }
}
