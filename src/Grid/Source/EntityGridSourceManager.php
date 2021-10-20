<?php

namespace Matt\SyGridBundle\Grid\Source;

class EntityGridSourceManager extends AbstractGridSourceManager
{
    private \Doctrine\ORM\EntityManager $entityManager;
    private array $where = [];

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

    /**
     * @param array $where
     * Allows you to set WHERE for the query
     */
    public function setWhere(array $where): void
    {
        $this->where = $where;
    }

    /**
     * @return int
     * Counts all available results, so that there's a proper paging
     */
    public function getCount(): int
    {
        if ($this->search === null) {
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
        return $this->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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
                if ($column->isSearchable() && ($column->getReflectionKey() !== null)) {
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