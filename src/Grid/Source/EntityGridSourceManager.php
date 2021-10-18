<?php

namespace Matt\SyGridBundle\Grid\Source;

class EntityGridSourceManager extends AbstractGridSourceManager
{
    private \Doctrine\ORM\EntityManager $entityManager;

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
     */
    public function setSource($entityClass)
    {
        $this->sourceClass = str_replace(':', '\\', $entityClass);
        $source = new $this->sourceClass;
        $this->source = $source;

        $this->readConfigurationForSource();
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->entityManager->getRepository(get_class($this->source))->findAll());
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->getQuery($this->limit, $this->offset)->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * @return \Doctrine\ORM\Query
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
        return $queryBuilder->getQuery();
    }
}