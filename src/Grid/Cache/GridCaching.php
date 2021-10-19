<?php

namespace Matt\SyGridBundle\Grid\Cache;

final class GridCaching
{
    private \Symfony\Component\Cache\Adapter\FilesystemAdapter $cache;

    public function __construct()
    {
        $this->cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter();
    }

    /**
     * @param string $source
     * @param array|null $columns
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * Caches all the columns, their return types, callback and stuff.
     */
    public function cacheColumns(string $source, ?array $columns = null)
    {
        $identifier = \str_replace(':', '-', $source) . ".gridColumnsCaching";
        if ($columns === null) {
            return $this->cache->getItem($identifier)->get();
        } else {
            $this->cache->deleteItem($identifier);
            return $this->cache->get($identifier, function () use ($columns) {
                return $columns;
            });
        }
    }

    /**
     * @param array|null $configuration
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * Caches the initial configuration read from Symfony config files
     */
    public function cacheConfiguration(?array $configuration = null)
    {
        if ($configuration === null) {
            return $this->cache->getItem('gridConfigurationCaching')->get();
        } else {
            $this->cache->deleteItem('gridConfigurationCaching');
            return $this->cache->get('gridConfigurationCaching', function () use ($configuration) {
                return $configuration;
            });
        }
    }

    /**
     * @param string $identifier
     * @param callable|null $dataGetter
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * Caches callable functions as data getters for columns that require it
     */
    public function cacheColumnDataGetter(string $identifier, ?callable $dataGetter = null)
    {
        $identifier = \str_replace(':', '-', $identifier);
        if ($dataGetter === null) {
            return $this->cache->getItem($identifier)->get();
        } else {
            $this->cache->deleteItem($identifier);
            return $this->cache->get($identifier, function (\Symfony\Contracts\Cache\ItemInterface $item) use ($dataGetter) {
                return new \Opis\Closure\SerializableClosure($dataGetter);
            });
        }
    }
}