<?php

namespace Matt\SyGridBundle\Grid\Source;

use Exception;

class EntityGridSourceManager extends AbstractGridSourceManager
{
    private \Doctrine\ORM\EntityManager $entityManager;
    private array $where = [];
    private array $fields = [];

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $entityClass
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     * Sets the source and assigns values from config or manual settings to it
     */
    public function setSource($entityClass)
    {
        $this->sourceClass = str_replace(':', '\\', $entityClass);
        $source = new $this->sourceClass;
        $this->source = $source;

        $this->readConfigurationForSource();
    }

    //TODO: Add better where, this one does not work at all

    /**
     * @param array $where
     * Allows you to set WHERE for the query
     */
    public function setWhere(array $where): void
    {
        $this->where = $where;
    }

    /**
     * @param array $fields
     * @return void
     * Adds selected fields into query selection
     */
    public function addFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);
    }

    /**
     * @return int
     * Counts all available results, so that there's a proper paging
     */
    public function getCount(): int
    {
        if ($this->search === null && count($this->where) == 0) {
            return count($this->entityManager->getRepository(get_class($this->source))->findAll());
        } else {
            return count($this->getData());
        }
    }

    /**
     * @return array
     * Gets all data via query
     */
    public function getData(): array
    {
        try {
            return $this->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } catch (Exception $ex) {
            dump($ex);
            throw new Exception($ex->getMessage());
        }
    }

    public function getCacheableParams(): array
    {
        $params = parent::getCacheableParams();
        $params['where'] = $this->where;
        $params['fields'] = $this->fields;
        return $params;
    }

    protected function getColumnNamesAsString(string $prepend = 'qb.', string $connector = ','): string
    {
        $fields = [];
        foreach ($this->fields as $field) {
            $fields[] = $prepend . $field;
        }
        if (count($fields) === 0) {
            return parent::getColumnNamesAsString();
        }
        return (parent::getColumnNamesAsString()) . "," . implode($connector, $fields);
    }

    /**
     * @return \Doctrine\ORM\Query
     * Builds and query for entity source
     */
    private function getQuery(): \Doctrine\ORM\Query
    {
        $select = $this->getColumnNamesAsString();
        $queryBuilder = $this->entityManager->createQueryBuilder('qb')
            ->select($select)
            ->from(get_class($this->source), 'qb');

        if ($this->limit !== null && $this->offset !== null) {
            $queryBuilder->setMaxResults($this->limit)
                ->setFirstResult($this->offset);
        }
        if ($this->search != null) {
            /**
             * @var $column \Matt\SyGridBundle\Grid\Column\GridColumn
             */
            foreach ($this->columns as $column) {
                if ($column->isSearchable() && ($column->isReflected() || $column->isReflectedKey())) {
                    $queryBuilder->orWhere("qb.{$column->getKey()} LIKE :search");
                    $queryBuilder->setParameter('search', "%{$this->search}%");
                }
            }
        }
        if (count($this->where) != 0) {
            foreach ($this->where as $where) {
                $queryBuilder->andWhere($where);
            }
        }
        return $queryBuilder->getQuery();
    }
}