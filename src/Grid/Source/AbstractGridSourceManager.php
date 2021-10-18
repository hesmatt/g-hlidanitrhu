<?php

namespace Matt\SyGridBundle\Grid\Source;

abstract class AbstractGridSourceManager
{
    protected $source;
    protected ?int $offset = null;
    protected ?int $limit = null;

    public array $columns = [];
    public array $actions = [];
    public ?string $sourceClass = null;

    /**
     * @param int|null $offset
     */
    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param array $columns
     * @return void
     * Allows to manually set the columns in case that they're already cached
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return void
     * Reads the configuration from Symfony config folder
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function readConfigurationForSource(): void
    {
        $cacheManager = new \Matt\SyGridBundle\Grid\Cache\GridCaching();
        $sourcesConfiguration = $cacheManager->cacheConfiguration();

        if (isset($sourcesConfiguration['sources'])) {
            if (isset($sourcesConfiguration['sources'][$this->sourceClass])) {
                $sourceConfiguration = $sourcesConfiguration['sources'][$this->sourceClass];
                if (isset($sourceConfiguration['columns'])) {
                    $this->processConfigColumns($sourceConfiguration['columns']);
                }
                if (isset($sourceConfiguration['actions'])) {
                    //TODO: Udělat processor pro actions
                    $this->actions = $sourceConfiguration['actions'];
                }
            }
        }
    }

    /**
     * @param string $prepend
     * @param string $connector
     * @return string
     * Returns connected names of individual columns with prepend and selected connector
     */
    protected function getColumnNamesAsString(string $prepend = 'qb.', string $connector = ','): string
    {
        $fields = [];
        /**
         * @var $column \Matt\SyGridBundle\Grid\Column\GridColumn
         */
        foreach ($this->columns as $column) {
            if ($column->isReflected() || $column->isReflectedKey()) {
                $fields[] = $prepend . $column->getKey();
            }
        }

        return implode($connector, $fields);
    }

    /**
     * @param array $columns
     * @return void
     * Processor for columns taken from config, so that they're not a string but object of @GridColumn
     */
    private function processConfigColumns(array $columns): void
    {
        if (count($columns) > 0) {
            foreach ($columns as $columnName => $columnSettings) {
                $column = new \Matt\SyGridBundle\Grid\Column\GridColumn();
                if ($columnName !== null) {
                    $column->setKey($columnName);
                }
                if (isset($columnSettings['label'])) {
                    $column->setTitle($columnSettings['label']);
                } elseif ($columnName !== null) {
                    $column->setTitle($columnName);
                }
                if (isset($columnSettings['reflected'])) {
                    $column->setReflected($columnSettings['reflected']);
                }
                if (isset($columnSettings['keyReflected'])) {
                    $column->setReflectedKey($columnSettings['keyReflected']);
                }
                $this->columns[] = $column;
            }
        }
    }
}