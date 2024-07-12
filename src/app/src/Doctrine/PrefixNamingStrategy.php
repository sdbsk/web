<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Exception;

class PrefixNamingStrategy extends UnderscoreNamingStrategy
{
    private string $prefix = '';

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    private function getPrefix(): string
    {
        if (empty($this->prefix)) {
            throw new Exception('Prefix is not set');
        }

        return $this->prefix;
    }

    /**
     * @param string $className
     * @return string
     * @throws Exception
     */
    public function classToTableName(string $className): string
    {
        return $this->getPrefix() . parent::classToTableName($className);
    }

    /**
     * @param string $sourceEntity
     * @param string $targetEntity
     * @param string $propertyName
     * @return string
     * @throws Exception
     */
    public function joinTableName(
        string $sourceEntity,
        string $targetEntity,
        string $propertyName,
    ): string
    {
        return $this->getPrefix() . parent::joinTableName($sourceEntity, $targetEntity, $propertyName);
    }

    /**
     * @param string $entityName
     * @param string|null $referencedColumnName
     * @return string
     * @throws Exception
     */
    public function joinKeyColumnName(
        string      $entityName,
        string|null $referencedColumnName,
    ): string
    {
        return $this->getPrefix() . parent::joinKeyColumnName($entityName, $referencedColumnName);
    }
}
