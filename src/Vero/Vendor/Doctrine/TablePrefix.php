<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/sql-table-prefixes.html
 */

namespace Vero\Vendor\Doctrine;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class TablePrefix
{
    protected $prefix = '';

    public function __construct($prefix)
    {
        $this -> prefix = (string) $prefix;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs -> getClassMetadata();

        /**
         * Problem: when visiting subclass with Single_Table inheritance type,
         * then table name was overwritten in parent class.
         * 
         * Two solutions:
         */
        if ($classMetadata -> parentClasses && $classMetadata -> isInheritanceTypeSingleTable()) {
            return;
        }

        /*if (strpos($classMetadata->getTableName(), $this->prefix) === 0) {
          return;
        }*/

        $classMetadata -> setTableName($this -> prefix . $classMetadata -> getTableName());

        foreach ($classMetadata -> getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY &&
                isset($classMetadata -> associationMappings[$fieldName]['joinTable'])
            ) {
                $mappedTableName = $classMetadata -> associationMappings[$fieldName]['joinTable']['name'];

                // prevent double-prefix in meny-to-many inherited associations
                if (strpos($mappedTableName, $this -> prefix) !== 0) {
                    $classMetadata -> associationMappings[$fieldName]['joinTable']['name'] = $this -> prefix . $mappedTableName;
                }
            }
        }
    }
}
