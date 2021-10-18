<?php

namespace Matt\SyGridBundle\Grid\Cache;

final class GridCaching
{
    //TODO: This caching class does not work properly so far, only some dummy data are stored here.
    private \Symfony\Component\Cache\Adapter\FilesystemAdapter $cache;

    public function __construct()
    {
        $this->cache = new \Symfony\Component\Cache\Adapter\FilesystemAdapter();
    }

    public function getCachedSourceSettings($source, $settings = null)
    {
        return $this->cacheSourceSettings($source, $settings);
    }

    private function cacheSourceSettings($source, $settings = null)
    {
        return $settings;
        //TODO: Tenhle caching je špatně! Nikdy to nevrati spravnou věc
//        $item = $this->cache->getItem(str_replace(':', '-', $source) . ".columnSettingsCaching");
//
//        if($item !== null)
//        {
//            return $item;
//        }

//        return $this->cache->get(str_replace(':', '-', $source) . ".columnSettingsCaching", function (\Symfony\Contracts\Cache\ItemInterface $item) use ($source, $settings) {
//            dump($settings);
//            return $settings;
//        });
    }

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